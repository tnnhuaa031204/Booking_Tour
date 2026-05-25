<?php
// src/Models/Tour.php

class Tour {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Lấy danh sách tour (có phân trang)
    public function getAll(int $limit = 12, int $offset = 0): array {
        $sql = "SELECT TourID, TourCode, TourName, Duration, Description, (SELECT TOP 1 Price FROM TourSchedules WHERE TourID = Tours.TourID) as Price
                FROM Tours 
                WHERE IsActive = 1 
                ORDER BY CreatedAt DESC 
                OFFSET :offset ROWS 
                FETCH NEXT :limit ROWS ONLY";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Lấy tổng số tour
    public function getTotal(): int {
        $sql = "SELECT COUNT(*) as total FROM Tours WHERE IsActive = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    // Lấy chi tiết tour theo ID
    public function getById(int $id): ?array {
        $sql = "SELECT t.*, 
                       (SELECT TOP 1 ImageURL FROM TourImages WHERE TourID = t.TourID AND IsThumbnail = 1) as ThumbnailURL
                FROM Tours t
                WHERE t.TourID = :id AND t.IsActive = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // Lấy lịch khởi hành của tour
    public function getSchedules(int $tourId): array {
        $sql = "SELECT ScheduleID, StartDate, EndDate, Price, AvailableSlots, TotalSlots, Status
                FROM TourSchedules 
                WHERE TourID = :tourId AND StartDate >= GETDATE() 
                ORDER BY StartDate ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Lấy ảnh của tour
    public function getImages(int $tourId): array {
        $sql = "SELECT ImageID, ImageURL, IsThumbnail, SortOrder, Caption
                FROM TourImages 
                WHERE TourID = :tourId 
                ORDER BY SortOrder ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tourId', $tourId, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // ========== HÀM BỔ SUNG ==========
    
    /**
     * Lấy thông tin tour theo Schedule ID
     * Dùng để gửi email xác nhận booking
     */
    public function getByScheduleId(int $scheduleId): ?array {
        $sql = "SELECT t.*, ts.Price, ts.StartDate, ts.EndDate
                FROM Tours t
                JOIN TourSchedules ts ON t.TourID = ts.TourID
                WHERE ts.ScheduleID = :scheduleId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':scheduleId', $scheduleId, \PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Lấy danh sách tour nổi bật (có đánh giá cao)
     */
    public function getFeatured(int $limit = 6): array {
        $sql = "SELECT TOP (:limit) 
                    t.TourID, t.TourCode, t.TourName, t.Duration, t.Description,
                    (SELECT TOP 1 Price FROM TourSchedules WHERE TourID = t.TourID) as Price,
                    (SELECT TOP 1 ImageURL FROM TourImages WHERE TourID = t.TourID AND IsThumbnail = 1) as ThumbnailURL,
                    AVG(r.Rating) as AvgRating,
                    COUNT(r.ReviewID) as TotalReviews
                FROM Tours t
                LEFT JOIN Reviews r ON t.TourID = r.TourID
                WHERE t.IsActive = 1
                GROUP BY t.TourID, t.TourCode, t.TourName, t.Duration, t.Description
                ORDER BY AvgRating DESC, TotalReviews DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Tìm kiếm tour theo tên hoặc điểm đến
     */
    public function search(string $keyword, int $limit = 12, int $offset = 0): array {
        $keyword = '%' . $keyword . '%';
        $sql = "SELECT DISTINCT 
                    t.TourID, t.TourCode, t.TourName, t.Duration, t.Description,
                    (SELECT TOP 1 Price FROM TourSchedules WHERE TourID = t.TourID) as Price,
                    (SELECT TOP 1 ImageURL FROM TourImages WHERE TourID = t.TourID AND IsThumbnail = 1) as ThumbnailURL
                FROM Tours t
                LEFT JOIN TourDestinations td ON t.TourID = td.TourID
                LEFT JOIN Destinations d ON td.DestinationID = d.DestinationID
                WHERE t.IsActive = 1 
                    AND (t.TourName LIKE :keyword OR d.DestinationName LIKE :keyword)
                ORDER BY t.CreatedAt DESC
                OFFSET :offset ROWS
                FETCH NEXT :limit ROWS ONLY";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':keyword', $keyword, \PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // ========== HÀM MỚI ==========
    
    /**
     * Lấy số lượng tour đang hoạt động
     */
    public function getActiveToursCount(): int {
        $sql = "SELECT COUNT(*) as Count FROM Tours WHERE IsActive = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (int)($result['Count'] ?? 0);
    }
    
    // ========== HÀM LỌC TOUR MỚI ==========
    
    /**
     * Tìm kiếm tour với bộ lọc (tên, khu vực, giá, số ngày)
     */
    public function searchWithFilters($keyword = '', $province = '', $minPrice = 0, $maxPrice = 0, $duration = 0, $limit = 12, $offset = 0) {
        $sql = "SELECT DISTINCT t.*, 
                       (SELECT TOP 1 Price FROM TourSchedules WHERE TourID = t.TourID) as Price,
                       (SELECT TOP 1 ImageURL FROM TourImages WHERE TourID = t.TourID AND IsThumbnail = 1) as ThumbnailURL
                FROM Tours t
                LEFT JOIN TourDestinations td ON t.TourID = td.TourID
                LEFT JOIN Destinations d ON td.DestinationID = d.DestinationID
                LEFT JOIN TourSchedules ts ON t.TourID = ts.TourID
                WHERE t.IsActive = 1";
        
        $params = [];
        
        // Lọc theo tên
        if (!empty($keyword)) {
            $sql .= " AND t.TourName LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        
        // Lọc theo khu vực (tỉnh thành)
        if (!empty($province)) {
            $sql .= " AND d.DestinationName LIKE :province";
            $params[':province'] = '%' . $province . '%';
        }
        
        // Lọc theo giá
        if ($minPrice > 0) {
            $sql .= " AND (SELECT TOP 1 Price FROM TourSchedules WHERE TourID = t.TourID) >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }
        if ($maxPrice > 0) {
            $sql .= " AND (SELECT TOP 1 Price FROM TourSchedules WHERE TourID = t.TourID) <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }
        
        // Lọc theo số ngày
        if ($duration > 0) {
            $sql .= " AND t.Duration = :duration";
            $params[':duration'] = $duration;
        }
        
        $sql .= " ORDER BY t.CreatedAt DESC OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
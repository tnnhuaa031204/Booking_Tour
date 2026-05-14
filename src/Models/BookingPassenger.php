<?php
// src/Models/BookingPassenger.php

class BookingPassenger {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    // Thêm danh sách khách đi tour
    public function addPassengers(int $bookingId, array $passengers): bool {
        $sql = "INSERT INTO BookingPassengers (BookingID, FullName, Gender, DateOfBirth, PassengerType) 
                VALUES (:bookingId, :fullname, :gender, :dob, :type)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($passengers as $p) {
            $stmt->execute([
                ':bookingId' => $bookingId,
                ':fullname' => $p['fullname'],
                ':gender' => $p['gender'] ?? null,
                ':dob' => $p['dob'] ?? null,
                ':type' => $p['type']
            ]);
        }
        return true;
    }
    
    // Lấy danh sách khách đi tour theo booking
    public function getByBooking(int $bookingId): array {
        $sql = "SELECT * FROM BookingPassengers WHERE BookingID = :bookingId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':bookingId' => $bookingId]);
        return $stmt->fetchAll();
    }
}
?>
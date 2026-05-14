<?php
// src/Models/Customer.php

class Customer {
    private \PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    public function getAll(): array {
        $sql = "SELECT CustomerID, FullName, Email, Phone, TotalSpent, TotalBookings
                FROM Customers
                ORDER BY FullName ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
?>
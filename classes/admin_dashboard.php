<?php
require_once "db_connection.php"; // Assuming this is the correct path

// Extend the Database class
class AdminDashboard extends Database {
    public function getTotalDonations() {
        $connection = $this->getConnection();  // Use inherited getConnection method
        $query = "SELECT COUNT(*) AS total_donations FROM donations"; 
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total_donations'];
    }

    // Function to get the total number of requests
    public function getTotalRequests() {
        $connection = $this->getConnection();  // Use inherited getConnection method
        $query = "SELECT COUNT(*) AS total_requests FROM donation_requests"; 
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total_requests'];
    }

    // Function to get the total number of users
    public function getTotalUsers() {
        $connection = $this->getConnection();  // Use inherited getConnection method
        $query = "SELECT COUNT(*) AS total_users FROM users"; 
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total_users'];
    }
}

// Example of creating an object and getting data
$adminDashboard = new AdminDashboard();
$totalDonations = $adminDashboard->getTotalDonations();
$totalRequests = $adminDashboard->getTotalRequests();
$totalUsers = $adminDashboard->getTotalUsers();
?>

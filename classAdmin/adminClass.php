<?php
require_once "../classes/db_connection.php";
require_once '../classAdmin/actionHandler.php';


class AdminClass extends Database {
    private $actionHandler;


    // Get the total number of donations
    public function getTotalDonations() {
        $connection = $this->getConnection();  
        $query = "SELECT COUNT(*) AS total_donations FROM donations"; 
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total_donations'];
    }

    // Get the total number of requests
    public function getTotalRequests() {
        $connection = $this->getConnection(); 
        $query = "SELECT COUNT(*) AS total_requests FROM donation_requests"; 
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total_requests'];
    }

    // Get the total number of users
    public function getTotalUsers() {
        $connection = $this->getConnection();  
        $query = "SELECT COUNT(*) AS total_users FROM users"; 
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total_users'];
    }

    // Fetch all donation requests (with user details)
    public function getAllRequests() {
        $connection = $this->getConnection();
        $query = "SELECT r.requestor_id, u.first_name AS requestor_name, u.email AS requestor_email, u.phone AS requestor_phone, 
                    r.delivery_option, r.special_notes, r.donation_id, r.quantity, r.status, r.created_at 
                    FROM donation_requests r
                    JOIN users u ON r.user_id = u.user_id
                    ORDER BY r.created_at DESC"; 
        $result = $connection->query($query);
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        return $requests;
    }

// Update the status of a donation request
public function updateRequestStatus($status, $request_id) {
    $connection = $this->getConnection();
    $query = "UPDATE donation_requests SET status = ? WHERE requestor_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('si', $status, $request_id);
    $stmt->execute();
    return $stmt->affected_rows > 0; 
}

    public function getDonationRequests() {

        $connection = $this->getConnection();
        
        $sql = "
            SELECT 
                dr.donation_id,
                dr.requestor_id,
                CONCAT(u.first_name, ' ', u.last_name) AS requestor_name,
                u.email AS requestor_email,
                dr.requestor_phone,  
                u.address AS requestor_address,
                d.products_type AS donation_product_type,
                d.quantity AS donation_quantity,
                d.products_condition AS donation_condition,
                d.delivery_option AS donation_delivery_option,
                dr.special_notes,
                dr.status
            FROM donation_requests dr
            JOIN users u ON dr.user_id = u.user_id
            JOIN donations d ON dr.donation_id = d.id
            ORDER BY dr.created_at DESC
        ";
    
        $result = $connection->query($sql);
    
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC); 
        } else {
            return []; 
        }
    }
    
    

    public function getDonations() {
        $connection = $this->getConnection();  
        $query = "
            SELECT 
                donations.id AS donor_id,  
                donations.user_id,        
                CONCAT(users.first_name, ' ', users.last_name) AS name,
                users.email,
                users.address,
                donations.phone,
                donations.products_type,
                donations.quantity,
                donations.products_condition,
                donations.delivery_option,
                donations.message,
                donations.status
            FROM donations
            JOIN users ON donations.user_id = users.user_id
        ";
        $stmt = $connection->prepare($query);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $donations = [];
    
        // Fetch rows as an associative array
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
    
        return $donations;
    }
    
    public function getUsers() {
        $connection = $this->getConnection();
        $query = "
            SELECT 
                user_id,
                first_name,
                last_name,
                email,
                phone,
                address,
                created_at
            FROM users
        ";
        $stmt = $connection->prepare($query);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $users = [];
    
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    
        return $users;
    }
    

// Fetch donations by a specific user
public function getUserDonations($user_id) {
    $connection = $this->getConnection();
    $query = "
        SELECT 
            id AS donation_id, 
            products_type, 
            quantity, 
            products_condition, 
            delivery_option, 
            created_at, 
            CASE 
                WHEN quantity = 0 THEN 'Out of Stock'
                WHEN status IS NULL OR status = '' THEN 'Pending'
                ELSE status 
            END AS status
        FROM donations 
        WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Fetch requests by a specific user
public function getUserRequests($user_id) {
    $connection = $this->getConnection();
    $query = "
        SELECT 
            requestor_id, 
            products_type, 
            quantity, 
            special_notes, 
            delivery_option, 
            created_at, 
            status 
        FROM donation_requests 
        WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}




public function getUserById($user_id) {
    $connection = $this->getConnection();
    $query = "
        SELECT user_id, first_name, last_name, email, phone, address, created_at 
        FROM users
        WHERE user_id = ?
    ";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}


public function updateUser($user_id, $first_name, $last_name, $email, $phone, $address) {
    $connection = $this->getConnection();
    $query = "
        UPDATE users
        SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?
        WHERE user_id = ?
    ";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $address, $user_id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}


public function deleteUser($user_id) {
    $connection = $this->getConnection();

    // Start a transaction
    $connection->begin_transaction();

    try {
        // First, check if the user has any associated donations
        $checkQuery = "SELECT COUNT(*) as count FROM donations WHERE user_id = ?";
        $checkStmt = $connection->prepare($checkQuery);
        $checkStmt->bind_param("i", $user_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            // User has donations, we can't delete
            throw new Exception("User has associated donations and cannot be deleted.");
        }

        // Check if the user has any associated requests
        $checkRequestsQuery = "SELECT COUNT(*) as count FROM donation_requests WHERE user_id = ?";
        $checkRequestsStmt = $connection->prepare($checkRequestsQuery);
        $checkRequestsStmt->bind_param("i", $user_id);
        $checkRequestsStmt->execute();
        $requestsResult = $checkRequestsStmt->get_result();
        $requestsRow = $requestsResult->fetch_assoc();
        
        if ($requestsRow['count'] > 0) {
            // User has requests, we can't delete
            throw new Exception("User has associated donation requests and cannot be deleted.");
        }

        // If we've made it here, the user has no donations or requests, so we can delete
        $deleteQuery = "DELETE FROM users WHERE user_id = ?";
        $deleteStmt = $connection->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $user_id);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows == 0) {
            throw new Exception("No user found with the given ID.");
        }

        // If successful, commit the transaction
        $connection->commit();
        return true;
    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        $connection->rollback();
        // Re-throw the exception with the error message
        throw new Exception($e->getMessage());
    }
}


// for emergency request method



    public function getTotalEmergencyRequests() {
        $connection = $this->getConnection();  
        $query = "SELECT COUNT(*) AS total_emergency_requests FROM emergency_requests"; 
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total_emergency_requests'];
    }

}


  // creating an object and getting data
  $adminDashboard = new AdminClass();
  $totalDonations = $adminDashboard->getTotalDonations();
  $totalRequests = $adminDashboard->getTotalRequests();
  $totalUsers = $adminDashboard->getTotalUsers();

?>


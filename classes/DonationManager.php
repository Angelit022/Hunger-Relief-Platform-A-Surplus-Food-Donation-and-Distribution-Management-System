<?php 
class DonationManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        
    }

    public function addDonation($userId, $phone, $foodType, $quantity, $condition, $pickupOption, $message) {
 
        $userQuery = "SELECT first_name, last_name, email, address FROM users WHERE user_id = ?";
        $userStmt = $this->conn->prepare($userQuery);
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $userData = $userResult->fetch_assoc();
        $userStmt->close();

     
        $sql = "INSERT INTO donations (user_id, phone, products_type, quantity, products_condition, delivery_option, message, name, email, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $fullName = $userData['first_name'] . ' ' . $userData['last_name'];
        $stmt->bind_param("ississssss", 
            $userId, 
            $phone, 
            $foodType, 
            $quantity, 
            $condition, 
            $pickupOption, 
            $message,
            $fullName,
            $userData['email'],
            $userData['address']
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }


    // Fetch all donations from the database with user information
    public function getDonations() {
        $sql = "SELECT d.id, 
                       CONCAT(u.first_name, ' ', u.last_name) as name,
                       u.email,
                       u.address,
                       d.products_type,
                       d.quantity,
                       d.products_condition,
                       d.delivery_option,
                       d.status
                FROM donations d
                JOIN users u ON d.user_id = u.user_id";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch a single donation by ID with user information
    public function getDonationsByUserId($userId) {
        $sql = "SELECT d.id, 
                       d.products_type,
                       d.quantity,
                       d.products_condition,
                       d.delivery_option,
                       d.message,
                       d.status,
                       d.created_at
                FROM donations d
                WHERE d.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getDonationById($id) {
        $sql = "SELECT d.id, 
                       d.user_id,
                       d.phone,
                       d.products_type,
                       d.quantity,
                       d.products_condition,
                       d.delivery_option,
                       d.message,
                       d.status,
                       d.created_at
                FROM donations d
                WHERE d.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update a donation (only updating donation-specific fields)
    public function updateDonation($id, $phone, $productsType, $quantity, $productsCondition, $deliveryOption, $message) {
        $sql = "UPDATE donations SET phone = ?, products_type = ?, quantity = ?, products_condition = ?, delivery_option = ?, message = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssissss", 
            $phone, 
            $productsType, 
            $quantity, 
            $productsCondition, 
            $deliveryOption, 
            $message, 
            $id
        );
        return $stmt->execute();
    }
    

    // Delete a donation by ID
    public function deleteDonation($donationId) {
        $query = "DELETE FROM donations WHERE id = ?";
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $donationId);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
    
        return false;
    }
    

    public function getLatestDonationByProductType($productType) {
        $sql = "SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) AS donor_name, u.address AS donor_address 
                FROM donations d 
                JOIN users u ON d.user_id = u.user_id 
                WHERE d.products_type = ? 
                ORDER BY d.created_at DESC 
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $productType);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function searchDonations($searchQuery) {
        $sql = "SELECT d.products_type, 
                   SUM(d.quantity) AS total_quantity,
                   MAX(d.id) AS latest_donation_id
            FROM donations d
            WHERE d.products_type LIKE ?
            GROUP BY d.products_type";
        $stmt = $this->conn->prepare($sql);
        $searchTerm = "%$searchQuery%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $donations = [];
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }

        return $donations;
    }

    public function getLatestDonationById($id) {
        $sql = "SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) AS donor_name, u.address AS donor_address 
            FROM donations d 
            JOIN users u ON d.user_id = u.user_id 
            WHERE d.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAcceptedDonations() {
        $sql = "SELECT d.id, 
                       CONCAT(u.first_name, ' ', u.last_name) as name,
                       u.email, 
                       u.address,
                       d.products_type,
                       d.quantity,
                       d.products_condition,
                       d.delivery_option,
                       d.status
                FROM donations d
                JOIN users u ON d.user_id = u.user_id
                WHERE d.status = 'Accepted'";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function isDonationOwner($donationId, $userId) {
        if (!$this->conn) {
            return false;
        }

        $sql = "SELECT COUNT(*) as count FROM donations WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ii", $donationId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['count'] > 0;
    }

    
}
?>

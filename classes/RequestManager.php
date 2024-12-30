<<<<<<< HEAD
<?php
class RequestManager {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRequest(
        $userId,
        $donationId,
        $requestorName,
        $requestorEmail,
        $requestorPhone,
        $deliveryOption,
        $specialNotes,
        $quantity,
        $productsType,
        $productsCondition,
        $requestorAddress 
    ) {
        // Step 1: Validate the donation ID
        $queryCheckDonation = "SELECT COUNT(*) AS count FROM donations WHERE id = ?";
        if ($stmtCheckDonation = $this->conn->prepare($queryCheckDonation)) {
            $stmtCheckDonation->bind_param("i", $donationId);
            $stmtCheckDonation->execute();
            $result = $stmtCheckDonation->get_result();
            $row = $result->fetch_assoc();
            $stmtCheckDonation->close();
    
            if ($row['count'] == 0) {
                return false; 
            }
        }
    
        // Step 2: Check if the request already exists
        $queryCheckRequest = "SELECT COUNT(*) AS count FROM donation_requests WHERE donation_id = ? AND requestor_email = ?";
        if ($stmtCheckRequest = $this->conn->prepare($queryCheckRequest)) {
            $stmtCheckRequest->bind_param("is", $donationId, $requestorEmail);
            $stmtCheckRequest->execute();
            $result = $stmtCheckRequest->get_result();
            $row = $result->fetch_assoc();
            $stmtCheckRequest->close();
    
            if ($row['count'] > 0) {
                return false; 
            }
        }
    
        // Step 3: Insert the new donation request
        $query = "INSERT INTO donation_requests (
                user_id, 
                donation_id, 
                requestor_name, 
                requestor_email, 
                requestor_phone, 
                delivery_option, 
                special_notes, 
                quantity, 
                products_type, 
                products_condition, 
                requestor_address,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param(
                "iisssssisss",
                $userId,
                $donationId,
                $requestorName,
                $requestorEmail,
                $requestorPhone,
                $deliveryOption,
                $specialNotes,
                $quantity,
                $productsType,
                $productsCondition,
                $requestorAddress
            );
            
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        
    
        return false; 
    }

    public function getRequest($id) {
        $query = "SELECT * FROM donation_requests WHERE requestor_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    

    public function updateRequest($id, $phone, $delivery_option, $notes, $address, $quantity, $previousQuantity) {
        $this->conn->begin_transaction();

        try {
            // Update the request
            $query = "
                UPDATE donation_requests 
                SET requestor_phone = ?, 
                    delivery_option = ?, 
                    special_notes = ?, 
                    requestor_address = ?, 
                    quantity = ?
                WHERE requestor_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssis", $phone, $delivery_option, $notes, $address, $quantity, $id);
            $stmt->execute();

            // Update the donation quantity
            $quantityDifference = $quantity - $previousQuantity;
            $updateDonationQuery = "
                UPDATE donations d
                JOIN donation_requests dr ON d.id = dr.donation_id
                SET d.quantity = d.quantity - ?
                WHERE dr.requestor_id = ?";

            $updateDonationStmt = $this->conn->prepare($updateDonationQuery);
            $updateDonationStmt->bind_param("ii", $quantityDifference, $id);
            $updateDonationStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    

    public function updateDonationQuantity($donationId, $deductedQuantity) {
        $this->conn->begin_transaction();

        try {
            // Update the quantity
            $query = "UPDATE donations SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $deductedQuantity, $donationId);
            $stmt->execute();

            // Check if the quantity is now zero and update the status
            $checkQuery = "SELECT quantity FROM donations WHERE id = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bind_param("i", $donationId);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['quantity'] == 0) {
                $updateStatusQuery = "UPDATE donations SET status = 'Out of Stock' WHERE id = ?";
                $updateStatusStmt = $this->conn->prepare($updateStatusQuery);
                $updateStatusStmt->bind_param("i", $donationId);
                $updateStatusStmt->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getRequestQuantities() {
        $query = "SELECT requestor_id, quantity FROM donation_requests";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getAllRequests() {
                $query = "SELECT 
                dr.requestor_id AS request_id, 
                dr.requestor_name,
                dr.requestor_email,
                dr.requestor_phone,
                dr.quantity AS requested_quantity,
                dr.delivery_option,
                dr.special_notes,
                dr.status AS request_status,
                d.products_type,
                d.products_condition,
                d.quantity AS available_quantity,
                d.delivery_option AS donor_delivery_option,
                CONCAT(u.first_name, ' ', u.last_name) AS donor_name,
                u.email AS donor_email,
                u.address AS donor_address
            FROM donation_requests dr
            JOIN donations d ON dr.donation_id = d.id
            JOIN users u ON d.user_id = u.user_id
            ORDER BY dr.created_at DESC";

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get a single request
    public function getRequestById($requestId) {
        $query = "SELECT 
                    dr.requestor_id AS request_id, 
                    dr.requestor_phone, 
                    dr.requestor_address,
                    dr.quantity AS requested_quantity,
                    dr.delivery_option,
                    dr.special_notes,
                    dr.status AS request_status,
                    d.id AS donation_id,
                    d.products_type,
                    d.products_condition,
                    d.quantity AS available_quantity,
                    d.delivery_option AS donor_delivery_option,
                    CONCAT(u.first_name, ' ', u.last_name) AS donor_name,
                    u.email AS donor_email,
                    u.address AS donor_address
                  FROM donation_requests dr
                  JOIN donations d ON dr.donation_id = d.id
                  JOIN users u ON d.user_id = u.user_id
                  WHERE dr.requestor_id = ?"; 
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_assoc();
        }
        return null;
    }
    

    // Delete a request
    public function deleteRequest($requestId) {
        $query = "DELETE FROM donation_requests WHERE requestor_id = ?"; 
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $requestId);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
    
        return false;
    }
    

    //for search 
    public function getLatestDonationByProductType($productType) {
        $sql = "SELECT d.id, 
                       d.products_type, 
                       d.quantity, 
                       d.delivery_option, 
                       CONCAT(u.first_name, ' ', u.last_name) AS donor_name, 
                       u.address AS donor_address
                FROM donations d
                JOIN users u ON d.user_id = u.user_id
                WHERE d.products_type = ?
                ORDER BY d.created_at DESC LIMIT 1";  
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $productType);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    

    
}
?>


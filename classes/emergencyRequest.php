<?php 

require_once __DIR__ . "/../classes/db_connection.php";

class EmergencyRequest {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function saveRequestToDatabase($userId, $latitude, $longitude, $type, $details) {
        try {
            $connection = $this->db->getConnection();
            $query = "INSERT INTO emergency_requests (user_id, type, details, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, 'pending')";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("issdd", $userId, $type, $details, $latitude, $longitude);
            
            if ($stmt->execute()) {
                return $connection->insert_id;
            } else {
                error_log("Database error: " . $stmt->error);
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in saveRequestToDatabase: " . $e->getMessage());
            throw $e;
        }
    }

    public function getEmergencyRequests() {
        $connection = $this->db->getConnection();
        $query = "
            SELECT 
                er.id,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                u.email,
                er.latitude,
                er.longitude,
                er.created_at,
                er.status
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.user_id
            ORDER BY er.created_at DESC
        ";
        
        $result = $connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getConnection() {
        return $this->db->getConnection();
    }
}    
?>
<?php 
require_once __DIR__ . "/../classes/db_connection.php";

class EmergencyRequest {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }
    //saving emergency request
    public function saveRequestToDatabase($userId, $latitude, $longitude) {
        try {
            $connection = $this->db->getConnection();
            $query = "INSERT INTO emergency_requests (user_id, latitude, longitude, status, name, email, phone) 
                      SELECT ?, ?, ?, 'pending', CONCAT(first_name, ' ', last_name), email, phone 
                      FROM users WHERE user_id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("iddi", $userId, $latitude, $longitude, $userId);
        
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
                er.name AS user_name,
                er.email,
                er.phone,
                er.latitude,
                er.longitude,
                er.created_at,
                er.status
            FROM emergency_requests er
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


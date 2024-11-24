<?php

class RequestManager {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all requests from the database
    public function getRequests() {
        $query = "SELECT * FROM requests ORDER BY created_at DESC";
        $result = $this->conn->query($query);

        $requests = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
        }

        return $requests;
    }

    // Add a new request to the database
    public function addRequest($name, $email, $location, $requested_items, $quantity, $item_condition, $urgency, $notes) {
        $query = "INSERT INTO requests (name, email, location, requested_items, quantity, item_condition, urgency, notes, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("ssssssss", 
                $name, 
                $email, 
                $location, 
                $requested_items, 
                $quantity, 
                $item_condition, 
                $urgency, 
                $notes
            );

            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        
        return false;
    }

    // Update an existing request
    public function updateRequest($id, $name, $email, $location, $requested_items, $quantity, $item_condition, $urgency, $notes, $status) {
        $query = "UPDATE requests 
                  SET name = ?, 
                      email = ?, 
                      location = ?, 
                      requested_items = ?, 
                      quantity = ?, 
                      item_condition = ?, 
                      urgency = ?, 
                      notes = ?,
                      status = ?
                  WHERE id = ?";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("sssssssssi", 
                $name, 
                $email, 
                $location, 
                $requested_items, 
                $quantity, 
                $item_condition, 
                $urgency, 
                $notes,
                $status,
                $id
            );

            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        
        return false;
    }

    // Delete a request
    public function deleteRequest($id) {
        $query = "DELETE FROM requests WHERE id = ?";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }

        return false;
    }

    // Get a single request by ID
    public function getRequestById($id) {
        $query = "SELECT * FROM requests WHERE id = ?";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_assoc();
        }

        return null;
    }

    // Update request status
    public function updateStatus($id, $status) {
        $query = "UPDATE requests SET status = ? WHERE id = ?";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("si", $status, $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }

        return false;
    }

    // Get requests by status
    public function getRequestsByStatus($status) {
        $query = "SELECT * FROM requests WHERE status = ? ORDER BY created_at DESC";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $requests = [];
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
            
            $stmt->close();
            return $requests;
        }

        return [];
    }

    // Get request statistics
    public function getRequestStats() {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        // Query to get request count by status
        $query = "SELECT status, COUNT(*) as count FROM requests GROUP BY status";
        $result = $this->conn->query($query);

        // Check if query executed successfully
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Ensure status is in lowercase for consistency
                $status = strtolower($row['status'] ?? 'pending');
                // Update corresponding status count
                if (array_key_exists($status, $stats)) {
                    $stats[$status] = $row['count'];
                }
            }
        }

        // Query to get the total number of requests
        $queryTotal = "SELECT COUNT(*) as count FROM requests";
        $resultTotal = $this->conn->query($queryTotal);
        if ($resultTotal) {
            $row = $resultTotal->fetch_assoc();
            $stats['total'] = $row['count'];
        }

        return $stats;
    }
}
?>

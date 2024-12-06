<?php

class ActionHandler {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function approveRecord($id, $table) {
        return $this->updateStatus($id, 'Accepted', $table);
    }


    public function rejectRecord($id, $table) {
        return $this->updateStatus($id, 'Rejected', $table);
    }

    public function fulfillRecord($id, $table) {
        return $this->updateStatus($id, 'Fulfilled', $table);
    }
    
    public function editRecord($recordId) {
        header("Location: edit_record.php?id=$recordId");
        exit;
    }

    // Delete a record
    public function deleteRecord($recordId) {
        $query = "DELETE FROM records WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $recordId);
        return $stmt->execute();
    }
    // public function updateStatus($id, $status, $table) {
    //     $query = "UPDATE $table SET status = ? WHERE " . ($table === 'donations' ? 'id' : 'requestor_id') . " = ?";
    //     $stmt = $this->db->prepare($query);
    //     if (!$stmt) {
    //         error_log("Prepare failed: " . $this->db->error);
    //         return false;
    //     }
    //     $stmt->bind_param("si", $status, $id);
    //     $result = $stmt->execute();
    //     if (!$result) {
    //         error_log("Execute failed: " . $stmt->error);
    //     }
    //     return $result;
    // }

    private function updateStatus($id, $status, $table) {
        $query = "UPDATE $table SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("si", $status, $id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
        }
        return $result;
    }
}
?>

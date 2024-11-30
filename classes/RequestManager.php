        <?php
        class RequestManager {
            private $conn;

            public function __construct($db) {
                $this->conn = $db;
            }

            public function addRequest($name, $email, $phone, $deliveryOption, $specialNotes, $donationId) {
                // Check if the donation_id already exists for the current requestor (to avoid duplicate requests by the same user)
                $queryCheck = "SELECT COUNT(*) AS count FROM donation_requests WHERE donation_id = ? AND requestor_email = ?";
                
                // Prepare and execute the query
                if ($stmtCheck = $this->conn->prepare($queryCheck)) {
                    $stmtCheck->bind_param("is", $donationId, $email);  // Bind donation_id and requestor_email to ensure uniqueness
                    $stmtCheck->execute();
                    
                    // Get the result and fetch the count directly
                    $result = $stmtCheck->get_result();
                    $row = $result->fetch_assoc();
                    
                    $stmtCheck->close(); // Close the prepared statement
                    
                    // If the count is greater than 0, it means the user has already made a request for this donation
                    if ($row['count'] > 0) {
                        return false; // Prevent duplicate request
                    }
                }
                
                // Proceed to insert the request if it's valid
                $query = "INSERT INTO donation_requests (
                            requestor_name, 
                            requestor_email, 
                            requestor_phone, 
                            delivery_option, 
                            special_notes, 
                            donation_id, 
                            status
                        ) 
                        VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
                
                // Prepare the insert statement
                if ($stmt = $this->conn->prepare($query)) {
                    $stmt->bind_param(
                        "sssssi", 
                        $name, 
                        $email, 
                        $phone, 
                        $deliveryOption, 
                        $specialNotes, 
                        $donationId
                    );
                
                    // Execute the statement
                    $success = $stmt->execute();
                    $stmt->close();
                    return $success;
                }
                
                return false;
            }
            
            // Update an existing request
            public function updateRequest($requestorId, $name, $email, $phone, $deliveryOption, $specialNotes, $status) {
                $query = "UPDATE donation_requests 
                        SET requestor_name = ?, 
                            requestor_email = ?, 
                            requestor_phone = ?, 
                            delivery_option = ?, 
                            special_notes = ?, 
                            status = ?, 
                            updated_at = CURRENT_TIMESTAMP
                        WHERE requestor_id = ?";
                
                if ($stmt = $this->conn->prepare($query)) {
                    // Correct the bind_param to match the query
                    $stmt->bind_param("ssssssi", 
                        $name, 
                        $email, 
                        $phone, 
                        $deliveryOption, 
                        $specialNotes, 
                        $status, 
                        $requestorId  // Make sure the integer requestorId is passed last
                    );
                    
                    $success = $stmt->execute();
                    $stmt->close();
                    return $success;
                }
                
                return false;
            }
                
            

            // Delete a request
            public function deleteRequest($requestorId) {
                $query = "DELETE FROM donation_requests WHERE requestor_id = ?";
                
                if ($stmt = $this->conn->prepare($query)) {
                    $stmt->bind_param("i", $requestorId);
                    $success = $stmt->execute();
                    $stmt->close();
                    return $success;
                }

                return false;
            }

            // Get a single request by ID
            public function getRequestById($requestorId) {
                $query = "SELECT * FROM donation_requests WHERE requestor_id = ?";
                
                if ($stmt = $this->conn->prepare($query)) {
                    $stmt->bind_param("i", $requestorId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc();
                }

                return null;
            }



            // Get requests by status
            public function getRequestsByStatus($status) {
                $query = "SELECT * FROM donation_requests WHERE status = ? ORDER BY created_at DESC";
                
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


        }
        ?>

<?php
require_once "db_connection.php";

class UserService extends Database {

    public function registerUser($first_name, $last_name, $email, $phone, $address, $password) {
        $connection = $this->getConnection();
        $password = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');

        $stmt = $connection->prepare("INSERT INTO users (first_name, last_name, email, phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $first_name, $last_name, $email, $phone, $address, $password, $created_at);
        
        $stmt->execute();
        $user_id = $stmt->insert_id;
        $stmt->close();

        return $user_id;
    }

    public function loginUser($email, $password) {
        $connection = $this->getConnection();

        $stmt = $connection->prepare("SELECT user_id, first_name, last_name, email, phone, address, password, created_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $first_name, $last_name, $email, $phone, $address, $stored_password, $created_at);

        if ($stmt->fetch() && password_verify($password, $stored_password)) {
            $stmt->close();
            return [
                "user_id" => $user_id,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "phone" => $phone,
                "address" => $address,
                "created_at" => $created_at
            ];
        } else {
            $stmt->close();
            return null;
        }
    }
}
?>

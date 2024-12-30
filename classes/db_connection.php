<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "hunger_relief_db";
    private $connection;

    public function getConnection() {
        if ($this->connection === null) {           // Check if the connection is not already established
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);         // Create a new MySQLi connection
            if ($this->connection->connect_error) {
                die("Error: Failed to connect to MySQL - " . $this->connection->connect_error);
            }
        }
        return $this->connection;       // Return the active database connection
    }

    public function closeConnection() {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}
?>

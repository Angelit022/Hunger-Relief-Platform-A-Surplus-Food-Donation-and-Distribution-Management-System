<?php 
class DonationManager {
    private $conn;

    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fetch all donations from the database
    public function getDonations() {
        $sql = "SELECT * FROM donations";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch a single donation by ID
    public function getDonationById($id) {
        $sql = "SELECT * FROM donations WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    // Update a donation
    public function updateDonation($id, $name, $email, $address, $phone, $productsType, $quantity, $productsCondition, $deliveryOption, $message) {
        $sql = "UPDATE donations SET name = ?, email = ?, address = ?, phone = ?, products_type = ?, quantity = ?, products_condition = ?, delivery_option = ?, message = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssi",
            $name,
            $email,
            $address,
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
    public function deleteDonation($id) {
        $sql = "DELETE FROM donations WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ito ay para sa SEARCH 
     public function searchDonations($searchQuery)
    {
        $sql = "SELECT products_type, SUM(quantity) AS total_quantity 
                FROM donations 
                WHERE products_type LIKE ? 
                GROUP BY products_type";
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
}
?>

}
?>
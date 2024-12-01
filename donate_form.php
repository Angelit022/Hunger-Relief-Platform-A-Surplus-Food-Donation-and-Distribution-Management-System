<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";


// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}


$donationSuccess = false; // Flag for SweetAlert success


// Parent Class to handle donor information
class Donation {
    public $name;
    public $email;
    public $address;
    public $phone;
    public $productsType;
    public $quantity;
    public $productsCondition;
    public $deliveryOption;
    public $message;


    // Constructor to initialize the values
    public function __construct($name, $email, $address, $phone, $productsType, $quantity, $productsCondition, $deliveryOption, $message) {
        $this->name = htmlspecialchars($name);
        $this->email = htmlspecialchars($email);
        $this->address = htmlspecialchars($address);
        $this->phone = htmlspecialchars($phone);
        $this->productsType = htmlspecialchars($productsType);
        $this->quantity = htmlspecialchars($quantity);
        $this->productsCondition = htmlspecialchars($productsCondition);
        $this->deliveryOption = htmlspecialchars($deliveryOption);
        $this->message = htmlspecialchars($message);
    }


    // Function to save the donation data to the database
    public function saveToDatabase($conn) {
        $sql = "INSERT INTO donations (name, email, address, phone, products_type, quantity, products_condition, delivery_option, message)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssss",
            $this->name,
            $this->email,
            $this->address,
            $this->phone,
            $this->productsType,
            $this->quantity,
            $this->productsCondition,
            $this->deliveryOption,
            $this->message
        );
        return $stmt->execute();
    }
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productsCondition = isset($_POST['condition']) ? implode(", ", $_POST['condition']) : 'No conditions specified';
    $donation = new Donation(
        $_POST['name'],
        $_POST['email'],
        $_POST['address'],
        $_POST['phone'],
        $_POST['ProductsType'],
        $_POST['quantity'],
        $productsCondition,
        $_POST['delivery_option'],
        $_POST['message']
    );


    // Create database connection
    $database = new Database();
    $conn = $database->getConnection();


    // Save donation data to the database
    if ($donation->saveToDatabase($conn)) {
        $donationSuccess = true; // Set success flag
    } else {
        echo "<p>Error: Could not save your donation details. Please try again later.</p>";
    }


    // Close the database connection
    $database->closeConnection();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Form</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>


<!-- Donation Form -->
<div class="donation-form-container">
    <div class="donor-info">
        <h2>Donation Form</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" id="address" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="ProductsType">Products Type</label>
                <select name="ProductsType" id="productsType" class="form-control" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Canned Goods">Canned Goods</option>
                    <option value="Fresh Produce">Fresh Produce</option>
                    <option value="Packaged Meals">Packaged Meals</option>
                    <option value="Household Care">Household Care</option>
                    <option value="Personal Care">Personal Care</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Products Condition</label><br>
                <input type="checkbox" name="condition[]" value="Unopened"> Unopened
                <input type="checkbox" name="condition[]" value="Properly Packaged"> Properly Packaged
                <input type="checkbox" name="condition[]" value="Within Expiry Date"> Within Expiry Date
            </div>
            <div class="form-group">
                <label>Delivery Option</label><br>
                <input type="radio" name="delivery_option" value="Pick-up" required> Pick-up
                <input type="radio" name="delivery_option" value="Drop-off" required> Drop-off
            </div>
            <div class="form-group">
                <label for="message">Message (Optional)</label>
                <textarea name="message" id="message" class="form-control" rows="4"></textarea>
            </div>


            <!-- Button container for Cancel and Submit -->
            <div class="form-buttons">
                <a href="index.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit</button>
            </div>
        </form>
    </div>
</div>




<!-- SweetAlert2 Script for Success Message -->
<script>
    <?php if ($donationSuccess): ?>
        Swal.fire({
            title: 'Thank You!',
            text: 'Check it out! Your donation has been successfully recorded in Donor List',
            confirmButtonText: 'Go to Dashboard',
            imageUrl: 'images/thankyou.jpg',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Donation Image',
            willClose: () => {
                window.location.href = 'dashboard.php';
            }
        });
    <?php endif; ?>
</script>


</body>
</html>


<?php
require_once "layout/footer.php";
?>



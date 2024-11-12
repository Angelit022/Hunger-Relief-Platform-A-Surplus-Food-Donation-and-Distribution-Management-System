<?php
require_once "layout/header.php";
require_once "db_connection.php";

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("location: login.php");
    exit;
}

$donationSuccess = false; // Flag to track donation success

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $phone = htmlspecialchars($_POST['phone']);
    $foodType = htmlspecialchars($_POST['foodType']);
    $quantity = (int)$_POST['quantity'];
    $condition = isset($_POST['condition']) ? implode(", ", $_POST['condition']) : "";
    $pickupOption = htmlspecialchars($_POST['pickup']);
    $message = htmlspecialchars($_POST['message']);

    // Create a new instance of the Database class
    $database = new Database();
    $conn = $database->getConnection();

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO donations (name, email, address, phone, food_type, quantity, condition, pickup_option, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $name, $email, $address, $phone, $foodType, $quantity, $condition, $pickupOption, $message);

    // Execute and check the result
    if ($stmt->execute()) {
        $donationSuccess = true; // Set the flag if donation is successful
    } else {
        echo "<p>Error: Could not save your donation details. Please try again later.</p>";
    }

    // Close the statement and connection
    $stmt->close();
    $database->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Food to Help Those in Need</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> 
    <!-- Include SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- Donation Form Section -->
<div class="donation-form-container">
    <div class="donor-info">
        <h2>Donate Food to Help Those in Need</h2>
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
                <label for="foodType">Food Type</label>
                <select name="foodType" id="foodType" class="form-control" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Canned Goods">Canned Goods</option>
                    <option value="Fresh Produce">Fresh Produce</option>
                    <option value="Packaged Meals">Packaged Meals</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required>
            </div>
            <div class="form-group" >
                <label>Condition of Food</label><br>
                <input type="checkbox" name="condition[]" value="Unopened"> Unopened
                <input type="checkbox" name="condition[]" value="Properly Packaged"> Properly Packaged
                <input type="checkbox" name="condition[]" value="Within Expiry Date"> Within Expiry Date
            </div>
            <div class="form-group" >
                <label>Pick-up or Drop-off Option</label><br>
                <input type="radio" name="pickup" value="Pick-up" required> Pick-up
                <input type="radio" name="pickup" value="Drop-off" required> Drop-off
            </div>
            <div class="form-group">
                <label for="message">Message (Optional)</label>
                <textarea name="message" id="message" class="form-control" rows="4"></textarea>
            </div>
            <button type="submit" class="btn-submit">Submit Donation</button>
        </form>
    </div>
</div>

<!-- SweetAlert2 Script for Success Message -->
<script>
    <?php if ($donationSuccess): ?>
        Swal.fire({
            title: 'Thank You!',
            text: 'Check it out! Your donation has been successfully recorded in Donor List',
            confirmButtonText: 'Go to Homepage',
            imageUrl: 'images/thankyou.jpg',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Donation Image',
            willClose: () => {
                window.location.href = 'index.php'; // Redirect to homepage after closing the alert
            }
        });
    <?php endif; ?>
</script>

</body>
</html>

<?php
require_once "layout/footer.php";
?>
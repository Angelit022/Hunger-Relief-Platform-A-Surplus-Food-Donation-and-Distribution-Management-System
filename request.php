<?php
require_once 'db_connection.php'; 


$db = new Database();
$conn = $db->getConnection();

$query = "SELECT * FROM donors";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $food_type = $_POST['food_type'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO requests (name, email, address, phone_number, food_type, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $address, $phone_number, $food_type, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Request submitted successfully!');</script>";
    } else {
        echo "<script>alert('Error submitting request.');</script>";
    }

    $stmt->close(); 
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link rel="stylesheet" href="request.css">
</head>
<body>
    <div class="contact-container">
        <div class="left-panel">
            <h1>Request Form</h1>
            <p>
                "In every act of giving, we find a piece of ourselves. When we donate, we are not just offering material support; we are sharing hope, kindness, and love. Each contribution, no matter how small, creates ripples of change that can transform lives and communities. Remember, the true measure of our wealth is not in what we keep, but in what we give away. By donating, we become part of something greater than ourselves, fostering a world where compassion and generosity light the way. Let your heart guide you to give, and watch as the world becomes a brighter place because of your kindness."
            </p>
        </div>
        <div class="right-panel">
            <!-- Contact Form -->
            <form class="contact-form" method="POST" action="index.php">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="food">Food Type *</label>
                <input type="text" id="food" name="food" placeholder="Food Type" required>

                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" placeholder="Type your message"></textarea>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
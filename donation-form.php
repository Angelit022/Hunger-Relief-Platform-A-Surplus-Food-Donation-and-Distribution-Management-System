<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Form</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
        }
        .navbar {
            background-color: #f8f9fa;
            padding: 0.5rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: flex-end;
        }
        .navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .navbar ul li {
            margin-right: 1rem;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #000;
        }
        .donation-form-container {
            display: flex;
            width: 70%;
            height: 70%;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .donor-image {
            width: 40%;
            background-color: #ececec;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            position: relative;
        }
        .donor-image img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
            padding: 5px;
        
        }
        .donor-image .overlay-text {
            position: absolute;
            bottom: 70px;
            color: #333;
            font-size: 1em;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 5px 10px;
            border-radius: 4px;
        }
        .donor-info {
            width: 60%;
            padding: 40px;
            overflow-y: auto;
        }
        .donor-info h2 {
            margin-top: 0;
            color: #333;
            font-size: 2em;
        }
        .form-group {
            margin-bottom: 1.5em;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
            color: #555;
        }
        .form-control {
            width: 100%;
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            color: #333;
        }
        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-group textarea {
            resize: vertical;
            padding: 15px;
        }
        .btn-submit {
            background-color: #007bff;
            color: #fff;
            padding: 15px 20px;
            border: none;
            border-radius: 4px;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        #imageUpload {
            display: none;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="#" onclick="toggleModal('searchModal')">Search Essentials</a></li>
        <li><a href="#">Donate</a></li>
        <li><a href="#">Request</a></li>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Profile</a></li>
    </ul>
</nav>

<div class="donation-form-container">
    <!-- HTML for Image Upload Section -->
<div class="donor-image" onclick="document.getElementById('imageUpload').click();">
    <img src="https://via.placeholder.com/200" alt="Donor Image" id="previewImage">
    <div class="overlay-text">Click to Upload Image</div>
</div>
<input type="file" id="imageUpload" name="imageUpload" accept="image/*" onchange="previewImage(event)">

    <div class="donor-info">
        <h2>Donation Form</h2>
        <form method="POST" enctype="multipart/form-data">
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
                <label for="foodType">Essential Type</label>
                <select name="foodType" id="foodType" class="form-control" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Household Care">Household Care</option>
                    <option value="Personal Care">Personal Care</option>
                </select>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" class="form-control" rows="4"></textarea>
            </div>
            <button type="submit" class="btn-submit">Submit Donation</button>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const preview = document.getElementById('previewImage');
        preview.src = URL.createObjectURL(event.target.files[0]);
    }
</script>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $phone = htmlspecialchars($_POST['phone']);
    $foodType = htmlspecialchars($_POST['foodType']);
    $message = htmlspecialchars($_POST['message']);

    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['imageUpload']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $targetFilePath)) {
                echo "<p>The image was successfully uploaded.</p>";
            } else {
                echo "<p>Sorry, there was an error uploading your file.</p>";
            }
        } else {
            echo "<p>Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
        }
    } else {
        echo "<p>No image uploaded or there was an upload error.</p>";
    }

    $data = "Name: $name\nEmail: $email\nAddress: $address\nPhone: $phone\nFood Type: $foodType\nMessage: $message\nImage Path: $targetFilePath\n\n";
    file_put_contents('donations.txt', $data, FILE_APPEND);

    echo "<p>Thank you, $name, for your donation! We have received your details.</p>";
}
?>

</body>
</html>

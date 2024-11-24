<?php
require_once 'db_connection.php';
require_once "RequestManager.php";

// Create Database connection
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all required fields are filled
    if (isset($_POST['full_name'], $_POST['email'], $_POST['location'], $_POST['requested_items'], $_POST['quantity'], $_POST['item_condition'], $_POST['urgency'], $_POST['notes'])) {
        // Sanitize inputs
        $name = htmlspecialchars($_POST['full_name']);  // Changed from full_name to name
        $email = htmlspecialchars($_POST['email']);
        $location = htmlspecialchars($_POST['location']);
        $requested_items = htmlspecialchars($_POST['requested_items']);
        $quantity = htmlspecialchars($_POST['quantity']);
        $item_condition = implode(", ", $_POST['item_condition']);
        $urgency = htmlspecialchars($_POST['urgency']);
        $notes = htmlspecialchars($_POST['notes']);
        $status = "Pending";

        // Updated SQL query to match database column names
        $stmt = $conn->prepare("INSERT INTO requests (name, email, location, requested_items, quantity, item_condition, urgency, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssssissss", $name, $email, $location, $requested_items, $quantity, $item_condition, $urgency, $notes, $status);

            if ($stmt->execute()) {
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Error submitting request: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Donation Request Form</h2>

    <form method="POST">
        <div class="mb-3">
            <label for="fullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullName" name="full_name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>

        <div class="form-group mb-3">
            <label for="requestedItems">Requested Items</label>
            <textarea name="requested_items" id="requestedItems" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Item Condition</label><br>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="item_condition[]" value="Unopened" id="condition1">
                <label class="form-check-label" for="condition1">Unopened</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="item_condition[]" value="Properly Packaged" id="condition2">
                <label class="form-check-label" for="condition2">Properly Packaged</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="item_condition[]" value="Within Expiry Date" id="condition3">
                <label class="form-check-label" for="condition3">Within Expiry Date</label>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="urgency">Urgency Level</label>
            <select name="urgency" id="urgency" class="form-control" required>
                <option value="" disabled selected>Select Urgency Level</option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="notes">Notes (Optional)</label>
            <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
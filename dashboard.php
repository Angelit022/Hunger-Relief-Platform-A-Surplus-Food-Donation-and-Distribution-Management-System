<?php
require_once "layout/header.php";
require_once "db_connection.php";
require_once "classes/DonationManager.php";

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Create Database connection
$database = new Database();
$conn = $database->getConnection();
$donationManager = new DonationManager($conn);

// Handle delete request
if (isset($_GET['delete'])) {
    $donationManager->deleteDonation($_GET['delete']);
    header("Location: dashboard.php");
    exit();
}

// Fetch all donations
$donations = $donationManager->getDonations();
?>

<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Donations</h2>

    <!-- Display Donations -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Products Type</th>
                <th>Quantity</th>
                <th>Products Condition</th>
                <th>Delivery Option</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donations as $donation): ?>
                <tr>
                    <td><?= htmlspecialchars($donation["name"]); ?></td>
                    <td><?= htmlspecialchars($donation["email"]); ?></td>
                    <td><?= htmlspecialchars($donation["address"]); ?></td>
                    <td><?= htmlspecialchars($donation["phone"]); ?></td>
                    <td><?= htmlspecialchars($donation["products_type"]); ?></td>
                    <td><?= htmlspecialchars($donation["quantity"]); ?></td>
                    <td><?= htmlspecialchars($donation["products_condition"]); ?></td>
                    <td><?= htmlspecialchars($donation["delivery_option"]); ?></td>
                    <td><?= htmlspecialchars($donation["message"]); ?></td>
                    <td>
                        <a href="edit_donation.php?edit=<?= $donation["id"]; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="dashboard.php?delete=<?= $donation["id"]; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
include "layout/footer.php";
?>

<?php
require_once "header.php";
require_once "db_connection.php";
require_once "DonationManager.php";

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();
$donationManager = new DonationManager($conn);

if (isset($_GET['delete'])) {
    $donationManager->deleteDonation($_GET['delete']);
    header("Location: dashboard.php");
    exit();
}

$donations = $donationManager->getDonations();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Requests</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Product Type</th>
                <th>Quantity</th>
                <th>Product Condition</th>
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
                        <form action="request_form.php" method="POST" style="display:inline;">
                            <input type="hidden" name="request" value="<?= $donation["id"]; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Request</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
include "footer.php";
?>

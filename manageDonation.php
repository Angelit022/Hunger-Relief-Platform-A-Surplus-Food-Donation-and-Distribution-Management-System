<?php
session_start();
require_once 'adminSidebar.php';
require_once '../classAdmin/adminClass.php';
require_once '../classAdmin/ActionHandler.php';
require_once '../classes/db_connection.php';

$database = new Database();
$db = $database->getConnection();
$actionHandler = new ActionHandler($db);
$admin = new AdminClass();

$statusMessage = "";

// Handle GET requests for actions
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['donation_id'])) {
    $action = $_GET['action'];
    $donationId = $_GET['donation_id'];

    switch ($action) {
        case 'Accepted':
            $result = $actionHandler->approveRecord($donationId, 'donations');
            $statusMessage = $result ? "Donation Accepted successfully!" : "Failed to Accept Donation!";
            break;
        case 'Rejected':
            $result = $actionHandler->rejectRecord($donationId, 'donations');
            $statusMessage = $result ? "Donation Rejected successfully!" : "Failed to Reject Donation!";
            break;
        case 'Fulfilled':
            $result = $actionHandler->fulfillRecord($donationId, 'donations');
            $statusMessage = $result ? "Donation Fulfilled successfully!" : "Failed to Fulfill Donation!";
            break;
        default:
            $statusMessage = "Invalid action!";
    }
}

// Fetch donations from the database 
$query = "
    SELECT 
        donations.id AS donor_id,
        donations.user_id,
        CONCAT(users.first_name, ' ', users.last_name) AS name,
        users.email,
        users.address,
        donations.phone,
        donations.products_type,
        donations.quantity,
        donations.products_condition,
        donations.delivery_option,
        donations.message,
        donations.status
    FROM donations
    JOIN users ON donations.user_id = users.user_id
";
$result = $db->query($query);
$donations = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminStyles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="main-content container mt-4">
<h1 class="text-center">Donations Manager</h1>

    <?php if ($statusMessage): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($statusMessage); ?>
        </div>
    <?php endif; ?>

    <table id="donationTable" class="table table-striped">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Donor ID</th>
                <th>Donor Name</th>
                <th>Donor Email</th>
                <th>Donor Address</th>
                <th>Donor Phone</th>
                <th>Products Type</th>
                <th>Quantity</th>
                <th>Condition</th>
                <th>Delivery Option</th>
                <th>Message</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($donations): ?>
                <?php foreach ($donations as $donation): ?>
                    <tr>
                        <td><?= htmlspecialchars($donation['user_id']); ?></td>
                        <td><?= htmlspecialchars($donation['donor_id']); ?></td>
                        <td><?= htmlspecialchars($donation['name']); ?></td>
                        <td><?= htmlspecialchars($donation['email']); ?></td>
                        <td><?= htmlspecialchars($donation['address']); ?></td>
                        <td><?= htmlspecialchars($donation['phone']); ?></td>
                        <td><?= htmlspecialchars($donation['products_type']); ?></td>
                        <td>
                            <?php
                            $quantity = htmlspecialchars($donation['quantity']);
                            echo $quantity == 0 ? 'Out of Stock' : $quantity;
                            ?>
                        </td>
                        <td><?= htmlspecialchars($donation['products_condition']); ?></td>
                        <td><?= htmlspecialchars($donation['delivery_option']); ?></td>
                        <td><?= htmlspecialchars($donation['message']); ?></td>
                        <td><?= htmlspecialchars($donation['status']); ?></td>
                        <td>
                            <?php if ($donation['status'] === 'Pending'): ?>
                                <a href="?action=Accepted&donation_id=<?= urlencode($donation['donor_id']); ?>" class="btn btn-success">Accept</a>
                                <a href="?action=Rejected&donation_id=<?= urlencode($donation['donor_id']); ?>" class="btn btn-danger">Reject</a>
                            <?php elseif ($donation['status'] === 'Accepted'): ?>
                                <a href="?action=Fulfilled&donation_id=<?= urlencode($donation['donor_id']); ?>" class="btn btn-primary">Fulfill</a>
                            <?php elseif ($donation['status'] === 'Fulfilled'): ?>
                                <span class="badge bg-success">Completed</span>
                            <?php elseif ($donation['status'] === 'Rejected'): ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13">No donations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#donationTable').DataTable();
    });
</script>

</body>
</html>

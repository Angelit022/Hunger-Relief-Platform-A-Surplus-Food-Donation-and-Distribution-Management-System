<?php
session_start();
require_once 'adminSidebar.php';
require_once '../classAdmin/adminClass.php';
require_once '../classAdmin/ActionHandler.php';
require_once '../classes/db_connection.php';

// Initialize Database and Classes
$database = new Database();
$db = $database->getConnection();
$actionHandler = new ActionHandler($db);
$admin = new AdminClass();

$statusMessage = "";

// Handle GET requests for actions
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['request_id'])) {
    $action = $_GET['action'];
    $requestId = $_GET['request_id'];

    switch ($action) {
        case 'Accepted':
            $result = $actionHandler->approveRecord($requestId, 'donation_requests');
            $statusMessage = $result ? "Request Accepted successfully!" : "Failed to Accept Request!";
            break;
        case 'Rejected':
            $result = $actionHandler->rejectRecord($requestId, 'donation_requests');
            $statusMessage = $result ? "Request Rejected successfully!" : "Failed to Reject Request!";
            break;
        case 'Fulfilled':
            $result = $actionHandler->fulfillRecord($requestId, 'donation_requests');
            $statusMessage = $result ? "Request Fulfilled successfully!" : "Failed to Fulfill Request!";
            break;
        default:
            $statusMessage = "Invalid action!";
    }
}

// Fetch donation requests from the database
$requests = $admin->getDonationRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminStyles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="main-content container mt-4">
<h1 class="text-center">Requests Manager</h1>
>
    <?php if ($statusMessage): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($statusMessage); ?>
        </div>
    <?php endif; ?>


    <table id="requestTable" class="table table-striped">
        <thead>
        <tr>
            <th>Don ID</th>
            <th>Req ID</th>
            <th>Requestor Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Product Type</th>
            <th>Quantity </th>
            <th>Condition </th>
            <th>Delivery </th>
            <th>Special Notes</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($requests): ?>
        <?php foreach ($requests as $request): ?>
            <tr>
                <td><?= isset($request['donation_id']) ? htmlspecialchars($request['donation_id']) : 'N/A'; ?></td>
                <td><?= isset($request['requestor_id']) ? htmlspecialchars($request['requestor_id']) : 'N/A'; ?></td>
                <td><?= isset($request['requestor_name']) ? htmlspecialchars($request['requestor_name']) : 'N/A'; ?></td>
                <td><?= isset($request['requestor_email']) ? htmlspecialchars($request['requestor_email']) : 'N/A'; ?></td>
                <td><?= isset($request['requestor_phone']) ? htmlspecialchars($request['requestor_phone']) : 'N/A'; ?></td>
                <td><?= isset($request['requestor_address']) ? htmlspecialchars($request['requestor_address']) : 'N/A'; ?></td>
                <td><?= isset($request['donation_product_type']) ? htmlspecialchars($request['donation_product_type']) : 'N/A'; ?></td>
                <td><?= isset($request['donation_quantity']) ? htmlspecialchars($request['donation_quantity']) : 'N/A'; ?></td>
                <td><?= isset($request['donation_condition']) ? htmlspecialchars($request['donation_condition']) : 'N/A'; ?></td>
                <td><?= isset($request['donation_delivery_option']) ? htmlspecialchars($request['donation_delivery_option']) : 'N/A'; ?></td>
                <td><?= isset($request['special_notes']) ? htmlspecialchars($request['special_notes']) : 'N/A'; ?></td>
                <td><?= isset($request['status']) ? htmlspecialchars($request['status']) : 'N/A'; ?></td>
                <td>
                    <?php if ($request['status'] === 'Pending'): ?>
                        <a href="?action=Accepted&request_id=<?= urlencode($request['requestor_id']); ?>" class="btn btn-success">Accept</a>
                        <a href="?action=Rejected&request_id=<?= urlencode($request['requestor_id']); ?>" class="btn btn-danger">Reject</a>
                    <?php elseif ($request['status'] === 'Accepted'): ?>
                        <a href="?action=Fulfilled&request_id=<?= urlencode($request['requestor_id']); ?>" class="btn btn-primary">Fulfill</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
    <tr>
        <td colspan="13">No requests found.</td>
    </tr>
<?php endif; ?>

        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#requestTable').DataTable();
    });
</script>

</body>
</html>

<?php
require_once 'adminSidebar.php';
require_once '../classAdmin/adminClass.php';

$admin = new AdminClass(); //create instance

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $donations = $admin->getUserDonations($user_id); 
    $requests = $admin->getUserRequests($user_id);   

    // Calculate total donations and request
    $totalDonations = count($donations);
    $totalRequests = count($requests);
} else {
    echo "User ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminStyles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>

<div class="main-content container mt-4">
    <h2 class="text-center">User Activity - User ID: <?php echo htmlspecialchars($user_id); ?></h2>
    
    <div class="table-container">
        <div class="table-section">
            <h4>Donation History (Total: <?php echo $totalDonations; ?>)</h4>
            <table id="donationsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Product Type</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($donations): ?>
                        <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donation['donation_id']); ?></td>
                                <td><?php echo htmlspecialchars($donation['products_type']); ?></td>
                                <td><?php echo htmlspecialchars($donation['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($donation['created_at'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($donation['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No donations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-section">
            <h4>Request History (Total: <?php echo $totalRequests; ?>)</h4>
            <table id="requestsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Requestor ID</th>
                        <th>Product Type</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($requests): ?>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['requestor_id']); ?></td>
                                <td><?php echo htmlspecialchars($request['products_type']); ?></td>
                                <td><?php echo htmlspecialchars($request['created_at'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($request['status'] ?? 'Pending'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-auto">
        <a href="manageUser.php" class="btn btn-secondary mt-3" style="background-color: aquamarine; color: black;"> < Back</a>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#donationsTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });
        $('#requestsTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });
    });
</script>

</body>
</html>

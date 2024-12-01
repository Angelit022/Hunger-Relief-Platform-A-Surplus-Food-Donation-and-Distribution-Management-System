<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";
require_once "classes/RequestManager.php";

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Create Database connection
$database = new Database();
$conn = $database->getConnection();

// Initialize managers
$donationManager = new DonationManager($conn);
$requestManager = new RequestManager($conn);

// Fetch all donations (This will always fetch the latest data)
$donations = $donationManager->getDonations();

// Fetch all requests (This will always fetch the latest data)
$requests = $requestManager->getRequestsByStatus('Pending'); // Adjust status filter as needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="styles.css" rel="stylesheet">
</head>
<body>

<div class="donation-dashboard-container">
    <h2>Donations Table</h2>

    <!-- Display Donations Table -->
    <table id="donationsTable" class="table table-striped table-bordered donation-table">
    <thead>
        <tr>
            <th>Donation ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Products Type</th>
            <th>Quantity</th>
            <th>Products Condition</th>
            <th>Delivery Option</th>
            <th>Message</th>
            <th>Status</th> 
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($donations as $donation): ?>
            <tr>
                <td><?= htmlspecialchars($donation["id"]); ?></td>
                <td><?= htmlspecialchars($donation["name"]); ?></td>
                <td><?= htmlspecialchars($donation["email"]); ?></td>
                <td><?= htmlspecialchars($donation["address"]); ?></td>
                <td><?= htmlspecialchars($donation["phone"]); ?></td>
                <td><?= htmlspecialchars($donation["products_type"]); ?></td>
                <td><?= htmlspecialchars($donation["quantity"]); ?></td>
                <td><?= htmlspecialchars($donation["products_condition"]); ?></td>
                <td><?= htmlspecialchars($donation["delivery_option"]); ?></td>
                <td><?= htmlspecialchars($donation["message"]); ?></td>
                <td><?= isset($donation["status"]) ? htmlspecialchars($donation["status"]) : 'N/A'; ?></td> <!-- Check for 'status' key -->
                <td>
                    <form action="edit_donation.php" method="POST" style="display: inline;">
                        <input type="hidden" name="edit" value="<?= $donation["id"]; ?>">
                        <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                    </form>
                    <form action="delete_donation.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $donation["id"]; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- Request Dashboard -->
<div class="request-dashboard-container" style="margin-top: 50px;">
    <h2>Requests Table</h2>

    <!-- Display Requests Table -->
    <table id="requestsTable" class="table table-striped table-bordered donation-table">
        <thead>
            <tr>
                <th>Requestor Name</th>
                <th>Requestor Email</th>
                <th>Requestor Phone</th>
                <th>Delivery Option</th>
                <th>Special Notes</th>
                <th>Donation ID</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request["requestor_name"]); ?></td>
                    <td><?= htmlspecialchars($request["requestor_email"]); ?></td>
                    <td><?= htmlspecialchars($request["requestor_phone"]); ?></td>
                    <td><?= htmlspecialchars($request["delivery_option"]); ?></td>
                    <td><?= htmlspecialchars($request["special_notes"]); ?></td>
                    <td><?= htmlspecialchars($request["donation_id"]); ?></td>
                    <td><?= isset($request["status"]) ? htmlspecialchars($request["status"]) : 'N/A'; ?></td> <!-- Check for 'status' key -->
                    <td>
                    <form action="edit_request.php" method="GET" style="display: inline;">
                        <input type="hidden" name="edit" value="<?= $request['requestor_id']; ?>"> <!-- Corrected Request ID -->
                        <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                    </form>
                    <form action="delete_request.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $request['requestor_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        $('#donationsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [5, 10, 25, 50, 100],
            "autoWidth": true,
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ]
        });

        $('#requestsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [5, 10, 25, 50, 100],
            "autoWidth": true,
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ]
        });
    });
</script>

</body>
</html>


<?php
include "layout/footer.php";
?>

<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
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

// Fetch all donations (This will always fetch the latest data)
$donations = $donationManager->getDonations();
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

    <!-- Link to custom CSS file -->
    <link href="styles.css" rel="stylesheet">
</head>
<body>
<div class="donation-dashboard-container">
    <h2>Donations</h2>

    <!-- Display Donations Table -->
    <table id="donationsTable" class="table table-striped table-bordered donation-table">
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

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        $('#donationsTable').DataTable({
            "paging": true,          // Enable pagination
            "searching": true,       // Enable search
            "ordering": true,        // Enable column ordering
            "info": true,            // Show table information
            "lengthMenu": [5, 10, 25, 50, 100], // Options for "entries per page"
            "columnDefs": [
                { "orderable": false, "targets": -1 } // Disable ordering for the "Actions" column
            ]
        });
    });
</script>
</body>
</html>

<?php
include "layout/footer.php";
?>

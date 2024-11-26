<?php
require_once "header.php";
require_once "db_connection.php";
require_once "DonationManager.php";
require_once "RequestManager.php";

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Create Database connection
$database = new Database();
$conn = $database->getConnection();

$donationManager = new DonationManager($conn);
$requestManager = new RequestManager($conn);

// Fetch all donations and requests
$donations = $donationManager->getDonations();
$requests = $requestManager->getRequests();
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

    <!-- Custom CSS -->
    <style>
        .dashboard-container {
            padding: 20px;
        }
        .status-pending {
            background-color: #fff3cd;
        }
        .status-approved {
            background-color: #d4edda;
        }
        .status-rejected {
            background-color: #f8d7da;
        }
        .table-container {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Donations Section -->
    <div class="table-container">
        <h2 class="mb-4">Donations</h2>
        <table id="donationsTable" class="table table-striped table-bordered">
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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $donation): ?>
                    <tr class="status-<?= strtolower($donation['status'] ?? 'pending') ?>">
                        <td><?= htmlspecialchars($donation["name"]); ?></td>
                        <td><?= htmlspecialchars($donation["email"]); ?></td>
                        <td><?= htmlspecialchars($donation["address"]); ?></td>
                        <td><?= htmlspecialchars($donation["phone"]); ?></td>
                        <td><?= htmlspecialchars($donation["products_type"]); ?></td>
                        <td><?= htmlspecialchars($donation["quantity"]); ?></td>
                        <td><?= htmlspecialchars($donation["products_condition"]); ?></td>
                        <td><?= htmlspecialchars($donation["delivery_option"]); ?></td>
                        <td><?= htmlspecialchars($donation["message"]); ?></td>
                        <td><?= htmlspecialchars($donation["status"] ?? 'Pending'); ?></td>
                        <td>
                            <div class="btn-group">
                                <form action="edit_donation.php" method="POST" class="me-2">
                                    <input type="hidden" name="edit" value="<?= $donation["id"]; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                                </form>
                                <form action="delete_donation.php" method="POST" onsubmit="return confirmDelete('donation');">
                                    <input type="hidden" name="id" value="<?= $donation["id"]; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Requests Section -->
    <div class="table-container">
        <h2 class="mb-4">Requests</h2>
        <table id="requestsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Location</th>
                    <th>Requested Items</th>
                    <th>Quantity</th>
                    <th>Item Condition</th>
                    <th>Urgency</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr class="status-<?= strtolower($request['status'] ?? 'pending') ?>">
                        <td><?= htmlspecialchars($request["name"]); ?></td>
                        <td><?= htmlspecialchars($request["email"]); ?></td>
                        <td><?= htmlspecialchars($request["location"]); ?></td>
                        <td><?= htmlspecialchars($request["requested_items"]); ?></td>
                        <td><?= htmlspecialchars($request["quantity"]); ?></td>
                        <td><?= htmlspecialchars($request["item_condition"]); ?></td>
                        <td><?= htmlspecialchars($request["urgency"]); ?></td>
                        <td><?= htmlspecialchars($request["notes"]); ?></td>
                        <td><?= htmlspecialchars($request["status"] ?? 'Pending'); ?></td>
                        <td>
                            <div class="btn-group">
                                <form action="edit_request.php" method="POST" class="me-2">
                                    <input type="hidden" name="edit" value="<?= $request["id"]; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                                </form>
                                <form action="delete_request.php" method="POST" onsubmit="return confirmDelete('request');">
                                    <input type="hidden" name="id" value="<?= $request["id"]; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Initialize DataTables and Handlers -->
<script>
$(document).ready(function() {
    // Initialize DataTables with consistent options
    const tableOptions = {
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthMenu": [5, 10, 25, 50, 100],
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": -1 }
        ],
        "responsive": true,
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)"
        }
    };

    $('#donationsTable').DataTable(tableOptions);
    $('#requestsTable').DataTable(tableOptions);
});

// Enhanced delete confirmation using SweetAlert2
function confirmDelete(type) {
    return Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to cancel this ${type}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        return result.isConfirmed;
    });
}
</script>

</body>
</html>

<?php include "footer.php"; ?>
<?php 
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";
require_once "classes/RequestManager.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Initialize managers
$donationManager = new DonationManager($conn);
$requestManager = new RequestManager($conn);

// Fetch donations for the logged-in user
$query = "
    SELECT 
        donations.id,
        CONCAT(users.first_name, ' ', users.last_name) AS full_name,
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
    WHERE donations.user_id = ? AND donations.status != 'Cancelled'
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$donations = [];
while ($row = $result->fetch_assoc()) {
    $donations[] = $row;
}

// Fetch requests for the logged-in user
$query = "
    SELECT 
        donation_requests.requestor_id AS requestor_id,  
        CONCAT(users.first_name, ' ', users.last_name) AS requestor_name,
        users.email AS requestor_email,
        users.address AS requestor_address,
        donation_requests.requestor_phone AS requestor_phone,  
        donations.id AS donation_id,  -- Donation ID
        donations.products_type,  -- Product type of the donor
        donations.quantity,  -- Donor's quantity
        donations.products_condition AS donor_condition,  
        donations.delivery_option AS donor_delivery_option,  
        donations.quantity,  -- Donor's quantity
        donations.products_condition AS donor_condition,  
        donations.delivery_option AS donor_delivery_option,  
        donation_requests.special_notes,
        donation_requests.status,
        donation_requests.quantity
        donation_requests.status,
        donation_requests.quantity
    FROM donation_requests
    JOIN users ON donation_requests.user_id = users.user_id
    JOIN donations ON donation_requests.donation_id = donations.id  -- Joining donations table
    WHERE donation_requests.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$requestResult = $stmt->get_result();

$requests = [];
while ($row = $requestResult->fetch_assoc()) {
    $requests[] = $row;
}

// Handle cancel action for donation
if (isset($_GET['cancel_id'])) {
    $cancelDonationId = intval($_GET['cancel_id']);
    
    $updateQuery = "UPDATE donations SET status = 'Cancelled' WHERE id = ? AND user_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ii', $cancelDonationId, $_SESSION['user_id']);
    
    if ($updateStmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error cancelling donation.";
    }
    
    $updateStmt->close();
}


$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?= $_SESSION['first_name'] ?>!</h1>
        <p>Your donations and requests are listed below.</p>

        <div class="donation-dashboard-container">
            <h2>Your Donations</h2>
            <table class="donation-table table-bordered" id="donations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Product Type</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                        <th>Delivery Option</th>
                        <th>Message</th>  
                        <th>Status</th> 
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation): ?>
                        <tr <?= $donation['quantity'] == 0 ? 'class="table-warning"' : '' ?>>
                            <td><?= htmlspecialchars($donation['id']); ?></td>
                            <td><?= htmlspecialchars($donation['full_name']); ?></td>
                            <td><?= htmlspecialchars($donation['email']); ?></td>
                            <td><?= htmlspecialchars($donation['address']); ?></td>
                            <td><?= htmlspecialchars($donation['phone']); ?></td>
                            <td><?= htmlspecialchars($donation['products_type']); ?></td>
                            <td><?= htmlspecialchars($donation['quantity']); ?></td>
                            <td><?= htmlspecialchars($donation['products_condition']); ?></td>
                            <td><?= htmlspecialchars($donation['delivery_option']); ?></td>
                            <td><?= htmlspecialchars($donation['message']); ?></td>
                            <td>
                                <?php 
                                if ($donation['quantity'] == 0 || $donation['status'] == 'Out of Stock') {
                                    echo '<span class="badge bg-secondary">Out of Stock</span>';
                                } else {
                                    switch ($donation['status']) {
                                        case 'Pending':
                                            echo '<span class="badge bg-warning">Pending</span>';
                                            break;
                                        case 'Accepted':
                                            echo '<span class="badge bg-success">Accepted</span>';
                                            break;
                                        case 'Rejected':
                                            echo '<span class="badge bg-danger">Rejected</span>';
                                            break;
                                        case 'Fulfilled':
                                            echo '<span class="badge bg-primary">Fulfilled</span>';
                                            break;
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($donation['status'] == 'Pending' && $donation['quantity'] > 0): ?>
                                    <a href="edit_donation.php?id=<?= htmlspecialchars($donation['id']); ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="delete_donation.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="cancel_id" value="<?= htmlspecialchars($donation['id']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                <?php elseif ($donation['status'] == 'Accepted'): ?>
                                    <span class="badge bg-success">Processing</span>
                                <?php elseif ($donation['status'] == 'Fulfilled'): ?>
                                    <span class="badge bg-primary">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Requests Table -->
        <div class="request-dashboard-container">
            <h2>Your Requests</h2>
            <table class="request-table table-bordered" id="requests-table">
                <thead>
                    <tr>
                        <th>Don ID</th>   
                        <th>Req ID</th>
                        <th>Requestor Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Product Type</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                        <th>Delivery Option</th>
                        <th>Special Notes</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['donation_id']) ?></td>  
                        <td><?= htmlspecialchars($request['requestor_id']) ?></td>
                        <td><?= htmlspecialchars($request['requestor_name']) ?></td>
                        <td><?= htmlspecialchars($request['requestor_email']) ?></td>
                        <td><?= htmlspecialchars($request['requestor_phone']) ?></td>
                        <td><?= htmlspecialchars($request['requestor_address']) ?></td>
                        <td><?= htmlspecialchars($request['products_type']) ?></td>
                        <td><?= htmlspecialchars($request['quantity']) ?></td>
                        <td><?= htmlspecialchars($request['quantity']) ?></td>
                        <td><?= htmlspecialchars($request['donor_condition']) ?></td>
                        <td><?= htmlspecialchars($request['donor_delivery_option']) ?></td>
                        <td><?= htmlspecialchars($request['special_notes']) ?></td>
                        
                        <td>
                            <?php 
                            switch ($request['status']) {
                                case 'Pending':
                                    echo '<span class="badge bg-warning">Pending</span>';
                                    break;
                                case 'Accepted':
                                    echo '<span class="badge bg-success">Accepted</span>';
                                    break;
                                case 'Rejected':
                                    echo '<span class="badge bg-danger">Rejected</span>';
                                    break;
                                case 'Fulfilled':
                                    echo '<span class="badge bg-primary">Fulfilled</span>';
                                    break;
                            }
                            ?>
                        </td>

                        <td>
                            <?php if ($request['status'] == 'Pending'): ?>
                                <a href="edit_request.php?id=<?= $request['requestor_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                <form action="delete_request.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $request['requestor_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            <?php elseif ($request['status'] == 'Accepted'): ?>
                                <span class="badge bg-success">Processing</span>
                            <?php elseif ($request['status'] == 'Fulfilled'): ?>
                                <span class="badge bg-primary">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


    <script>
        $(document).ready(function() {
            $('#donations-table').DataTable();
            $('#requests-table').DataTable();
        });
    </script>
</body>
</html>

<?php
require_once "layout/footer.php";
?>

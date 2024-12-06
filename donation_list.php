<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";

// Database connection
$database = new Database();
$conn = $database->getConnection(); 

// Initialize DonationManager and fetch donation data
$donationManager = new DonationManager($conn);
$donations = $donationManager->getAcceptedDonations();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donors List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<header class="donors-header">
    <h1>-- DONORS LIST --</h1>
</header>

<div class="donor-cards-container">
    <?php if (!empty($donations)): ?>
        <?php foreach ($donations as $donation): ?>
            <div class="donor-card">
                <h3><?php echo htmlspecialchars($donation['name']); ?></h3>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($donation['address']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($donation['email']); ?></p>
                <p><strong>Product Type:</strong> <?php echo htmlspecialchars($donation['products_type']); ?></p>
                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($donation['quantity']); ?></p>
                <p><strong>Condition:</strong> <?php echo htmlspecialchars($donation['products_condition']); ?></p>
                <p><strong>Delivery Option:</strong> <?php echo htmlspecialchars($donation['delivery_option']); ?></p>
                <?php if ($donationManager->isDonationOwner($donation['id'], $_SESSION['user_id'])): ?>
                    <button class="btn btn-secondary" onclick="showOwnDonationError()">Request</button>
                <?php else: ?>
                    <a href="request_form.php?donation_id=<?php echo $donation['id']; ?>" class="btn btn-primary">Request</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No donations available.</p>
    <?php endif; ?>
</div>

<script>
function showOwnDonationError() {
    Swal.fire({
        title: 'Error!',
        text: 'You cannot request your own donation.',
        icon: 'error',
        confirmButtonText: 'OK'
    });
}
</script>

<?php 
// Close connection
$database->closeConnection();
require_once "layout/footer.php"; 
?>

</body>
</html>


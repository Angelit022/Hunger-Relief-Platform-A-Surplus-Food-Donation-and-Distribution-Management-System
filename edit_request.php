<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/RequestManager.php";
require_once "classes/DonationManager.php";

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();
$requestManager = new RequestManager($conn);
$donationManager = new DonationManager($conn);

// Fetch the request details if edit ID is provided
$editData = null;
if (isset($_GET['id'])) {
    $editData = $requestManager->getRequestById($_GET['id']);
    
    if ($editData === null) {
        echo "No data found for ID: " . $_GET['id'];
        exit();
    }
} else {
    echo "No request ID provided.";
    exit();
}

$error_message = '';
$success_message = '';

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["requestor_id"];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $delivery_option = isset($_POST['delivery_option']) ? $_POST['delivery_option'] : '';
    $notes = isset($_POST['special_notes']) ? $_POST['special_notes'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $previousQuantity = isset($_POST['previous_quantity']) ? intval($_POST['previous_quantity']) : 0;

    if (!preg_match("/^[0-9\s\-\+]*$/", $phone)) {
        $error_message = "Invalid phone number format. Please enter a valid phone number.";
    } elseif ($quantity <= 0) {
        $error_message = "Quantity must be a positive number.";
    } elseif ($quantity > ($editData['available_quantity'] + $previousQuantity)) {
        $error_message = "The requested quantity exceeds the available donation quantity.";
    } else {
        // Check if any changes were made
        $changes_made = ($phone !== $editData['requestor_phone'] ||
                         $delivery_option !== $editData['delivery_option'] ||
                         $notes !== $editData['special_notes'] ||
                         $address !== $editData['requestor_address'] ||
                         $quantity !== $previousQuantity);

        if (!$changes_made) {
            $error_message = "No changes were made to the request.";
        } else {
        
            $updateSuccess = $requestManager->updateRequest($id, $phone, $delivery_option, $notes, $address, $quantity, $previousQuantity);
            
            if ($updateSuccess) {
                $success_message = "Request updated successfully!";
            } else {
                $error_message = "Failed to update the request. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Request</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="donation-form-container">
    <?php if ($editData): ?>
        <div class="donation-details-box" style="width: 30%; max-width: 700px; padding: 25px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 30px; transition: transform 0.3s ease, box-shadow 0.3s ease; margin-top: 20px; border: 1px solid #ccc;">
            <h3 style="color: #007bff; font-size: 1.6rem; margin-bottom: 15px; text-align: center; font-weight: bold;">Donation Details</h3>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Donor Name:</strong> <?php echo htmlspecialchars($editData['donor_name']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Donor Address:</strong> <?php echo htmlspecialchars($editData['donor_address']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Product Type:</strong> <?php echo htmlspecialchars($editData['products_type']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Available Quantity:</strong> 
                <?php echo htmlspecialchars($editData['available_quantity'] + $editData['requested_quantity']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Delivery Option:</strong> <?php echo htmlspecialchars($editData['donor_delivery_option']); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error_message ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success" role="alert">
            <?= $success_message ?>
        </div>
    <?php endif; ?>
    
    <div class="donor-info">
        <h2>Edit Request</h2>
        <?php if ($editData): ?>
            <form method="POST">
                <input type="hidden" name="requestor_id" value="<?= $editData["request_id"]; ?>">
                <input type="hidden" name="previous_quantity" value="<?= $editData["requested_quantity"]; ?>">

                <div class="form-group">
                    <label for="phone" class="form-label">Your Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                        value="<?= htmlspecialchars($editData["requestor_phone"]); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Your Address</label>
                    <input type="text" class="form-control" id="address" name="address" 
                        value="<?= htmlspecialchars($editData["requestor_address"]); ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity Needed</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" 
                        value="<?= htmlspecialchars($editData["requested_quantity"]); ?>" 
                        min="1" max="<?= $editData['available_quantity'] + $editData['requested_quantity'] ?>" required>
                    <small class="form-text text-muted">Available quantity: <?= $editData['available_quantity'] + $editData['requested_quantity'] ?></small>
                </div>

                <div class="form-group">
                    <label for="delivery_option" class="form-label">Delivery Option</label>
                    <select class="form-control" id="delivery_option" name="delivery_option" required>
                        <option value="">Select delivery option</option>
                        <option value="pickup" <?= ($editData["delivery_option"] == "pickup") ? 'selected' : ''; ?>>Pickup</option>
                        <option value="delivery" <?= ($editData["delivery_option"] == "delivery") ? 'selected' : ''; ?>>Drop-off</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="special_notes" class="form-label">Special Notes</label>
                    <textarea class="form-control" id="special_notes" name="special_notes"><?= htmlspecialchars($editData["special_notes"]); ?></textarea>
                </div>

                <div class="form-buttons">
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Update Request</button>
                </div>
            </form>
        <?php else: ?>
            <p class="alert alert-warning">No request found to edit.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    <?php if ($success_message): ?>
    Swal.fire({
        title: 'Success',
        text: '<?= $success_message ?>',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'dashboard.php';
        }
    });
    <?php endif; ?>
</script>

</body>
</html>

<?php
require_once "layout/footer.php";
?>


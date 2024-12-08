<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Create Database connection
$database = new Database();
$conn = $database->getConnection();
$donationManager = new DonationManager($conn);

// Fetch the donation details if edit ID is provided
$editData = null;
if (isset($_GET['id'])) {
    $editData = $donationManager->getDonationById($_GET['id']);
    
    if ($editData === null) {
        echo "No data found for ID: " . $_GET['id'];
        exit();
    }
} else {
    echo "No donation ID provided.";
    exit();
}

$error_message = '';
$success_message = '';

// Handle update donation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["donation_id"];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $products_type = isset($_POST['products_type']) ? $_POST['products_type'] : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $products_condition = isset($_POST['products_condition']) ? implode(", ", $_POST['products_condition']) : '';
    $delivery_option = isset($_POST['delivery_option']) ? $_POST['delivery_option'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';

    if (!preg_match("/^\d{11}$/", $phone)) {
        $error_message = "Invalid phone number format. Please enter a valid 11-digit phone number.";
    } elseif ($quantity <= 0) {
        $error_message = "Quantity must be a positive number.";
    } elseif (empty($products_type) || empty($products_condition) || empty($delivery_option)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Check if any changes were made
        $changes_made = ($phone !== $editData['phone'] ||
                         $products_type !== $editData['products_type'] ||
                         $quantity !== intval($editData['quantity']) ||
                         $products_condition !== $editData['products_condition'] ||
                         $delivery_option !== $editData['delivery_option'] ||
                         $message !== $editData['message']);

        if (!$changes_made) {
            $error_message = "No changes were made to the donation.";
        } else {
            // Call update function
            $updateSuccess = $donationManager->updateDonation($id, $phone, $products_type, $quantity, $products_condition, $delivery_option, $message);
            
            if ($updateSuccess) {
                $success_message = "Donation updated successfully!";
            } else {
                $error_message = "Failed to update the donation. Please try again.";
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
    <title>Edit Donation</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="donation-form-container">
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
        <h2>Edit Donation</h2>
        <?php if ($editData): ?>
            <form method="POST">
                <input type="hidden" name="donation_id" value="<?= $editData["id"]; ?>">

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                        value="<?= htmlspecialchars($editData["phone"]); ?>" required>
                </div>

                <div class="form-group">
                    <label for="products_type" class="form-label">Product Type</label>
                    <input type="text" class="form-control" id="products_type" name="products_type" 
                        value="<?= htmlspecialchars($editData["products_type"]); ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" 
                        value="<?= htmlspecialchars($editData["quantity"]); ?>" 
                        min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Condition of Food</label><br>
                    <?php
                    $conditions = explode(", ", $editData["products_condition"]);
                    $allConditions = ["Unopened", "Properly Packaged", "Within Expiry Date"];
                    foreach ($allConditions as $condition) {
                        $checked = in_array($condition, $conditions) ? 'checked' : '';
                        echo "<input type='checkbox' name='products_condition[]' value='$condition' $checked> $condition ";
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Delivery Option</label><br>
                    <input type="radio" name="delivery_option" value="Pick-up" <?= ($editData["delivery_option"] == "Pick-up") ? 'checked' : ''; ?> required> Pick-up
                    <input type="radio" name="delivery_option" value="Drop-off" <?= ($editData["delivery_option"] == "Drop-off") ? 'checked' : ''; ?> required> Drop-off
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message"><?= htmlspecialchars($editData["message"]); ?></textarea>
                </div>

                <div class="form-buttons">
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Update Donation</button>
                </div>
            </form>
        <?php else: ?>
            <p class="alert alert-warning">No donation found to edit.</p>
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

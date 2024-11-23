<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";

$database = new Database();
$conn = $database->getConnection();
$donationManager = new DonationManager($conn);

$editData = null;
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit'])) {
    $editData = $donationManager->getDonationById($_POST['edit']);
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $productsType = $_POST['ProductsType'];
    $quantity = (int)$_POST['quantity'];
    $productsCondition = implode(',', $_POST['condition'] ?? []);
    $deliveryOption = $_POST['delivery_option'];
    $messageInput = trim($_POST['message']);

    // Fetch original data
    $originalData = $donationManager->getDonationById($id);

    // Check for changes
    if (
        $name === $originalData['name'] &&
        $email === $originalData['email'] &&
        $address === $originalData['address'] &&
        $phone === $originalData['phone'] &&
        $productsType === $originalData['products_type'] &&
        $quantity == $originalData['quantity'] &&
        $productsCondition === $originalData['products_condition'] &&
        $deliveryOption === $originalData['delivery_option'] &&
        $messageInput === $originalData['message']
    ) {
        $message = "No changes detected. Please modify at least one field.";
    } else {
        // Update the donation
        $updateSuccess = $donationManager->updateDonation(
            $id,
            $name,
            $email,
            $address,
            $phone,
            $productsType,
            $quantity,
            $productsCondition,
            $deliveryOption,
            $messageInput
        );
        $message = $updateSuccess ? "Donation updated successfully!" : "Failed to update the donation. Please try again.";
        
        // Do not redirect here immediately, let SweetAlert handle the redirect
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donation</title>
    <link href="styles.css" rel="stylesheet">
    <!-- SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom CSS to ensure the form remains visible behind SweetAlert */
        .swal2-popup {
            z-index: 99999 !important;
        }
        .donation-form-container {
            z-index: 10;
            position: relative;
        }
    </style>
</head>
<body>
<div class="donation-form-container">
    <div class="donor-info">
        <h2>Edit Donation</h2>

        <?php if ($editData): ?>
            <form action="" method="POST" id="editDonationForm">
                <input type="hidden" name="id" value="<?= $editData['id']; ?>">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($editData['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($editData['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($editData['address']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($editData['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="products_type">Products Type</label>
                    <select class="form-control" id="products_type" name="ProductsType" required>
                        <option value="Beverages" <?= ($editData['products_type'] == 'Beverages') ? 'selected' : ''; ?>>Beverages</option>
                        <option value="Canned Goods" <?= ($editData['products_type'] == 'Canned Goods') ? 'selected' : ''; ?>>Canned Goods</option>
                        <option value="Fresh Produce" <?= ($editData['products_type'] == 'Fresh Produce') ? 'selected' : ''; ?>>Fresh Produce</option>
                        <option value="Packaged Meals" <?= ($editData['products_type'] == 'Packaged Meals') ? 'selected' : ''; ?>>Packaged Meals</option>
                        <option value="Household Care" <?= ($editData['products_type'] == 'Household Care') ? 'selected' : ''; ?>>Household Care</option>
                        <option value="Personal Care" <?= ($editData['products_type'] == 'Personal Care') ? 'selected' : ''; ?>>Personal Care</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($editData['quantity']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Products Condition</label><br>
                    <input type="checkbox" name="condition[]" value="Unopened" <?= in_array('Unopened', explode(',', $editData['products_condition'])) ? 'checked' : ''; ?>> Unopened
                    <input type="checkbox" name="condition[]" value="Properly Packaged" <?= in_array('Properly Packaged', explode(',', $editData['products_condition'])) ? 'checked' : ''; ?>> Properly Packaged
                    <input type="checkbox" name="condition[]" value="Within Expiry Date" <?= in_array('Within Expiry Date', explode(',', $editData['products_condition'])) ? 'checked' : ''; ?>> Within Expiry Date
                </div>
                <div class="form-group">
                    <label for="delivery_option">Delivery Option</label><br>
                    <input type="radio" name="delivery_option" value="Pick-up" <?= ($editData['delivery_option'] == 'Pick-up') ? 'checked' : ''; ?>> Pick-up
                    <input type="radio" name="delivery_option" value="Drop-off" <?= ($editData['delivery_option'] == 'Drop-off') ? 'checked' : ''; ?>> Drop-off
                </div>
                <div class="form-group">
                    <label for="message">Message (Optional)</label>
                    <textarea class="form-control" id="message" name="message" rows="4"><?= htmlspecialchars($editData['message']); ?></textarea>
                </div>
                <div class="form-buttons">
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit" name="update">Update</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>

<?php if ($message): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: "<?= $message === 'Donation updated successfully!' ? 'Success' : 'Error' ?>",
                text: "<?= $message; ?>",
                icon: "<?= $message === 'Donation updated successfully!' ? 'success' : 'error' ?>",
                confirmButtonText: "OK",
                allowOutsideClick: false,  // Prevent click outside to close
                willClose: () => {
                    window.location.href = "dashboard.php";  // Redirect to dashboard after alert
                }
            });
        });
    </script>
<?php endif; ?>


</body>
</html>


<?php
include "layout/footer.php";
?>

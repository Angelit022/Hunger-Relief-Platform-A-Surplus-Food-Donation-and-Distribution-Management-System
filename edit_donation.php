<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";


$database = new Database();
$conn = $database->getConnection();

// Create DonationManager instance
$donationManager = new DonationManager($conn);

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if donation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$donationId = intval($_GET['id']);

// Fetch the donation details
$donation = $donationManager->getDonationById($donationId);
if (!$donation) {
    exit();
}


$errorMessage = "";
$successMessage = false;
$infoMessage = "";

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input data
    $products_type = $_POST['products_type'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 0);
    $products_condition = isset($_POST['products_condition']) ? implode(", ", $_POST['products_condition']) : '';
    $phone = $_POST['phone'] ?? '';
    $delivery_option = $_POST['delivery_option'] ?? '';
    $message = trim($_POST['message'] ?? '');

    // Validate inputs
    if (empty($products_type) || empty($quantity) || empty($products_condition) || empty($phone) || empty($delivery_option)) {
        $errorMessage = "Error: Please fill in all required fields.";
    } elseif (!preg_match("/^\d{11}$/", $phone)) {
        $errorMessage = "Error: Please enter a valid 11-digit phone number.";
    } elseif (!in_array($delivery_option, ['Pick-up', 'Drop-off'])) {
        $errorMessage = "Error: Invalid delivery option selected.";
    } else {
        // Check if any changes were made
        $changes_made = false;
        if ($products_type !== $donation['products_type'] ||
            $quantity !== intval($donation['quantity']) ||
            $products_condition !== $donation['products_condition'] ||
            $phone !== $donation['phone'] ||
            $delivery_option !== $donation['delivery_option'] ||
            $message !== $donation['message']) {
            $changes_made = true;
        }

        if ($changes_made) {
            if ($donationManager->updateDonation($donationId, $phone, $products_type, $quantity, $products_condition, $delivery_option, $message)) {
                $donation = $donationManager->getDonationById($donationId);
                $successMessage = true;
            }
       }
    }
}

// Fetch product types for dropdown
$productTypesQuery = "SELECT DISTINCT products_type FROM donations";
$productTypesResult = $conn->query($productTypesQuery);
if ($productTypesResult === false) {
    die( $conn->error);
}

$productTypes = [];
while ($row = $productTypesResult->fetch_assoc()) {
    $productTypes[] = $row['products_type'];
}

$conn->close();
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
        <div class="donor-info">
            <h2>Edit Donation</h2>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <?php if (!empty($infoMessage)): ?>
                <div class="alert alert-info" role="alert"><?= htmlspecialchars($infoMessage) ?></div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($donation['phone']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="products_type">Product Type</label>
                    <select id="products_type" name="products_type" class="form-control" required>
                        <?php foreach ($productTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>" <?= $type == $donation['products_type'] ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" value="<?= htmlspecialchars($donation['quantity']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Condition of Food</label><br>
                    <input type="checkbox" id="unopened_condition" name="products_condition[]" value="Unopened" <?= in_array('Unopened', explode(", ", $donation['products_condition'])) ? 'checked' : '' ?>>
                    <label for="unopened_condition">Unopened</label>
                    <input type="checkbox" id="packaged_condition" name="products_condition[]" value="Properly Packaged" <?= in_array('Properly Packaged', explode(", ", $donation['products_condition'])) ? 'checked' : '' ?>>
                    <label for="packaged_condition">Properly Packaged</label>
                    <input type="checkbox" id="expiry_condition" name="products_condition[]" value="Within Expiry Date" <?= in_array('Within Expiry Date', explode(", ", $donation['products_condition'])) ? 'checked' : '' ?>>
                    <label for="expiry_condition">Within Expiry Date</label>
                </div>
                <div class="form-group">
                    <label>Pick-up or Drop-off Option</label><br>
                    <input type="radio" name="delivery_option" value="Pick-up" <?= $donation['delivery_option'] === 'Pick-up' ? 'checked' : '' ?> required> Pick-up
                    <input type="radio" name="delivery_option" value="Drop-off" <?= $donation['delivery_option'] === 'Drop-off' ? 'checked' : '' ?> required> Drop-off
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control"><?= htmlspecialchars($donation['message'] ?? '') ?></textarea>
                </div>
                <div class="form-buttons">
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function validateForm() {
        var phone = document.getElementById('phone').value;
        var quantity = document.getElementById('quantity').value;
        var conditions = document.querySelectorAll('input[name="products_condition[]"]:checked');
        var deliveryOption = document.querySelector('input[name="delivery_option"]:checked');

        if (!phone || !quantity || conditions.length === 0 || !deliveryOption) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please fill in all required fields.',
            });
            return false;
        }

        if (!/^\d{11}$/.test(phone)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid 11-digit phone number.',
            });
            return false;
        }

        return true;
    }
    </script>

    <?php if ($successMessage): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Donation Updated Successfully',
                text: 'Your donation details have been updated!',
                confirmButtonText: 'Okay',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'dashboard.php'; 
                }
            });
        </script>
    <?php elseif (!empty($infoMessage)): ?>
        <script>
            Swal.fire({
                icon: 'info',
                title: 'No Changes Made',
                text: '<?= htmlspecialchars($infoMessage) ?>',
                confirmButtonText: 'Okay',
            });
        </script>
    <?php endif; ?>
</body>
</html>

<?php
require_once "layout/footer.php";
?>

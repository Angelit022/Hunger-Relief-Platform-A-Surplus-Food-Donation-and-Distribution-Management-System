<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/RequestManager.php";

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Create Database connection
$database = new Database();
$conn = $database->getConnection();
$requestManager = new RequestManager($conn);

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

// Handle update request
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["requestor_id"];
    $phone = $_POST["phone"];
    $delivery_option = $_POST["delivery_option"];
    $notes = $_POST["special_notes"];
    $address = $_POST["address"];
    $quantity = $_POST["quantity"];
    $name = $editData["requestor_name"] ?? ""; 
    $email = $editData["requestor_email"] ?? ""; 
    $status = 'Pending'; 

    if (!preg_match("/^[0-9\s\-\+\(\)]*$/", $phone)) {
        $message = "Invalid phone number format.";
    } elseif ($quantity < 1) {
        $message = "Quantity must be at least 1.";
    } else {
        // Call update function
        $updateSuccess = $requestManager->updateRequest($id, $name, $email, $phone, $delivery_option, $notes, $address, $quantity, $status);
        $message = $updateSuccess ? "Request updated successfully!" : "Failed to update the request. Please try again.";
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
    <div class="donor-info">
        <h2>Edit Request</h2>
        <?php if ($editData): ?>
            <form method="POST">
                <input type="hidden" name="requestor_id" value="<?= $editData["request_id"]; ?>">

                <div class="form-group">
                    <label for="phone">Your Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" 
                        value="<?= htmlspecialchars($editData["requestor_phone"]); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Your Address</label>
                    <input type="text" name="address" id="address" class="form-control" 
                        value="<?= htmlspecialchars($editData["requestor_address"]); ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" 
                        value="<?= htmlspecialchars($editData["requested_quantity"]); ?>" min="1" required>
                </div>

                <div class="form-group">
                    <label for="delivery_option">Delivery Option</label>
                    <select name="delivery_option" id="delivery_option" class="form-control" required>
                        <option value="" disabled>Select Delivery Option</option>
                        <option value="Pick Up" <?= ($editData["delivery_option"] == "Pick Up") ? 'selected' : ''; ?>>Pick Up</option>
                        <option value="Drop Off" <?= ($editData["delivery_option"] == "Drop Off") ? 'selected' : ''; ?>>Drop Off</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="special_notes">Special Notes</label>
                    <textarea name="special_notes" id="special_notes" class="form-control"><?= htmlspecialchars($editData["special_notes"]); ?></textarea>
                </div>

                <div class="form-buttons">
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Update</button>
                </div>
            </form>
        <?php else: ?>
            <p class="alert alert-warning">No request found to edit.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($message): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: "<?= $message === 'Request updated successfully!' ? 'Success' : 'Error' ?>",
                text: "<?= $message; ?>",
                icon: "<?= $message === 'Request updated successfully!' ? 'success' : 'error' ?>",
                confirmButtonText: "OK",
                allowOutsideClick: false,
                willClose: () => {
                    if ("<?= $message; ?>" === "Request updated successfully!") {
                        window.location.href = "dashboard.php";
                    }
                }
            });
        });
    </script>
<?php endif; ?>

</body>
</html>

<?php
require_once "layout/footer.php";
?>

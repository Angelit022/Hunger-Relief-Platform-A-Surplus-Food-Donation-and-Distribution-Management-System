<?php
require_once "header.php";
require_once "db_connection.php";
require_once "RequestManager.php";


// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}


// Create Database connection
$database = new Database();
$conn = $database->getConnection();
$requestManager = new RequestManager($conn);


// Fetch the request details if edit ID is provided
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $requestManager->getRequestById($_GET['edit']);
}


// Handle update request
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["requestor_id"];


    // Fetch the original data
    $originalData = $requestManager->getRequestById($id);


    // Check if any fields were updated
    if ($_POST["name"] === $originalData["requestor_name"] &&
        $_POST["email"] === $originalData["requestor_email"] &&
        $_POST["phone"] === $originalData["requestor_phone"] &&
        $_POST["delivery_option"] === $originalData["delivery_option"] &&
        $_POST["special_notes"] === $originalData["special_notes"]
    ) {
        $message = "No changes detected. Please modify at least one field.";
    } else {
        // Update the request
        $status = 'Pending'; // Default status
        $updateSuccess = $requestManager->updateRequest(
            $id,
            $_POST["name"],
            $_POST["email"],
            $_POST["phone"],
            $_POST["delivery_option"],
            $_POST["special_notes"],
            $status
        );
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
                <input type="hidden" name="requestor_id" value="<?= $editData["requestor_id"]; ?>">


                <!-- Name -->
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($editData["requestor_name"]); ?>" required>
                </div>
               
                <!-- Email -->
                <div class="form-group">
                    <label for="email">Your Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($editData["requestor_email"]); ?>" required>
                </div>
               
                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Your Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($editData["requestor_phone"]); ?>" required>
                </div>


                <!-- Delivery Option Dropdown -->
                <div class="form-group">
                    <label for="delivery_option">Delivery Option</label>
                    <select name="delivery_option" id="delivery_option" class="form-control" required>
                        <option value="" disabled>Select Delivery Option</option>
                        <option value="Pick Up" <?= ($editData["delivery_option"] == "Pick Up") ? 'selected' : ''; ?>>Pick Up</option>
                        <option value="Drop Off" <?= ($editData["delivery_option"] == "Drop Off") ? 'selected' : ''; ?>>Drop Off</option>
                    </select>
                </div>


                <!-- Special Notes Textarea -->
                <div class="form-group">
                    <label for="special_notes">Special Notes</label>
                    <textarea name="special_notes" id="special_notes" class="form-control"><?= htmlspecialchars($editData["special_notes"]); ?></textarea>
                </div>


                <!-- Submit Button -->
                <div class="form-buttons">
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update" class="btn btn-primary">Update Request</button>
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
                allowOutsideClick: false,  // Prevent click outside to close
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
require_once "footer.php";
?>

<?php
require_once "layout/header.php";
require_once "db_connection.php";
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

// Handle edit request
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $donationManager->getDonationById($_GET['edit']);
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $conditions = isset($_POST["condition"]) ? implode(",", $_POST["condition"]) : ''; // Handle conditions
    $donationManager->updateDonation(
        $id,
        $_POST["name"],
        $_POST["email"],
        $_POST["address"],
        $_POST["phone"],
        $_POST["ProductsType"],
        $_POST["quantity"],
        $conditions, // Save as a comma-separated string
        $_POST["delivery_option"],
        $_POST["message"]
    );
    header("Location: dashboard.php");
    exit();
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
</head>
<body>
<div class="donation-form-container">
    <div class="donor-info">
    <h2>Edit Donation</h2>
    <?php if ($editData): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $editData["id"]; ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= $editData["name"]; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= $editData["email"]; ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" id="address" class="form-control" value="<?= $editData["address"]; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="<?= $editData["phone"]; ?>" required>
            </div>
            <div class="form-group">
                <label for="ProductsType">Products Type</label>
                <select name="ProductsType" id="productsType" class="form-control" required>
                    <option value="" disabled>Select Category</option>
                    <option value="Beverages" <?= ($editData["products_type"] == "Beverages") ? 'selected' : ''; ?>>Beverages</option>
                    <option value="Canned Goods" <?= ($editData["products_type"] == "Canned Goods") ? 'selected' : ''; ?>>Canned Goods</option>
                    <option value="Fresh Produce" <?= ($editData["products_type"] == "Fresh Produce") ? 'selected' : ''; ?>>Fresh Produce</option>
                    <option value="Packaged Meals" <?= ($editData["products_type"] == "Packaged Meals") ? 'selected' : ''; ?>>Packaged Meals</option>
                    <option value="Household Care" <?= ($editData["products_type"] == "Household Care") ? 'selected' : ''; ?>>Household Care</option>
                    <option value="Personal Care" <?= ($editData["products_type"] == "Personal Care") ? 'selected' : ''; ?>>Personal Care</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="<?= $editData["quantity"]; ?>" required>
            </div>
            <div class="form-group">
                <label>Products Condition</label><br>
                <input type="checkbox" name="condition[]" value="Unopened" <?= (strpos($editData["products_condition"], "Unopened") !== false) ? 'checked' : ''; ?>> Unopened
                <input type="checkbox" name="condition[]" value="Properly Packaged" <?= (strpos($editData["products_condition"], "Properly Packaged") !== false) ? 'checked' : ''; ?>> Properly Packaged
                <input type="checkbox" name="condition[]" value="Within Expiry Date" <?= (strpos($editData["products_condition"], "Within Expiry Date") !== false) ? 'checked' : ''; ?>> Within Expiry Date
            </div>

            <div class="form-group">
                <label>Delivery Option</label><br>
                <input type="radio" name="delivery_option" value="Pick-up" <?= ($editData["delivery_option"] == "Pick-up") ? 'checked' : ''; ?> required> Pick-up
                <input type="radio" name="delivery_option" value="Drop-off" <?= ($editData["delivery_option"] == "Drop-off") ? 'checked' : ''; ?> required> Drop-off
            </div>
            <div class="form-group">
                <label for="message">Message (Optional)</label>
                <textarea name="message" id="message" class="form-control" rows="4"><?= $editData["message"]; ?></textarea>
            </div>

            <button type="submit" name="update" class="btn-submit">Update</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>




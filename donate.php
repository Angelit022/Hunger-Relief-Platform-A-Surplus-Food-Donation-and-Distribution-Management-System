<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
    exit;
}

$donationSuccess = false; // Flag to track donation success
$errorMessage = ''; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // (check if key exists in POST array)
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $foodType = isset($_POST['foodType']) ? $_POST['foodType'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $condition = isset($_POST['condition']) ? implode(", ", $_POST['condition']) : '';
    $pickupOption = isset($_POST['pickup']) ? $_POST['pickup'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';

   
    if (!preg_match("/^\d{11}$/", $phone)) {
        $errorMessage = "Error: Please enter a valid 11-digit phone number.";
    }
    elseif (empty($phone) || empty($foodType) || empty($quantity) || empty($pickupOption)) {
        $errorMessage = "Error: Please fill in all required fields (Phone, Food Type, Quantity, Pick-up Option).";
    } else {
        // Create a new instance of the Database class and DonationManager
        $database = new Database();
        $conn = $database->getConnection();
        $donationManager = new DonationManager($conn);

        if ($donationManager->addDonation($_SESSION["user_id"], $phone, $foodType, $quantity, $condition, $pickupOption, $message)) {
            $donationSuccess = true; // Set the flag if donation is tama
        } else {
            $errorMessage = "Error: Could not save your donation details. Please try again later.";
        }

        $database->closeConnection();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Food to Help Those in Need</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="donation-form-container">
    <div class="donor-info">
        <h2>Donate Food to Help Those in Need</h2>
        <link rel="stylesheet" href="styles.css">

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="foodType">Product Type</label>
                <select name="foodType" id="foodType" class="form-control" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Beverages" <?php echo isset($_POST['foodType']) && $_POST['foodType'] == 'Beverages' ? 'selected' : ''; ?>>Beverages</option>
                    <option value="Canned Goods" <?php echo isset($_POST['foodType']) && $_POST['foodType'] == 'Canned Goods' ? 'selected' : ''; ?>>Canned Goods</option>
                    <option value="Fresh Produce" <?php echo isset($_POST['foodType']) && $_POST['foodType'] == 'Fresh Produce' ? 'selected' : ''; ?>>Fresh Produce</option>
                    <option value="Packaged Meals" <?php echo isset($_POST['foodType']) && $_POST['foodType'] == 'Packaged Meals' ? 'selected' : ''; ?>>Packaged Meals</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Condition of Food</label><br>
                <input type="checkbox" name="condition[]" value="Unopened" <?php echo isset($_POST['condition']) && in_array('Unopened', $_POST['condition']) ? 'checked' : ''; ?>> Unopened
                <input type="checkbox" name="condition[]" value="Properly Packaged" <?php echo isset($_POST['condition']) && in_array('Properly Packaged', $_POST['condition']) ? 'checked' : ''; ?>> Properly Packaged
                <input type="checkbox" name="condition[]" value="Within Expiry Date" <?php echo isset($_POST['condition']) && in_array('Within Expiry Date', $_POST['condition']) ? 'checked' : ''; ?>> Within Expiry Date
            </div>
            <div class="form-group">
                <label>Pick-up or Drop-off Option</label><br>
                <input type="radio" name="pickup" value="Pick-up" <?php echo isset($_POST['pickup']) && $_POST['pickup'] == 'Pick-up' ? 'checked' : ''; ?> required> Pick-up
                <input type="radio" name="pickup" value="Drop-off" <?php echo isset($_POST['pickup']) && $_POST['pickup'] == 'Drop-off' ? 'checked' : ''; ?> required> Drop-off
            </div>
            <div class="form-group">
                <label for="message">Message (Optional)</label>
                <textarea name="message" id="message" class="form-control" rows="4"><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>
            </div>
            <div class="form-buttons">
                <a href="donate_page.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit Donation</button>
            </div>
        </form>
    </div>
</div>

<script>
    <?php if ($donationSuccess): ?>
        Swal.fire({
            title: 'Thank You!',
            text: 'Check it out! Your donation has been successfully recorded in Donor List',
            confirmButtonText: 'Ok',
            imageUrl: 'images/thankyou.jpg',
            imageWidth: 400,
            imageHeight: 200,
            imageAlt: 'Donation Image',
            willClose: () => {
                window.location.href = 'donate_page.php'; 
            }
        });
    <?php endif; ?>
</script>   

</body>
</html>

<?php
require_once "layout/footer.php";
?>


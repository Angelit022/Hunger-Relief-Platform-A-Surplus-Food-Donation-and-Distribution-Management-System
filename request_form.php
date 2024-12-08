<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/RequestManager.php";
require_once "classes/DonationManager.php";

$database = new Database();
$conn = $database->getConnection();
$requestManager = new RequestManager($conn);
$donationManager = new DonationManager($conn);

// Get donation_id from URL
$donation_id = isset($_GET['donation_id']) ? intval($_GET['donation_id']) : null;

// Fetch donation details if donation_id is present
$donation = null;
if ($donation_id) {
    $donation = $donationManager->getLatestDonationById($donation_id);
}

// Fetch user data from session
$user_id = $_SESSION['user_id'];
$requestor_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; 
$requestor_email = $_SESSION['email']; 

$error_message = '';

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $delivery_option = isset($_POST['delivery_option']) ? $_POST['delivery_option'] : '';
    $notes = isset($_POST['special_notes']) ? $_POST['special_notes'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;


    if (!preg_match("/^[0-9\s\-\+]*$/", $phone)) {
        $error_message = "Invalid phone number format. Please enter a valid phone number.";
    }

    elseif ($quantity <= 0) {
        $error_message = "Quantity must be a positive number.";
    }

    elseif ($quantity > $donation['quantity']) {
        $error_message = "The requested quantity exceeds the available donation quantity.";
    }
    else {
      
        $success = $requestManager->addRequest(
            $user_id,
            $donation_id,
            $requestor_name,
            $requestor_email,
            $phone,
            $delivery_option,
            $notes,
            $quantity,
            $donation['products_type'],
            $donation['products_condition'],
            $address
        );

        if ($success) {
         
            $quantityUpdateSuccess = $requestManager->updateDonationQuantity($donation_id, $quantity);
            
            if ($quantityUpdateSuccess) {
                $message = 'Your request has been successfully recorded and the quantity has been updated.';
                if ($quantity >= $donation['quantity']) {
                    $message .= ' The donation is now out of stock.';
                }
           
                echo "<script>
                        Swal.fire({
                            title: 'Request Submitted!',
                            text: '$message',
                            icon: 'success',
                            confirmButtonText: 'Close',
                            willClose: () => {
                                window.location.href = 'dashboard.php';
                            }
                        });
                      </script>";
            } else {
              
                echo "<script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'Your request was submitted, but there was an issue updating the quantity. Please contact support.',
                            icon: 'warning',
                            confirmButtonText: 'Close'
                        });
                      </script>";
            }
        } else {
          
            echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an issue submitting your request. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'Close'
                    });
                  </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="donation-form-container">

    <?php if ($donation): ?>
        <div class="donation-details-box" style="width: 30%; max-width: 700px; padding: 25px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 30px; transition: transform 0.3s ease, box-shadow 0.3s ease; margin-top: 20px; border: 1px solid #ccc;">
            <h3 style="color: #007bff; font-size: 1.6rem; margin-bottom: 15px; text-align: center; font-weight: bold;">Donation Details</h3>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Donor Name:</strong> <?php echo htmlspecialchars($donation['donor_name']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Donor Address:</strong> <?php echo htmlspecialchars($donation['donor_address']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Product Type:</strong> <?php echo htmlspecialchars($donation['products_type']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Available Quantity:</strong> 
                <?php 
                if ($donation['quantity'] > 0) {
                    echo htmlspecialchars($donation['quantity']);
                } else {
                    echo '<span class="badge bg-secondary">Out of Stock</span>';
                }
                ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Delivery Option:</strong> <?php echo htmlspecialchars($donation['delivery_option']); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error_message ?>
        </div>
    <?php endif; ?>
    
    <div class="donor-info">
        <h2>Request Form</h2>

        <form method="POST" <?= $donation['quantity'] == 0 ? 'onsubmit="return false;"' : '' ?>>
            <input type="hidden" name="donation_id" value="<?= $donation['id']; ?>">

            <div class="form-group">
                <label for="phone" class="form-label">Your Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Your Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="quantity" class="form-label">Quantity Needed</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?= isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : ''; ?>" min="1" max="<?= $donation['quantity'] ?>" required>
                <small class="form-text text-muted">Available quantity: <?= $donation['quantity'] ?></small>
            </div>

            <div class="form-group">
                <label for="delivery_option" class="form-label">Delivery Option</label>
                <select class="form-control" id="delivery_option" name="delivery_option" required>
                    <option value="">Select delivery option</option>
                    <option value="pickup" <?= (isset($_POST['delivery_option']) && $_POST['delivery_option'] == 'pickup') ? 'selected' : ''; ?>>Pickup</option>
                    <option value="delivery" <?= (isset($_POST['delivery_option']) && $_POST['delivery_option'] == 'delivery') ? 'selected' : ''; ?>>Drop-off</option>
                </select>
            </div>

            <div class="form-group">
                <label for="special_notes" class="form-label">Special Notes</label>
                <textarea class="form-control" id="special_notes" name="special_notes"><?= isset($_POST['special_notes']) ? htmlspecialchars($_POST['special_notes']) : ''; ?></textarea>
            </div>

            <div class="form-buttons">
                <a href="search.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit" <?= $donation['quantity'] == 0 ? 'disabled' : '' ?>>
                    <?= $donation['quantity'] == 0 ? 'Out of Stock' : 'Submit Request' ?>
                </button>
            </div>
        </form>

    </div>
</div>

</body>
</html>

<?php
require_once "layout/footer.php";
?>


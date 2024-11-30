<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/RequestManager.php";

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();
$requestManager = new RequestManager($conn);

// Get donation_id from URL
$donation_id = isset($_GET['donation_id']) ? intval($_GET['donation_id']) : null;

// Fetch donation details if donation_id is present
$donation = null;
if ($donation_id) {
    $stmt = $conn->prepare("SELECT * FROM donations WHERE id = ?");
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $donation = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $delivery_option = $_POST['delivery_option'];
    $notes = $_POST['special_notes'];

    // Ensure donation_id is provided
    if (!$donation_id) {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Donation ID is missing. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Close'
                });
              </script>";
        exit;
    }

    // Check if the user has already made a request for the specific donation_id
    $stmt = $conn->prepare("SELECT COUNT(*) FROM donation_requests WHERE donation_id = ? AND requestor_email = ?");
    $stmt->bind_param("is", $donation_id, $email);
    $stmt->execute();
    $stmt->bind_result($existing_request_count);
    $stmt->fetch();
    $stmt->close();

    if ($existing_request_count > 0) {
        // Show SweetAlert if the user has already requested the specific donation
        echo "<script>
                Swal.fire({
                    title: 'Request Already Submitted!',
                    text: 'You have already submitted a request for this donation.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    willClose: () => {
                        window.location.href = 'donation_list.php';
                    }
                });
              </script>";
    } else {
        // Proceed with adding the request
        $success = $requestManager->addRequest($name, $email, $phone, $delivery_option, $notes, $donation_id);

        if ($success) {
            // Display success message and trigger SweetAlert
            echo "<script>
                    Swal.fire({
                        title: 'Request Submitted!',
                        text: 'Your request has been successfully recorded.',
                        icon: 'success',
                        confirmButtonText: 'Close',
                        willClose: () => {
                            window.location.href = 'donation_list.php';
                        }
                    });
                  </script>";
        } else {
            // If an error occurs during request submission
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

<!-- Donation Form Container -->
<div class="donation-form-container">
    <!-- Donation Details Box -->
    <?php if ($donation): ?>
        <div class="donation-details-box" style="width: 30%; max-width: 700px; padding: 25px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 30px; transition: transform 0.3s ease, box-shadow 0.3s ease; margin-top: 20px; border: 1px solid #ccc;">
            <h3 style="color: #007bff; font-size: 1.6rem; margin-bottom: 15px; text-align: center; font-weight: bold;">Donation Details</h3>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Donor Name:</strong> <?php echo htmlspecialchars($donation['name']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Product Type:</strong> <?php echo htmlspecialchars($donation['products_type']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Quantity:</strong> <?php echo htmlspecialchars($donation['quantity']); ?>
            </p>
            <p style="font-size: 1.1rem; margin: 10px 0; text-align: center;">
                <strong style="color: #343a40; font-weight: bold;">Delivery Option:</strong> <?php echo htmlspecialchars($donation['delivery_option']); ?>
            </p>
        </div>
    <?php endif; ?>
    <div class="donor-info">
        <h2>Request Form</h2>

        <form method="POST">
            <input type="hidden" name="donation_id" value="<?= $donation_id; ?>"> <!-- Hidden field for donation_id -->
            <div class="form-group">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Your Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">Your Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="delivery_option" class="form-label">Delivery Option</label>
                <select class="form-control" id="delivery_option" name="delivery_option" required>
                    <option value="" disabled selected>Select Delivery Option</option>
                    <option value="Pick Up">Pick Up</option>
                    <option value="Drop Off">Drop Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="special_notes" class="form-label">Special Notes</label>
                <textarea class="form-control" id="special_notes" name="special_notes"></textarea>
            </div>
            <div class="form-buttons">
                <a href="donation_list.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>

    </div>
</div>

</body>
</html>

<?php
require_once "layout/footer.php";

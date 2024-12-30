<?php
require_once 'adminSidebar.php'; 
require_once '../classAdmin/adminClass.php';

$admin = new AdminClass();
$error_message = '';      //instanciating
$success_message = '';

$first_name_error = '';
$last_name_error = '';
$email_error = '';
$phone_error = '';
$address_error = '';

// Fetch user details for editing
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $user = $admin->getUserById($user_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));

        $valid = true;

        // Validatation
        if (empty($first_name)) {
            $first_name_error = 'First name is required.';
            $valid = false;
        }

        if (empty($last_name)) {
            $last_name_error = 'Last name is required.';
            $valid = false;
        }
        if (empty($email)) {
            $email_error = 'Email is required.';
            $valid = false;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = 'Please enter a valid email.';
            $valid = false;
        }
        if (empty($phone)) {
            $phone_error = 'Phone number is required.';
            $valid = false;
        } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
            $phone_error = 'Please enter a valid phone number (11 digits).';
            $valid = false;
        }
        if (empty($address)) {
            $address_error = 'Address is required.';
            $valid = false;
        }

        // If validation passes, update user
        if ($valid) {
            $update_status = $admin->updateUser($user_id, $first_name, $last_name, $email, $phone, $address);

            if ($update_status) {
                $success_message = "User details updated successfully!";
            } else {
                $error_message = "Failed to update user details. Please try again.";
            }
        }
    }
} else {
    $error_message = "Invalid user ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminStyles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.js"></script>
</head>
<body>

<div class="containers mt-4">
    <h2>Edit User Information</h2>


    <?php if ($error_message): ?>
        <script>
            Swal.fire({
                title: 'Error!',
                text: "<?php echo $error_message; ?>",
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manageUser.php';  
                }
            });
        </script>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <script>
            Swal.fire({
                title: 'Success!',
                text: "<?php echo $success_message; ?>",
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manageUser.php';  
                }
            });
        </script>
    <?php endif; ?>

        <form method="POST">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control <?php echo $first_name_error ? 'is-invalid' : ''; ?>" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            <div class="invalid-feedback">
                <?php echo $first_name_error; ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control <?php echo $last_name_error ? 'is-invalid' : ''; ?>" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            <div class="invalid-feedback">
                <?php echo $last_name_error; ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?php echo $email_error ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <div class="invalid-feedback">
                <?php echo $email_error; ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control <?php echo $phone_error ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            <div class="invalid-feedback">
                <?php echo $phone_error; ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control <?php echo $address_error ? 'is-invalid' : ''; ?>" id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            <div class="invalid-feedback">
                <?php echo $address_error; ?>
            </div>
        </div>

    <div class="form-buttons">
        <a href="manageUser.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>

</body>
</html>

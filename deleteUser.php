<?php
require_once '../classAdmin/adminClass.php';

$admin = new AdminClass();
$error_message = '';
$success_message = '';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Call delete method in the AdminClass
    $delete_status = $admin->deleteUser($user_id);

    if ($delete_status) {
        // Success Message
        $success_message = "User deleted successfully!";
    } else {
        // Error Message
        $error_message = "Failed to delete user. Please try again.";
    }
} else {
    $error_message = "Invalid user ID.";
}

// Include SweetAlert script in the response
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>';

if ($error_message) {
    echo "<script>
        Swal.fire({
            title: 'Error!',
            text: '$error_message',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the manageUser page after confirmation
                window.location.href = 'manageUser.php';  
            }
        });
    </script>";
} elseif ($success_message) {
    echo "<script>
        Swal.fire({
            title: 'Success!',
            text: '$success_message',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reload the page to reflect changes after successful deletion
                window.location.href = 'manageUser.php';  
            }
        });
    </script>";
}

echo '</body>
</html>';
?>

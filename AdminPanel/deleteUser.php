<?php
require_once '../classAdmin/adminClass.php';

$admin = new AdminClass();
$error_message = '';
$success_message = '';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        // Call delete method in the AdminClass
        $delete_status = $admin->deleteUser($user_id);

        if ($delete_status) {
            $success_message = "User deleted successfully!";
        } else {
            $error_message = "Failed to delete user. Please try again.";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
} else {
    $error_message = "Invalid user ID.";
}

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
            text: '" . addslashes($error_message) . "',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'manageUser.php';
            }
        });
    </script>";
} elseif ($success_message) {
    echo "<script>
        Swal.fire({
            title: 'Success!',
            text: '" . addslashes($success_message) . "',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'manageUser.php';
            }
        });
    </script>";
}

echo '</body>
</html>';
?>


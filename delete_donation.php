<?php
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancel_id"])) {
    $database = new Database();
    $conn = $database->getConnection();
    $donationManager = new DonationManager($conn);

    $id = htmlspecialchars(trim($_POST["cancel_id"]));

    if ($donationManager->deleteDonation($id)) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>SweetAlert</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
        Swal.fire({
            title: 'Success!',
            text: 'Donation canceled successfully!',
            icon: 'success'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'dashboard.php';
            }
        });
        </script>
        </body>
        </html>";
    } else {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>SweetAlert</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
        Swal.fire({
            title: 'Error!',
            text: 'Failed to cancel the donation!',
            icon: 'error'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'dashboard.php';
            }
        });
        </script>
        </body>
        </html>";
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>

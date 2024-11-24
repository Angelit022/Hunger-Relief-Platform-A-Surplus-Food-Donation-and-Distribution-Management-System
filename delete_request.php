<?php
require_once "db_connection.php";
require_once "RequestManager.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $database = new Database();
    $conn = $database->getConnection();
    $requestManager = new RequestManager($conn); 

    $id = htmlspecialchars(trim($_POST["id"]));

    if ($requestManager->deleteRequest($id)) { 
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
            text: 'Request canceled successfully!',
            icon: 'success'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'dashboard.php'; // Redirect to dashboard after success
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
            text: 'Failed to cancel the request!',
            icon: 'error'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'dashboard.php'; // Redirect to dashboard if failed
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

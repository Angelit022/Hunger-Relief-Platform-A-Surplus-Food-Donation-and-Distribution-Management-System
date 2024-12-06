<?php
require_once "layout/header.php";
require_once "classes/user_service.php";

if (isset($_SESSION["registration_success"]) && $_SESSION["registration_success"] === true) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            position: 'top-center',
            icon: 'success',
            title: 'Registration successful! Welcome, " . htmlspecialchars($_SESSION['first_name']) . "!',
            showConfirmButton: false,
            timer: 2500
        });
    </script>";
    unset($_SESSION["registration_success"]); 
}

// Redirect if user is already logged in
if (isset($_SESSION["email"])) {
    // Check if the 'role' key is set in the session before using it
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'donation_admin') {
            if (basename($_SERVER['PHP_SELF']) !== 'manageDonation.php') {
                header("location: AdminPanel/manageDonation.php");
                exit;
            }
        } elseif ($_SESSION['role'] === 'request_admin') {
            if (basename($_SERVER['PHP_SELF']) !== 'manageRequest.php') {
                header("location: AdminPanel/manageRequest.php"); 
                exit;
            }
        } elseif ($_SESSION['role'] === 'user_admin') {
            if (basename($_SERVER['PHP_SELF']) !== 'manageUser.php') {
                header("location: AdminPanel/manageUser.php"); 
                exit;
            }
        } else {
            if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
                header("location: index.php"); 
                exit;
            }
        }
    }
}
?>

<div style="background-image: url('images/backgroundhomepage.jpg'); background-size: cover; background-position: center; height: 100vh;">
  <div class="container text-white py-5">
    <div class="row align-items-center g-5">
      <div class="col-md-6">
        <h1 class="mb-5" style="font-size: 5rem;"><strong>Hunger Relief Platform</strong></h1>
        <p>A Surplus Food Donation and Distribution Management System</p>
      </div>
      <div class="col-md-6 text-center">
        <!-- Add any content if needed -->
      </div>
    </div>
  </div>
</div>

<?php
require_once "layout/footer.php";
?>

<?php
require_once "layout/header.php";

// Check if registration was successful
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
    unset($_SESSION["registration_success"]); // Clear session variable after displaying the alert
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
      </div>
    </div>
  </div>
</div>

</body>
</html>

<?php
require_once "layout/footer.php";
?>

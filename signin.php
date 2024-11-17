<?php
require_once "header.php";
require_once "user_service.php";

if (isset($_SESSION["email"])) {
    header("location: index.php");
    exit;
}

$userService = new UserService();

$first_name = $last_name = $email = $phone = $address = $password = $confirm_password = "";
$first_name_error = $last_name_error = $email_error = $phone_error = $address_error = $password_error = $confirm_password_error = "";
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($first_name)) {
        $first_name_error = "First name is required";
        $error = true;
    }
    if (empty($last_name)) {
        $last_name_error = "Last name is required";
        $error = true;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Email format is not valid";
        $error = true;
    } elseif ($userService->loginUser($email, $password)) { // Check if email is already used
        $email_error = "Email is already used";
        $error = true;
    }
    if (!preg_match("/^[0-9]{11}$/", $phone)) {
        $phone_error = "Phone number must be exactly 11 digits";
        $error = true;
    }
    if (strlen($password) < 6) {
        $password_error = "Password must be at least 6 characters long.";
        $error = true;
    }
    if ($password !== $confirm_password) {
        $confirm_password_error = "Passwords do not match.";
        $error = true;
    }

    // If no errors, proceed to register user
    if (!$error) {
      $user_id = $userService->registerUser($first_name, $last_name, $email, $phone, $address, $password);

      // Store user session data
      $_SESSION["user_id"] = $user_id;
      $_SESSION["first_name"] = $first_name;
      $_SESSION["last_name"] = $last_name;
      $_SESSION["email"] = $email;
      $_SESSION["phone"] = $phone;
      $_SESSION["address"] = $address;
      $_SESSION["created_at"] = date('Y-m-d H:i:s');

      // Set success message in session
      $_SESSION["registration_success"] = true;

      // Redirect to homepage
      header("Location: index.php");
      exit();
    }

}
?>


<div class="container py-5">
  <div class="row">
    <div class="col-lg-6 mx-auto border shadow p-4">
      <h2 class="text-center mb-4">Register</h2>
      <hr />

      <form method="post">
        <div class="mb-3 row">
          <label for="first_name" class="col-sm-4 col-form-label">First Name</label>
          <div class="col-sm-8">
            <input class="form-control" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
            <span class="text-danger"><?= htmlspecialchars($first_name_error) ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="last_name" class="col-sm-4 col-form-label">Last Name</label>
          <div class="col-sm-8">
            <input class="form-control" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
            <span class="text-danger"><?= htmlspecialchars($last_name_error) ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="email" class="col-sm-4 col-form-label">Email</label>
          <div class="col-sm-8">
            <input class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
            <span class="text-danger"><?= htmlspecialchars($email_error) ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="phone" class="col-sm-4 col-form-label">Phone</label>
          <div class="col-sm-8">
            <input class="form-control" name="phone" value="<?= htmlspecialchars($phone) ?>">
            <span class="text-danger"><?= htmlspecialchars($phone_error) ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="address" class="col-sm-4 col-form-label">Address</label>
          <div class="col-sm-8">
            <input class="form-control" name="address" value="<?= htmlspecialchars($address) ?>">
            <span class="text-danger"><?= htmlspecialchars($address_error) ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="password" class="col-sm-4 col-form-label">Password</label>
          <div class="col-sm-8">    
            <input class="form-control" type="password" name="password">
            <span class="text-danger"><?= htmlspecialchars($password_error) ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="confirm_password" class="col-sm-4 col-form-label">Confirm Password</label>
          <div class="col-sm-8">
            <input class="form-control" type="password" name="confirm_password">
            <span class="text-danger"><?= htmlspecialchars($confirm_password_error) ?></span>
          </div>
        </div>

        <div class="row mb-3">
          <div class="offset-sm-4 col-sm-4 d-grid">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
          <div class="col-sm-4 d-grid">
            <a href="index.php" class="btn btn-outline-primary">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
// Check if registration was successful
if (isset($_SESSION["registration_success"]) && $_SESSION["registration_success"] === true) {
    // Display SweetAlert message
    echo "<script>
        Swal.fire({
            position: 'top-center',
            icon: 'success',
            title: 'Registration successful! Welcome, " . htmlspecialchars($_SESSION['first_name']) . "!',
            showConfirmButton: false,
            timer: 2500
        });
    </script>";

    // Clear the session variable after showing the message
    unset($_SESSION["registration_success"]);
}
?>

<?php
require_once "footer.php";
?>
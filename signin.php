<?php
include "layout/header.php";

//logged in users are directed to the homepage
if(isset($_SESSION["email"])){
    header("location: index.php");
    exit;
}
 
// Initialize variables
$first_name = "";
$last_name = "";
$email = "";
$phone = "";
$address = "";
$password = "";
$confirm_password = "";

$first_name_error = "";
$last_name_error = "";
$email_error = "";
$phone_error = "";
$address_error = "";
$password_error = "";
$confirm_password_error = "";

// Initialize error flag
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate first_name
    if(empty($first_name)){
        $first_name_error = "First name is required";
        $error = true;
    }

    // Validate last_name
    if(empty($last_name)){
        $last_name_error = "Last name is required";
        $error = true;
    }

    // Check email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $email_error = "Email format is not valid";
        $error = true;
    }

    include "db_connection.php";
    $dbConnection = getDatabaseConnection();

    $statement = $dbConnection->prepare("SELECT user_id FROM users WHERE email = ?");

    // Bind variables to the prepared statement as parameters
    $statement->bind_param("s", $email);

    // Execute statement
    $statement->execute();

    // Check if email is already in the database
    $statement->store_result();
    if($statement->num_rows > 0){
        $email_error = "Email is already used";
        $error = true;
    }

    // Close the statement
    $statement->close();

    // Check if phone number is valid
    if (!preg_match("/^[0-9]{11}$/", $phone)) {
        $phone_error = "Phone number must be exactly 11 digits";
        $error = true;
    }

    // Check if password is at least 6 characters long
    if (strlen($password) < 6) {
        $password_error = "Password must be at least 6 characters long.";
        $error = true;
    }

    // Check if password confirmation matches the original password
    if ($password !== $confirm_password) {
        $confirm_password_error = "Password and confirmation password do not match.";
        $error = true;
    }

    if (!$error) {
        // Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Get current timestamp for 'created_at'
        $created_at = date('Y-m-d H:i:s');

        // Use prepared statements to avoid SQL injection attacks
        $statement = $dbConnection->prepare(
            "INSERT INTO users (first_name, last_name, email, phone, address, password, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        // Bind variables to the prepared statement as parameters
        $statement->bind_param('sssssss', $first_name, $last_name, $email, $phone, $address, $password, $created_at);

        // Execute the statement
        $statement->execute();

        // Get the inserted ID
        $insert_id = $statement->insert_id;

        // Close the prepared statement
        $statement->close();

        // Save session data
        $_SESSION["user_id"] = $insert_id;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;
        $_SESSION["email"] = $email;
        $_SESSION["phone"] = $phone;
        $_SESSION["address"] = $address;
        $_SESSION["created_at"] = $created_at;

        // Redirect user to the home page
        header("Location: index.php");
        exit();  // Make sure to call exit() after header to stop further script execution
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
            <input class="form-control" name="first_name" value="<?= $first_name ?>">
            <span class="text-danger"><?= $first_name_error ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="last_name" class="col-sm-4 col-form-label">Last Name</label>
          <div class="col-sm-8">
            <input  class="form-control"  name="last_name" value="<?= $last_name ?>">
            <span class="text-danger"><?= $last_name_error ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="email" class="col-sm-4 col-form-label">Email</label>
          <div class="col-sm-8">
            <input  class="form-control"  name="email" value="<?= $email ?>">
            <span class="text-danger"><?= $email_error ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="phone" class="col-sm-4 col-form-label">Phone</label>
          <div class="col-sm-8">
            <input  class="form-control"  name="phone" value="<?= $phone ?>">
            <span class="text-danger"><?= $phone_error ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="address" class="col-sm-4 col-form-label">Address</label>
          <div class="col-sm-8">
            <input  class="form-control" name="address" value="<?= $address ?>">
            <span class="text-danger"><?= $address_error ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="password" class="col-sm-4 col-form-label">Password</label>
          <div class="col-sm-8">    
            <input  class="form-control"  name="password" value="<?= $password ?>">
            <span class="text-danger"><?= $password_error ?></span>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="confirm-password" class="col-sm-4 col-form-label">Confirm Password</label>
          <div class="col-sm-8">
          <input class="form-control" name="confirm_password" value="<?= $confirm_password ?>">
            <span class="text-danger"><?= $confirm_password_error ?></span>
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
include "layout/footer.php";
?>

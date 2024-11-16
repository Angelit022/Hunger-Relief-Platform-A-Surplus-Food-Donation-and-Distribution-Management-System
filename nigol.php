<?php

include "layout/header.php";

// Logged-in users are directed to the homepage
if (isset($_SESSION["email"])) {
    header("location: index.php");
    exit;
}

$email = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and Password are required!";
    } else {
        include "db_connection.php";
        $dbConnection = getDatabaseConnection();

        $statement = $dbConnection->prepare
        ("SELECT user_id, first_name, last_name, email, phone, address, password, created_at FROM users WHERE email = ?");

        // Bind the email variable to the prepared statement as a parameter
        $statement->bind_param("s", $email);

        // Execute the statement
        $statement->execute();  

        // Bind result variables
        $statement->bind_result($user_id, $first_name, $last_name, $email, $phone, $address, $stored_password, $created_at);

        // Fetch values
        if ($statement->fetch()) { 
            if (password_verify($password, $stored_password)) {
                // Password is correct, set up session data
                $_SESSION["user_id"] = $user_id;
                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;
                $_SESSION["email"] = $email;
                $_SESSION["phone"] = $phone;
                $_SESSION["address"] = $address;
                $_SESSION["created_at"] = $created_at;

                // Redirect user to the home page
                header("Location: index.php");
                exit;
            }

        }
           
        $statement->close();
        $error = "Email or Password Invalid";
    }
}
?>

<div class="container py-5">
    <div class="col-md-6 col-lg-4 mx-auto border shadow-sm p-4 rounded">
        <h2 class="text-center mb-4">Login</h2>
        <hr />

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><?= $error ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" name="email" value="<?= $email ?>" />
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" />
            </div>

            <div class="row mb-3">
                <div class="col d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                <div class="col d-grid">
                    <a href="index.php" class="btn btn-outline-primary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
include "layout/footer.php";
?>

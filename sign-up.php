<?php
$confirmationMsg = 0;
$user = 0;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connection.php';
    $username = $_POST['username'];
    $password = $_POST['password'];


    // Handle sign-up
    if (isset($_POST['signup'])) {
        $sql = "SELECT * FROM signin WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $num = mysqli_num_rows($result);
            if ($num > 0) {
                $user = 1; // User already exists
            } else {
                $sql = "INSERT INTO signin (username, password) VALUES ('$username', '$password')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    $confirmationMsg = 1; // Successful registration
                } else {
                    die(mysqli_error($conn));
                }
            }
        }
    }
    // Handle login
    elseif (isset($_POST['login'])) {
        // Perform login logic here
        $sql = "SELECT * FROM signin WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            // Login successful, redirect or set session
            header('Location: welcome.php'); // Redirect to a welcome page
            exit();
        } else {
            $loginError = "Invalid username or password."; // Error message for login
        }
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 80px;
        }
        .title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }
        .footer-link {
            text-align: center;
            margin-top: 10px;
        }
        .footer-link a {
            color: #007bff;
            text-decoration: none;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }
        /* Hide login form by default */
        #login-form {
            display: none;
        }
    </style>
</head>
<body>


<?php
if ($user) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Oh no!.. </strong> User already exists!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}


if ($confirmationMsg) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!.. </strong> You have successfully registered!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}


if (isset($loginError)) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!.. </strong> ' . $loginError . '
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
?>


<h1 class="text-center mt-5 text-primary">" Welcome to Hunger Relief Platform "</h1>
<div class="container">
    <div class="form-container">
        <!-- Sign-Up Form -->
        <h2 class="title text-center mb-4" id="signup-title">Sign Up</h2>
        <form action="" method="post" id="signup-form">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Create Username</label>
                <input type="text" class="form-control" id="username" placeholder="Enter username" name="username" required>
            </div>
            <div class="form-group mb-4">
                <label for="password" class="form-label">Create Password</label>
                <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="signup">Submit</button>
        </form>


        <!-- Link to toggle login form -->
        <p class="footer-link" id="login-link">
            Already have an account? <a href="#" onclick="showLoginForm()">Login</a>
        </p>


        <!-- Login Form (hidden by default) -->
        <div id="login-form">
            <h2 class="title text-center mb-4">Login</h2>
            <form action="" method="post">
                <div class="form-group mb-3">
                    <label for="login-username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="login-username" placeholder="Enter username" name="username" required>
                </div>
                <div class="form-group mb-3">
                    <label for="login-password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="login-password" placeholder="Enter Password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
            </form>


            <!-- Forgot Password Link -->
            <p class="footer-link">
                <a href="forgot-password.php">Forgot Password?</a>
            </p>
        </div>
    </div>
</div>


<script>
    function showLoginForm() {
        document.getElementById('signup-form').style.display = 'none';
        document.getElementById('signup-title').style.display = 'none';
        document.getElementById('login-link').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
    }
</script>


</body>
</html>




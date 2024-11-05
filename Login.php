<?php
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connection.php'; // Ensure you have this file to connect to your database
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Handle login
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        // Prepare and execute the SQL statement to prevent SQL injection
        $sql = "SELECT * FROM signin WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $user = mysqli_fetch_assoc($result); // Fetch user data
            if ($user && password_verify($password, $user['password'])) { // Verify password
                // Login successful, redirect or set session
                header('Location: welcome.php'); // Redirect to a welcome page
                exit();
            } else {
                $loginError = "Invalid username or password."; // Error message for login
            }
        } else {
            die(mysqli_error($conn)); // Handle query error
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<title>Log in Page</title>
<style>
    body{
        background-color:whitesmoke

    }
    .container {
        margin-top: 150px;
    }
    input{
        max-width: 250px;
        min-width: 250px;
    }
</style>

</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-md-offset-3" align="center">
            <h2>Sign In</h2>
            <form action="login.php" method="POST">
                <input type="text" name="username" class="form-control" placeholder="Enter email"/><br/>
                <input type="password" name="password" class="form-control" placeholder="Enter password"/><br/>
                <input type="submit" value="Login" class="btn btn-success">
</form>
         </div>
    </div>
</div>
</body>

</html>

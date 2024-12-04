<?php
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connection.php'; 
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Handle login
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
      
        $sql = "SELECT * FROM signin WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $user = mysqli_fetch_assoc($result); 
            if ($user && password_verify($password, $user['password'])) { 
                // Login successful, redirect or set session
                header('Location: welcome.php'); 
                exit();
            } else {
                $loginError = "Invalid username or password."; 
            }
        } else {
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>Login Page</title>
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
 </style>
</head>
<body>

<h2 class="text-center mt-5 text-primary">Welcome to Hunger Relief Platform</h2>
<div class="container">
<div class="form-container">
<h2 class="title text-center mb-4">Login</h2>
 <form action="login.php" method="post">
<input type="hidden" name="action" value="login"> <!-- Hidden field for login action -->
 <div class="mb-3">
 <label for="username" class="form-label">Username:</label>
<input type="text" id="username" name="username" class="form-control" required>
 </div>
 <div class="mb-3">
 <label for="password" class="form-label">Password:</label>
 <input type="password" id="password" name="password" class="form-control" required>
 </div>
 <button type="submit" class="btn btn-primary w-100">Login</button>


  <!-- Display login error message -->
<?php 
    if (!empty($loginError)) {
        echo $loginError;
    }
 ?>

 </form>
 </div>
</div>

</body>
</html>
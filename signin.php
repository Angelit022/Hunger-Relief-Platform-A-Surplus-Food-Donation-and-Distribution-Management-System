<?php
$userExists = 0;
$errorMsg = '';
$errorClass = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connection.php';
    
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $message = trim($_POST['message']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Invalid email format.';
        $errorClass = 'alert-danger';
    } elseif (!preg_match('/^[0-9]{11}$/', $mobile)) {
        $errorMsg = 'Mobile number must be 11 digits.';
        $errorClass = 'alert-danger';
    } else {
        $sql = "SELECT * FROM `register` WHERE `email_address`='$email'";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $num = mysqli_num_rows($result);
            if ($num > 0) {
                $userExists = 1;
            } else {
                $sql = "INSERT INTO `register` (name, address, email_address, contact_number, message, password) VALUES ('$name', '$address', '$email', '$mobile', '$message', '$password')";
                $result = mysqli_query($conn, $sql);
                header("Refresh: 1; url=index.php");
                exit();
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>

<?php
if ($errorMsg) {
    echo '<div class="alert ' . $errorClass . ' alert-dismissible fade show fade-out" role="alert" style="animation-delay: 3s;">
    <strong>Error:</strong> ' . htmlspecialchars($errorMsg) . '
    </div>';
}

if ($userExists) {
    echo '<div class="alert alert-warning alert-dismissible fade show fade-out" role="alert" style="animation-delay: 3s;">
    <strong>Oh no!.. </strong> A user with this email already exists!
    </div>';
}
?>

<h1 class="text-center mt-5 text-primary">" Welcome to Hunger Relief Platform "</h1>
<div class="container">
    <div class="register-container">
        <h2 class="register-title text-center mb-4">Register</h2>
        <form action="signin.php" method="post"> 
            <div class="form-group mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" required>
            </div>
            <div class="form-group mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" placeholder="Enter address" name="address" required>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
            </div>
            <div class="form-group mb-3">
                <label for="mobile" class="form-label">Mobile Number</label>
                <input type="tel" class="form-control" id="mobile" placeholder="Enter mobile number" name="mobile" required>
            </div>  
            <div class="form-group mb-4">
                <label for="message" class="form-label">Reason / Type of Food (Optional)</label>
                <textarea class="form-control" id="message" placeholder="Enter reason or type of food" name="message"></textarea>
            </div>
            <div class="form-group mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>

</body>
</html>

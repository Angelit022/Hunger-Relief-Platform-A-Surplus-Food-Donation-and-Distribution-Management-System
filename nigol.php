<?php 
require_once "layout/header.php";
require_once "classes/user_service.php";

if (isset($_SESSION["email"])) {
    header("location: index.php");
    exit;
}

$email = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and Password are required!";
    } else {
        $userService = new UserService();
        $user = $userService->loginUser($email, $password);

        if ($user) {
            // Store user session data
            $_SESSION = $user;

            // SweetAlert success message after login
            echo "<script>
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: 'Successfully logged in!',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = 'index.php';  // Redirect after alert closes
                    });
                  </script>";
            exit; // Ensure no further code is executed after the SweetAlert.
        } else {
            $error = "Email or Password Invalid";
        }
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
require_once "layout/footer.php";
?>

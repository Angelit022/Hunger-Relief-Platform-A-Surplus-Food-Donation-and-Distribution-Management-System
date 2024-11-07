<?php
session_start();

$authenticated = false;
if (isset($_SESSION["email"])) {
  $authenticated = true;
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hunger Relief Platform</title>
    <link rel="icon" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>

  <nav class="navbar navbar-expand-lg" style="background-color: #FFFFE0; border-bottom: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.png" width="40" height="40" class="d-inline-block align-top" alt=""> HRP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="index.php">Home</a>
                </li>

                <?php if ($authenticated): ?>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">Search Food</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">Donate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">Request</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">Dashboard</a>
                    </li>
                <?php endif; ?>
            </ul>

            <?php if ($authenticated): ?>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="index.php" id="profileDropdown" role="button" data-bs-toggle="dropdown" data-bs-target="#dropdown-menu" aria-expanded="false">
                                Your Profile
                            </a>
                        <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="signin.php" class="btn btn-outline-primary me-2">Register</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-primary">Login</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
  </nav>
  
  <!-- Bootstrap Bundle with Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN6jIeHz" crossorigin="anonymous"></script>

</body>
</html>

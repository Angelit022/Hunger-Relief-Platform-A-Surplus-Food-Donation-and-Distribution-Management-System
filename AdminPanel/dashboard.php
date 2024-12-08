<?php
session_start();

if (!isset($_SESSION['role']) || 
($_SESSION['role'] !== 'donation_admin' && $_SESSION['role'] !== 'request_admin' && $_SESSION['role'] !== 'user_admin')) {
    header("Location: Login.php");
    exit();
}

require_once "../classAdmin/adminClass.php";

// Get admin class instance
$adminDashboard = new AdminClass();

// Get data
$totalDonations = $adminDashboard->getTotalDonations();
$totalRequests = $adminDashboard->getTotalRequests();
$totalUsers = $adminDashboard->getTotalUsers();
$totalEmergencyRequests = $adminDashboard->getTotalEmergencyRequests();

// Get the current logged-in admin's role
$adminRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminStyles.css">
</head>
<body>
    <?php require_once "adminSidebar.php"; ?>

    <div class="main-content container mt-5">
        <h1 class="text-center">DASHBOARD</h1>
        <div class="row mt-5">
         
            <div class="col-md-3">
                <div class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-hand-holding-heart fa-3x text-primary"></i>
                            <h5 class="card-title mt-3">Total Donations</h5>
                            <p class="number fs-4"><?php echo $totalDonations; ?></p>
                        </div>
                    </div>
                </div>
            </div>
      
            <div class="col-md-3">
                <div class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-envelope-open-text fa-3x text-success"></i>
                            <h5 class="card-title mt-3">Total Requests</h5>
                            <p class="number fs-4"><?php echo $totalRequests; ?></p>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-3">
                <div href="manageUser.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-info"></i>
                            <h5 class="card-title mt-3">Total Users</h5>
                            <p class="number fs-4"><?php echo $totalUsers; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                            <h5 class="card-title mt-3">Emergency Requests</h5>
                            <p class="number fs-4"><?php echo $totalEmergencyRequests; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
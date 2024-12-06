<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if session variables are set
if (isset($_SESSION['role'])):
?>
<div class="sidebar">
    <h2>ADMIN PANEL</h2>
    
    <!-- Display role-specific message -->
    <?php if ($_SESSION['role'] === 'donation_admin'): ?>
        <p>Welcome ! <br>Donation Admin</p>
    <?php elseif ($_SESSION['role'] === 'request_admin'): ?>
        <p>Welcome ! <br>Request Admin</p>
    <?php elseif ($_SESSION['role'] === 'user_admin'): ?>
       <p>Welcome ! <br>User Admin</p>
    <?php endif; ?>
    
    <!-- Sidebar buttons -->
    <button onclick="location.href='dashboard.php'">Dashboard</button>

    <?php if ($_SESSION['role'] === 'donation_admin'): ?>
        <!-- Buttons for donation admin -->
        <button onclick="location.href='manageDonation.php'">Donations Manager</button>
    <?php elseif ($_SESSION['role'] === 'request_admin'): ?>
        <!-- Buttons for request admin -->
        <button onclick="location.href='manageRequest.php'">Requests Manager</button>
        <!-- Buttons for user admin -->
    <?php elseif ($_SESSION['role'] === 'user_admin'): ?>
        <button onclick="location.href='manageUser.php'">Users Manager</button>
        <button onclick="location.href='manageEmergency.php'">Emergency Manager</button>
    <?php endif ?>
    <!-- Logout button -->
    <button class="logout" onclick="location.href='../logout.php'">LOGOUT</button>
</div>
<?php endif; ?>

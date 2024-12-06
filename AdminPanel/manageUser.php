<?php 
require_once 'adminSidebar.php'; 
require_once '../classAdmin/adminClass.php';

$admin = new AdminClass();

$users = $admin->getUsers(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminStyles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="main-content container mt-4">
<h1 class="text-center">Users Manager</h1>

    <table id="userTable" class="table table-striped">
        <thead>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Created At</th>
                <th>User Activity</th> 
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <a href="viewUserAct.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-info">View</a>
                        </td>
                        <td>
                            <a href="editUser.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="deleteUser.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<script>
    $(document).ready(function() {
        $('#userTable').DataTable();
    });
</script>

</body>
</html>

<?php 
require_once 'adminSidebar.php'; 
require_once '../classes/emergencyRequest.php';
require_once '../classAdmin/actionHandler.php';

$emergencyRequest = new EmergencyRequest();
$actionHandler = new ActionHandler($emergencyRequest->getConnection());

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $table = 'emergency_requests';

    if ($action === 'accepted') {
        $result = $actionHandler->approveRecord($id, $table);
    } elseif ($action === 'reject') {
        $result = $actionHandler->rejectRecord($id, $table);
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$emergencyRequests = $emergencyRequest->getEmergencyRequests(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Emergency Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminStyles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="main-content container mt-4">
    <h1 class="text-center">Emergency Requests Manager</h1>

    <table id="emergencyTable" class="table table-striped">
        <thead>
            <tr>
                <th>Requestor ID</th>
                <th>User Name</th>
                <th>Email Address</th>
                <th>Location</th>
                <th>Report At</th>
                <th>Request Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emergencyRequests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                    <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                    <td>
                        <a href="https://www.google.com/maps?q=<?php echo $request['latitude']; ?>,<?php echo $request['longitude']; ?>" target="_blank" class="btn btn-sm btn-info">View Map</a>
                    </td>
                    <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                    <td>
                        <?php if ($request['status'] === 'pending'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                <input type="hidden" name="action" value="accepted">
                                <button type="submit" class="btn btn-sm btn-success">Accepted</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        <?php else: ?>
                            <?php echo ucfirst($request['status']); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#emergencyTable').DataTable();
    });
</script>

</body>
</html>
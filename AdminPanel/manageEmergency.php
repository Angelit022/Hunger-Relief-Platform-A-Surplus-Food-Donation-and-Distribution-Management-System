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

    $result = false;
    if ($action === 'accepted') {
        $result = $actionHandler->approveRecord($id, $table);
    } elseif ($action === 'rejected') {
        $result = $actionHandler->rejectRecord($id, $table);
    }
    
    $_SESSION['action_result'] = $result ? 'success' : 'error';
    $_SESSION['action_message'] = $result ? 'The request has been ' . $action : 'There was an error processing the request.';

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$emergencyRequests = $emergencyRequest->getEmergencyRequests(); 

// Check for new emergency requests
$newRequestCount = 0;
foreach ($emergencyRequests as $request) {
    if ($request['status'] === 'pending') {
        $newRequestCount++;
    }
}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="main-content container mt-4">
    <h1 class="text-center">Emergency Requests Manager</h1>

    <table id="emergencyTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Email Address</th>
                <th>Phone</th>
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
                    <td><?php echo htmlspecialchars($request['phone']); ?></td>
                    <td>
                        <a href="https://www.google.com/maps?q=<?php echo $request['latitude']; ?>,<?php echo $request['longitude']; ?>" target="_blank" class="btn btn-sm btn-info">View Map</a>
                    </td>
                    <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                    <td>
                        <?php if ($request['status'] === 'pending'): ?>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <form method="POST" class="action-form">
                                    <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                    <input type="hidden" name="action" value="accepted">
                                    <button type="submit" class="btn btn-sm btn-success">Accept</button>
                                </form>
                                <form method="POST" class="action-form">
                                    <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                    <input type="hidden" name="action" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <?php echo ucfirst($request['status']); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <h3>Emergency Platforms</h3>
        <button class="btn btn-danger" onclick="window.open('https://redcross.org.ph/', '_blank')">Red Cross Philippines</button>
        <button class="btn btn-info" onclick="window.open('https://www.worldvision.org.ph/support-program/', '_blank')">World Vision Support</button>
        <button class="btn btn-warning" onclick="window.open('https://www.who.int/philippines/', '_blank')">Word Health Organization</button>
        <button class="btn btn-success" onclick="window.open('https://foodbank.org.ph/about/', '_blank')">Food Bank Organization</button>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#emergencyTable').DataTable();

        <?php if ($newRequestCount > 0): ?>
        Swal.fire({
            title: 'New Emergency Requests',
            text: 'There are <?php echo $newRequestCount; ?> new emergency requests pending.',
            icon: 'info',
            confirmButtonText: 'OK'
        });
        <?php endif; ?>

       
        <?php if (isset($_SESSION['action_result'])): ?>
        Swal.fire({
            title: '<?php echo $_SESSION['action_result'] === 'success' ? 'Success!' : 'Error!'; ?>',
            text: '<?php echo $_SESSION['action_message']; ?>',
            icon: '<?php echo $_SESSION['action_result']; ?>',
            confirmButtonText: 'OK'
        });
        <?php
        // Clear the session variables
        unset($_SESSION['action_result']);
        unset($_SESSION['action_message']);
        endif;
        ?>

        $('.action-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var action = form.find('input[name="action"]').val();
            
            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to ' + action + ' this request?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, ' + action + ' it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.off('submit').submit();
                }
            });
        });
    });
</script>

</body>
</html>


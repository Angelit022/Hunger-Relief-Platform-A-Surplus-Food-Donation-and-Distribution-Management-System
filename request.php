<?php
require_once "header.php";
require_once "db_connection.php";
require_once "DonationManager.php";


if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}


$database = new Database();
$conn = $database->getConnection();
$donationManager = new DonationManager($conn);


if (isset($_GET['delete'])) {
    $donationManager->deleteDonation($_GET['delete']);
    header("Location: dashboard.php");
    exit();
}


$donations = $donationManager->getDonations();
?>
<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Requests</h2>

 
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Product Type</th>
                <th>Quantity</th>
                <th>Product Condition</th>
                <th>Delivery Option</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donations as $donation): ?>
                <tr>
                    <td><?= htmlspecialchars($donation["name"]); ?></td>
                    <td><?= htmlspecialchars($donation["email"]); ?></td>
                    <td><?= htmlspecialchars($donation["address"]); ?></td>
                    <td><?= htmlspecialchars($donation["phone"]); ?></td>
                    <td><?= htmlspecialchars($donation["products_type"]); ?></td>
                    <td><?= htmlspecialchars($donation["quantity"]); ?></td>
                    <td><?= htmlspecialchars($donation["products_condition"]); ?></td>
                    <td><?= htmlspecialchars($donation["delivery_option"]); ?></td>
                    <td><?= htmlspecialchars($donation["message"]); ?></td>
                    <td>
                     
                        <form action="request_form.php" method="POST" style="display:inline;">
                            <input type="hidden" name="request" value="<?= $donation["id"]; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Request</button>
                        </form>
                        
                      
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $donation['id']; ?>">Edit</button>
                    </td>
                </tr>

                <div class="modal fade" id="editModal<?= $donation['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Donation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                               
                                <form action="request.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $donation['id']; ?>">

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($donation["name"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($donation["email"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($donation["address"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($donation["phone"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="products_type" class="form-label">Product Type</label>
                                        <input type="text" class="form-control" name="products_type" value="<?= htmlspecialchars($donation["products_type"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" value="<?= htmlspecialchars($donation["quantity"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="products_condition" class="form-label">Product Condition</label>
                                        <input type="text" class="form-control" name="products_condition" value="<?= htmlspecialchars($donation["products_condition"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="delivery_option" class="form-label">Delivery Option</label>
                                        <input type="text" class="form-control" name="delivery_option" value="<?= htmlspecialchars($donation["delivery_option"]); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" name="message" required><?= htmlspecialchars($donation["message"]); ?></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
include "footer.php";
?>

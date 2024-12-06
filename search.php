<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/DonationManager.php";

$database = new Database();
$conn = $database->getConnection();
$donationManager = new DonationManager($conn);

// Initialize the search query variable
$searchQuery = '';
$donations = [];

// Check if a search query is set in the URL
if (isset($_GET['search'])) {
    $searchQuery = htmlspecialchars($_GET['search']);
    // Fetch donations filtered by search query
    $donations = $donationManager->searchDonations($searchQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .search-container, .table-container {
            width: 75%;
            margin: 0 auto;
        }
        .btn-request {
            display: block;
            margin: 20px auto;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="search-container">
        <h2 class="mb-4 text-center">Search Donations</h2>

      
        <form method="GET" action="search.php" class="d-flex justify-content-center">
            <div class="input-group mb-4">
                <input type="text" class="form-control" name="search" placeholder="Search by Product Type" value="<?= $searchQuery ?>" aria-label="Search">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <?php if ($searchQuery): ?>
        <?php if (count($donations) > 0): ?>
            <div class="table-container">
                <h4 class="text-center">Results for: <?= htmlspecialchars($searchQuery) ?></h4>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Product Type</th>
                                <th>Total Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td><?= htmlspecialchars($donation["products_type"]); ?></td>
                                    <td><?= htmlspecialchars($donation["total_quantity"]); ?></td>
                                    <td>
                                        <form action="request_form.php" method="GET">
                                            <input type="hidden" name="donation_id" value="<?= htmlspecialchars($donation['latest_donation_id']); ?>">
                                            <input type="hidden" name="search" value="<?= htmlspecialchars($searchQuery); ?>">
                                            <button type="submit" class="btn btn-primary">Request</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <p class="mt-3 text-center">No donations found for "<strong><?= htmlspecialchars($searchQuery) ?></strong>".</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>

<?php
include "layout/footer.php";
?>

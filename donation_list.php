<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";

// Database connection
$database = new Database();
$conn = $database->getConnection(); 

// Fetch donation data
$sql = "SELECT * FROM donations";
$result = $conn->query($sql);

// Close connection
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donors List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>

<header class="donors-header">
    <h1>-- DONORS LIST --</h1>
</header>

<div class="donor-cards-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="donor-card">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                <p><strong>Product Type:</strong> <?php echo htmlspecialchars($row['products_type']); ?></p>
                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?></p>
                <p><strong>Condition:</strong> <?php echo htmlspecialchars($row['products_condition']); ?></p>
                <p><strong>Delivery Option:</strong> <?php echo htmlspecialchars($row['delivery_option']); ?></p>
                <a href="request_form.php?donation_id=<?php echo $row['id']; ?>" class="btn btn-primary">Request</a>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require_once "layout/footer.php"; ?>

</body>
</html>

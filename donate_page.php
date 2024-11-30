<?php
require_once "layout/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Page</title>
    <link href="styles.css" rel="stylesheet">

</head> 
<body>
    <div class="hero-section">
        <div class="hero-text">
            <h1>Make a Difference Today</h1>
            <p>Your donations can change lives. Join us in making the world a better place.</p>
            <a href="donate.php" class="btn-highlight">Donate Now</a>
        </div>
        <div class="hero-image">
            <img src="images/donatePage.jpg" alt="Donation Banner">
        </div>
    </div>

    <div class="donation-info-section">
        <h2>Why you should Donate?</h2>
        <p>Every contribution brings hope and smiles to those in need.<br> Your generosity ensures meals for the hungry, education for children, and aid for the distressed.</p>
        <ul>
            <li>Support local communities</li>
            <li>Provide resources for disaster relief</li>
            <li>Ensure transparency and impactful donations</li>
        </ul>
    </div>

    <div class="call-to-action">
        <h2>Ready to change lives?</h2>
        <a href="donate.php" class="btn-highlight">Donate Now</a>
    </div>
</body>
</html>

<?php
require_once "layout/footer.php";
?>

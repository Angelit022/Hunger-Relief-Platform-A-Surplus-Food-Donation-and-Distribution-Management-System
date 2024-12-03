<?php
// Include the database class
require_once "db_connection.php";
require_once "header.php";

// Flag for success message
$donationSuccess = false;

class Donation {
    public $name;
    public $address;
    public $productsCondition;
    public $deliveryOption;

    // Constructor to initialize the values
    public function __construct($name, $address, $productCondition, $deliveryOption) {
        $this->name = htmlspecialchars($name);
        $this->address = htmlspecialchars($address);
        $this->productsCondition = htmlspecialchars($productCondition);
        $this->deliveryOption = htmlspecialchars($deliveryOption);
    }

    // Function to save the donation data to the database
    public function saveToDatabase($conn) {
        $sql = "INSERT INTO donations (name, address, products_condition, delivery_option) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $this->name, $this->address, $this->productsCondition, $this->deliveryOption);
        return $stmt->execute();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values from POST request
    $name = $_POST['name'] ?? ''; // Using null coalescing operator to handle missing values
    $address = $_POST['address'] ?? '';
    $products_condition = $_POST['productsCondition'] ?? '';  // Ensure the name matches the HTML form name
    $delivery_option = $_POST['deliveryOption'] ?? '';

    // Initialize the Database class
    $database = new Database();
    $connection = $database->getConnection();

    // Check for database connection errors
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Create a new Donation object
    $donation = new Donation($name, $address, $products_condition, $delivery_option);

    // Save donation to database
    if ($donation->saveToDatabase($connection)) {
        $donationSuccess = true; // Donation was successful
    } else {
        echo "Error executing query: " . $connection->error;
    }

    // Close the database connection
    $database->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Page</title>
    <link href="donate_page.css" rel="stylesheet">
</head>

<body>
    <div class="hero-section">
        <div class="hero-text">
            <h1>Make a Difference Today</h1>
            <p>Your donations can change lives. Join us in making the world a better place.</p>
            <a href="donate.php" class="btn-highlight">Donate Now</a>
            <a href="#" id="quickDonateBtn" class="btn-quick-donate">Quick Donate</a> <!-- Quick Donate button -->
        </div>
        <div class="hero-image">
            <img src="donatePage.jpg" alt="Donation Banner">
        </div>
    </div>

    <div class="donation-info-section">
        <h2>Why You Should Donate</h2>
        <p>Every contribution brings hope and smiles to those in need. Your generosity ensures meals for the hungry, education for children, and aid for the distressed.</p>
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

    <!-- Modal for Quick Donate -->
    <div id="quickDonateModal" class="modal">
        <div class="modal-content">
            <p>
                The Quick Donate option is designed for users who prefer donating pre-packed essentials. These packages are complete and ready for donation as soon as they reach us.
            </p>
            <p>Would you like to proceed?</p>
            <label>
                <input type="checkbox" id="proceedCheckbox"> Yes, I would like to proceed
            </label>
            <form id="quickDonateForm" method="POST" action="donate_page.php" style="display: none; margin-top: 20px;">
                <p>Please provide your name and address below to complete your donation:</p>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required><br><br>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required><br><br>

                <label for="productCondition">Product Condition:</label>
                <select id="productCondition" name="productsCondition" required>
                    <option value="unopened">Unopened</option>
                    <option value="properlyPacked">Properly Packed</option>
                    <option value="withinExpiry">Within Expiry Date</option>
                </select><br><br>

                <label for="deliveryOption">Delivery Option:</label>
                <select id="deliveryOption" name="deliveryOption" required>
                    <option value="pickUp">Pick-up</option>
                    <option value="dropOff">Drop-off</option>
                </select><br><br>

                <button type="submit">Submit</button>
            </form>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>

    <!-- Success alert using SweetAlert2 -->
    <script>
        <?php if ($donationSuccess): ?>
            Swal.fire({
                title: 'Donation Successful!',
                text: 'Thank you for your generous contribution. Your donation has been recorded.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                // Redirect to another page after successful submission, like dashboard.php or back to the donate page
                window.location.href = 'donate_page.php'; // or any other page you wish to redirect
            });
        <?php endif; ?>
    </script>
    <!-- script for quick donate-->
    <script>
        const quickDonateBtn = document.getElementById("quickDonateBtn");
        const modal = document.getElementById("quickDonateModal");
        const proceedCheckbox = document.getElementById("proceedCheckbox");
        const quickDonateForm = document.getElementById("quickDonateForm");

        quickDonateBtn.onclick = function() {
            modal.style.display = "flex";
        };

        function closeModal() {
            modal.style.display = "none";
        }

        proceedCheckbox.addEventListener('change', function() {
            if (proceedCheckbox.checked) {
                quickDonateForm.style.display = "block";
            } else {
                quickDonateForm.style.display = "none";
            }
        });
    </script>
</body>
</html>

<?php require_once "footer.php"; ?>
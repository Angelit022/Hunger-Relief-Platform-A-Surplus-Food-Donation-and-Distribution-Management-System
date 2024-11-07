<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePage</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Basic styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #f8f9fa;
            padding: 0.5rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: flex-end;
        }
        .navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .navbar ul li {
            margin-right: 1rem;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #000;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-dialog {
            background: #fff;
            padding: 1rem;
            width: 90%;
            max-width: 500px;
            border-radius: 4px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .modal-header h5 {
            margin: 0;
            flex: 1;
        }
        .close-button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
        }
        .modal-content {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 1rem;
            padding: 0 1rem;
            position: relative;
        }
        label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }
        #category, #address {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        /* Dropdown styling */
        #category-options {
            display: none;
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 0;
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }
        #category-options div {
            padding: 0.5rem;
            font-size: 1rem;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        #category-options div:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 0.5rem 1rem;
            cursor: pointer;
            text-align: center;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            margin-left: 0.5rem;
        }
        .show {
            display: flex;
        }
        .content-padding {
            padding-top: 70px;
        }
    </style>
</head>

<body>
    <!-- Fixed Navbar -->
    <nav class="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#" onclick="toggleModal('searchModal')">Search Essentials</a></li>
            <li><a href="#">Donate</a></li>
            <li><a href="#">Request</a></li>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Profile</a></li>
        </ul>
    </nav>

    <!-- Content Padding -->
    <div class="content-padding"></div>

    <!-- Search Modal -->
    <div class="modal" id="searchModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Search Specific Type</h5>
                    <button class="close-button" onclick="toggleModal('searchModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category" class="form-control" readonly onclick="toggleCategoryOptions()" placeholder="Select a category" required>
                            <div id="category-options">
                                <div onclick="selectCategory('Beverages')">Beverages</div>
                                <div onclick="selectCategory('Household Care')">Household Care</div>
                                <div onclick="selectCategory('Personal Care')">Personal Care</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleModal('resultModal')">Check the Result</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="resultModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Search Result</h5>
                    <button class="close-button" onclick="toggleModal('resultModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($_POST['category']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($_POST['address']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle modal visibility
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('show');
        }

        // Toggle category options
        function toggleCategoryOptions() {
            const options = document.getElementById('category-options');
            options.style.display = options.style.display === 'none' || options.style.display === '' ? 'block' : 'none';
        }

        // Select a category
        function selectCategory(category) {
            document.getElementById('category').value = category;
            document.getElementById('category-options').style.display = 'none';
        }

        // Close category options if clicked outside
        document.addEventListener('click', function (event) {
            const categoryOptions = document.getElementById('category-options');
            const categoryInput = document.getElementById('category');
            if (!categoryOptions.contains(event.target) && event.target !== categoryInput) {
                categoryOptions.style.display = 'none';
            }
        });
    </script>
</body>

</html>

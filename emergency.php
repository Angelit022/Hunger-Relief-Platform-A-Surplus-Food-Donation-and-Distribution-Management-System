<?php
require_once "layout/header.php";
require_once "classes/db_connection.php";
require_once "classes/emergencyRequest.php";    

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'submit_emergency') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $type = $_POST['type'];
    $details = $_POST['details'];
    $userId = $_SESSION['user_id'] ?? null; // Use null if user is not logged in

    try {
        $emergencyRequest = new EmergencyRequest();
        $requestId = $emergencyRequest->saveRequestToDatabase($userId, $latitude, $longitude, $type, $details);

        if ($requestId) {
            echo json_encode(['success' => true, 'message' => 'Emergency request submitted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit emergency request']);
        }
    } catch (Exception $e) {
        error_log("Emergency request error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while processing your request']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Request</title>
    <link href="styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="emergency-container">
        <h1>Emergency Request</h1>
        <p>
            Click the buttons below to locate your position or request urgent help. 
            Please ensure your location details are accurate.
        </p>
        <img src="images/map.jpg" alt="Emergency Help">
        <div class="buttons-group">
            <button id="locateBtn" class="btns-highlight">Locate Me</button>
            <button id="emergencyBtn" class="btns-highlight">Request Emergency Help</button>
        </div>
        <p id="location-status">Status: Not located yet.</p>
    </div>

    <script>
        function updateLocationStatus(latitude, longitude) {
            document.getElementById("location-status").textContent = 
                `Status: Located. Latitude: ${latitude}, Longitude: ${longitude}`;
        }

        function handleLocationError(error) {
            let errorMessage;
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = "User denied the request for Geolocation.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = "Location information is unavailable.";
                    break;
                case error.TIMEOUT:
                    errorMessage = "The request to get user location timed out.";
                    break;
                case error.UNKNOWN_ERROR:
                    errorMessage = "An unknown error occurred.";
                    break;
            }
            Swal.fire("Error", errorMessage, "error");
        }

        function getLocation(callback) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        updateLocationStatus(latitude, longitude);
                        callback(latitude, longitude);
                    },
                    handleLocationError
                );
            } else {
                Swal.fire("Error", "Geolocation is not supported by this browser.", "error");
            }
        }

        document.getElementById("locateBtn").addEventListener("click", () => {
            getLocation((latitude, longitude) => {
                const googleMapsUrl = `https://www.google.com/maps?q=${latitude},${longitude}`;
                window.open(googleMapsUrl, "_blank");
            });
        });

        document.getElementById("emergencyBtn").addEventListener("click", () => {
            Swal.fire({
                title: "Emergency Use Only",
                text: "This button is for urgent assistance only. By proceeding, you consent to sharing your location and details to receive help.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "I Understand",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    getLocation((latitude, longitude) => {
                        fetch('emergency.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=submit_emergency&latitude=${latitude}&longitude=${longitude}&type=Emergency&details=Urgent assistance required`
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Help Requested", "Your emergency request has been sent. A nearby relief organization will assist you soon.", "success");
                            } else {
                                Swal.fire("Error", data.message || "There was an issue processing your request. Please try again.", "error");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire("Help Requested", "Your emergency request has been sent. A nearby relief organization will assist you soon.", "success");
                        });
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
require_once "layout/footer.php";
?>


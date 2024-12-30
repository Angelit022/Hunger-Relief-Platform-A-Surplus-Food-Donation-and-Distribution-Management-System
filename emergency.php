<?php
require_once "layout/header.php";
require_once "classes/db_connection.php"; 
require_once "classes/emergencyRequest.php"; 

// Check if the request is a POST request and the action is to submit an emergency request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'submit_emergency') {
    // Retrieve latitude, longitude, and user ID from POST data and session
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $userId = $_SESSION['user_id'] ?? null; // Defaults to null if the user is not logged in

    $emergencyRequest = new EmergencyRequest();
    $requestId = $emergencyRequest->saveRequestToDatabase($userId, $latitude, $longitude);

    // Return JSON response based on the success of the database operation
    if ($requestId) {
        echo json_encode(['status' => 'success']); 
    } else {
        echo json_encode(['status' => 'error']);
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
                            body: `action=submit_emergency&latitude=${latitude}&longitude=${longitude}` //dito
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.status === "success") {
                                Swal.fire("Help Requested", "Your emergency request has been sent. A nearby relief organization will assist you soon.", "success");
                            } else {
                                throw new Error("Failed to submit emergency request");
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            console.error('Success', error);
                            Swal.fire("Help Requested", "Your emergency request has been sent to admin. Admin will call you soon, stay tuned !, A nearby relief organization will assist you .", "success");
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


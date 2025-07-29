<?php
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resident_id = $_POST['resident_id'];
    $service_id = $_POST['service_id'];

    // Optional: Sanitize input
    $resident_id = intval($resident_id);
    $service_id = intval($service_id);

    // Insert into `requests` table
    $sql = "INSERT INTO requests (resident_id, service_id, request_date, status)
            VALUES (?, ?, NOW(), 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $resident_id, $service_id);

    if ($stmt->execute()) {
        // Redirect or return success response
       header("Location: ../section/request.php?success=1");
        exit();
    } else {
        // Error response
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>

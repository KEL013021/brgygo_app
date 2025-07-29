<?php
include('connection.php');

// Make sure it's a POST request and an ID is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Step 1: Get the image filename from DB
    $sql = "SELECT image FROM announcements WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($imageName);
        $stmt->fetch();

        // Step 2: Delete the image file if it exists
        if (!empty($imageName)) {
            $imagePath = "../image/announcement/" . $imageName;
            if (file_exists($imagePath)) {
                unlink($imagePath); // Deletes the image from the server
            }
        }

        $stmt->close();

        // Step 3: Delete the announcement from the DB
        $deleteStmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Failed to delete announcement.";
        }

        $deleteStmt->close();
    } else {
        http_response_code(404);
        echo "Announcement not found.";
    }
} else {
    http_response_code(400);
    echo "Invalid request.";
}

$conn->close();
?>

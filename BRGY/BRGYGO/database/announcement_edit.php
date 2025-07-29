<?php
include('connection.php');

// Get input values
$id = $_POST['announcement_id'];
$title = $_POST['title'];
$content = $_POST['content'];
$imagePath = '';

// Validate input
if (!$id || !$title || !$content) {
    die("Invalid input.");
}

// Check if new image was uploaded
if (!empty($_FILES['image']['name'])) {
    $uploadDir = '../image/announcement/';
    $fileName = time() . "_" . basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $fileName;

    // Ensure the upload directory exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = $fileName; // Save only the filename
    } else {
        die("Image upload failed.");
    }
}

// Build the update query
if ($imagePath) {
    // If a new image is uploaded, update image field too
    $stmt = $conn->prepare("UPDATE announcements SET title=?, content=?, image=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $content, $imagePath, $id);
} else {
    // No new image uploaded
    $stmt = $conn->prepare("UPDATE announcements SET title=?, content=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $content, $id);
}

// Execute and check result
if ($stmt->execute()) {
    header("Location: ../section/announcement.php");
    exit();
} else {
    echo "Error updating announcement: " . $conn->error;
}
?>

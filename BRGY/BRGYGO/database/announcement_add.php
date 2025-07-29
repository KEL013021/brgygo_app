<?php
include('connection.php');

// Check if POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $date_posted = date('Y-m-d H:i:s');

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetDir = "../image/announcement/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $imageName; // Save only image name, not full path
        }
    }

    // Insert into DB
    $sql = "INSERT INTO announcements (title, content, image, date_posted) 
            VALUES ('$title', '$content', '$imagePath', '$date_posted')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../section/announcement.php"); // Redirect after success
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>

<?php
include 'connection.php';

if (!isset($_POST['id'])) {
    echo 'Resident ID is required.';
    exit;
}

$id = $_POST['id'];
$image = isset($_POST['image']) ? $_POST['image'] : null;

// 🗑️ Delete record
$sql = "DELETE FROM residents WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    // 🧼 Delete image file if exists
    if ($image && file_exists('../uploads/' . $image)) {
        unlink('../uploads/' . $image);
    }
    echo 'success';
} else {
    echo 'Failed: ' . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

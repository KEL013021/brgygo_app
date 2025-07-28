<?php
include('connection.php'); // Ensure this file connects to your DB

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $requestId = intval($_POST['id']);

    $sql = "UPDATE requests SET status = 'Declined' WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $requestId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

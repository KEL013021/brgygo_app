<?php
include('connection.php');

$position = $_POST['position'];
$name = $_POST['name'] ?? null;

// For Barangay Police, we match by name (not position-only)
if ($position === 'Barangay Police' && $name) {
    $nameParts = explode(' ', $name);
    $first = $nameParts[0] ?? '';
    $middle = $nameParts[1] ?? '';
    $last = $nameParts[2] ?? '';

    $residentQuery = mysqli_query($conn, "SELECT id FROM residents WHERE first_name='$first' AND middle_name='$middle' AND last_name='$last'");
    if (mysqli_num_rows($residentQuery) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Resident not found.']);
        exit;
    }
    $resident = mysqli_fetch_assoc($residentQuery);
    $resident_id = $resident['id'];
    $delete = mysqli_query($conn, "DELETE FROM barangay_official WHERE resident_id = '$resident_id' AND position = 'Barangay Police'");
} else {
    // For fixed positions (Chairman, Secretary, etc.), delete by position
    $delete = mysqli_query($conn, "DELETE FROM barangay_official WHERE position = '$position'");
}

if ($delete) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete functionary.']);
}
?>

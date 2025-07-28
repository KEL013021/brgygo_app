<?php
include 'connection.php';

$resident_id = $_POST['resident_id'];
$position = $_POST['position'];

// Check if this resident already holds another position
$check = mysqli_query($conn, "SELECT position FROM barangay_official WHERE resident_id = '$resident_id'");
if (mysqli_num_rows($check) > 0) {
    $row = mysqli_fetch_assoc($check);
    echo json_encode(['status' => 'conflict', 'current_position' => $row['position']]);
    exit;
}

// If position is a unique one (e.g. Chairman, Secretary, Treasurer), replace the existing one
$unique_positions = ['BRGY. CHAIRMAN', 'BRGY. Secretary', 'BRGY. Treasurer'];

if (in_array($position, $unique_positions)) {
    mysqli_query($conn, "DELETE FROM barangay_official WHERE position = '$position'");
}

// Assign new position
$insert = mysqli_query($conn, "INSERT INTO barangay_official (resident_id, position) VALUES ('$resident_id', '$position')");

if ($insert) {
    echo json_encode(['status' => 'success', 'message' => 'Resident assigned successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to assign resident.']);
}
?>

<?php
include('../database/connection.php');
$data = [];

$query = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, image_url FROM residents";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $row['image'] = '../upload/' . $row['image_url'];
    $data[] = $row;
}
echo json_encode($data);
?>

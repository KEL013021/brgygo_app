<?php
include('../database/connection.php');
$data = [];

$query = "SELECT id, service_name FROM services";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>

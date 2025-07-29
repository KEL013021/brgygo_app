<?php
include('connection.php');
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM announcements WHERE id = $id");
$row = $result->fetch_assoc();
echo json_encode($row);
?>

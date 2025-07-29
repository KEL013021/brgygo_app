<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];
    $fee = $_POST['fee'];

    $stmt = $conn->prepare("UPDATE services SET service_name=?, description=?, requirements=?, fee=? WHERE id=?");
    $stmt->bind_param("sssdi", $service_name, $description, $requirements, $fee, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>

<?php
include('connection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM services WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'service' => [
                'service_name' => $row['service_name'],
                'service_fee' => $row['service_fee'],
                'requirements' => $row['requirements'],
                'description' => $row['description'],
                'pdf_template' => $row['pdf_template'], // ✅ Include this
                'pdf_layout_data' => $row['pdf_layout_data'] // ✅ Include this
            ]
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>

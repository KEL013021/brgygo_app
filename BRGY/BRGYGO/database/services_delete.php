<?php
ob_start(); // Start buffering

ini_set('display_errors', 0);
error_reporting(0);

include('connection.php');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_POST['id'])) {
    $response['message'] = 'Service ID is required';
    echo json_encode($response);
    exit;
}

$serviceId = $_POST['id'];

try {
    $conn->begin_transaction();

    // Fetch file
    $stmt = $conn->prepare("SELECT pdf_template FROM services WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $stmt->bind_result($pdfTemplate);
    $stmt->fetch();
    $stmt->close();

    // Delete record
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $stmt->close();

    // Delete file
    if (!empty($pdfTemplate) && file_exists("../pdf_templates/$pdfTemplate")) {
        unlink("../pdf_templates/$pdfTemplate");
    }

    $conn->commit();
    $response['success'] = true;

} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = $e->getMessage();
}

// Clear all previous output
ob_end_clean();
echo json_encode($response);
exit;
?>

<?php
include('connection.php');

// Get POST data
$service_name = $_POST['service_name'];
$fee = $_POST['fee'];
$requirements = $_POST['requirements'];
$description = $_POST['description'];
$pdf_layout_data = $_POST['pdf_layout_data'];

$pdf_filename = '';

// Handle file upload
if (isset($_FILES['pdf_template']) && $_FILES['pdf_template']['error'] === UPLOAD_ERR_OK) {
    $pdf_filename = basename($_FILES['pdf_template']['name']);
    $upload_dir = '../pdf_templates/';
    $target_path = $upload_dir . $pdf_filename;

    // ✅ Create folder if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            die("❌ Failed to create upload directory.");
        }
    }

    // ✅ Move file
    if (!move_uploaded_file($_FILES['pdf_template']['tmp_name'], $target_path)) {
        die("❌ Failed to move uploaded file to: $target_path");
    }
}

// ✅ Check for duplicate service name
$check = $conn->prepare("SELECT * FROM services WHERE service_name = ?");
$check->bind_param("s", $service_name);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    echo "<script>window.location.href='../pages/services.php?exists=true';</script>";
    exit;
}

// ✅ Insert new service
$stmt = $conn->prepare("INSERT INTO services (service_name, service_fee, requirements, description, pdf_layout_data, pdf_template) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdssss", $service_name, $fee, $requirements, $description, $pdf_layout_data, $pdf_filename);

if ($stmt->execute()) {
    echo "<script>window.location.href='../section/services.php?success=true';</script>";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>

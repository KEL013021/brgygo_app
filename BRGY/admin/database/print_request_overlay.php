<?php
include('../database/connection.php');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing request ID']);
    exit;
}

$requestId = intval($_GET['id']);

// Fetch request + resident + service + layout
$query = "
    SELECT r.*, 
           s.service_name, s.pdf_template, s.pdf_layout_data,
           CONCAT(res.first_name, ' ', res.last_name) AS full_name
    FROM requests r
    JOIN services s ON r.service_id = s.id
    JOIN residents res ON r.resident_id = res.id
    WHERE r.id = $requestId
";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Request not found']);
    exit;
}

$data = mysqli_fetch_assoc($result);

// Prepare merge values
$tokens = [
    '{{full_name}}' => $data['full_name'],
    '{{Service Name}}' => $data['service_name'],
    '{{Date Requested}}' => $data['request_date'],
    '{{Status}}' => $data['status']
];

$layout = json_decode($data['pdf_layout_data'], true);
if (!is_array($layout)) $layout = [];

// Replace tokens in layout
foreach ($layout as &$field) {
    foreach ($tokens as $key => $value) {
        $field['text'] = str_replace($key, $value, $field['text']);
    }
}

echo json_encode([
    'success' => true,
    'pdf_template' => $data['pdf_template'],
    'layout' => $layout
]);

<?php
include('connection.php');

// === ✅ Handle Cropped Image (from base64) ===
$imagePath = "../assets/img/default.png";

if (!empty($_POST['cropped_image_data'])) {
    $base64 = $_POST['cropped_image_data'];

    if (strpos($base64, 'base64,') !== false) {
        list(, $base64) = explode(',', $base64);
    }

    $imageData = base64_decode($base64);
    $filename = uniqid('resident_') . '.png';
    $uploadDir = "../uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filePath = $uploadDir . $filename;
    if (file_put_contents($filePath, $imageData)) {
        $imagePath = $filePath;
    }
}

$user_id = 0;

$stmt = $conn->prepare("INSERT INTO residents (
  user_id, image_url, first_name, middle_name, last_name,
  gender, date_of_birth, pob_country, pob_province, pob_city,
  pob_barangay, civil_status, nationality, religion, country,
  province, city, barangay, zipcode, house_number,
  zone_purok, residency_date, years_of_residency, residency_type, previous_address,
  father_name, mother_name, spouse_name, number_of_family_members, household_number,
  relationship_to_head, house_position, educational_attainment, current_school, occupation,
  monthly_income, mobile_number, telephone_number, email_address, emergency_contact_person,
  emergency_contact_number, pwd_status, pwd_id_number, senior_citizen_status, senior_id_number,
  solo_parent_status, is_4ps_member, blood_type, voter_status
) VALUES (
  ?, ?, ?, ?, ?,
  ?, ?, ?, ?, ?,
  ?, ?, ?, ?, ?,
  ?, ?, ?, ?, ?,
  ?, ?, ?, ?, ?,
  ?, ?, ?, ?, ?,
  ?, ?, ?, ?, ?,
  ?, ?, ?, ?, ?, 
  ?, ?, ?, ?, ?, 
  ?, ?, ?, ?
)");

$stmt->bind_param(
  "issssssssssssssssssssssssssssssssssssssssssssssss",
  $user_id,
  $imagePath,
  $_POST['first_name'],
  $_POST['middle_name'],
  $_POST['last_name'],
  $_POST['gender'],
  $_POST['date_of_birth'],
  $_POST['pob_country'],
  $_POST['pob_province'],
  $_POST['pob_city'],
  $_POST['pob_barangay'],
  $_POST['civil_status'],
  $_POST['nationality'],
  $_POST['religion'],
  $_POST['country'],
  $_POST['province'],
  $_POST['city'],
  $_POST['barangay'],
  $_POST['zipcode'],
  $_POST['house_number'],
  $_POST['zone_purok'],
  $_POST['residency_date'],
  $_POST['years_of_residency'],
  $_POST['residency_type'],
  $_POST['previous_address'],
  $_POST['father_name'],
  $_POST['mother_name'],
  $_POST['spouse_name'],
  $_POST['number_of_family_members'],
  $_POST['household_number'],
  $_POST['relationship_to_head'],
  $_POST['house_position'],
  $_POST['educational_attainment'],
  $_POST['current_school'],
  $_POST['occupation'],
  $_POST['monthly_income'],
  $_POST['mobile_number'],
  $_POST['telephone_number'],
  $_POST['email_address'],
  $_POST['emergency_contact_person'],
  $_POST['emergency_contact_number'],
  $_POST['pwd_status'],
  $_POST['pwd_id_number'],
  $_POST['senior_citizen_status'],
  $_POST['senior_id_number'],
  $_POST['solo_parent_status'],
  $_POST['is_4ps_member'],
  $_POST['blood_type'],
  $_POST['voter_status']
);

if ($stmt->execute()) {
    header("Location: ../section/barangay_resident.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

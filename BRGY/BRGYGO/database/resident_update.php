<?php
include 'connection.php';

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo 'Resident ID is missing.';
    exit;
}

$id = $_POST['id'];
$first_name = trim($_POST['first_name']);
$middle_name = trim($_POST['middle_name']);
$last_name = trim($_POST['last_name']);
$gender = $_POST['gender'];
$date_of_birth = $_POST['date_of_birth'];
$pob_country = $_POST['pob_country'];
$pob_province = $_POST['pob_province'];
$pob_city = $_POST['pob_city'];
$pob_barangay = $_POST['pob_barangay'];
$civil_status = $_POST['civil_status'];
$nationality = $_POST['nationality'];
$religion = $_POST['religion'];
$country = $_POST['country'];
$province = $_POST['province'];
$city = $_POST['city'];
$barangay = $_POST['barangay'];
$zipcode = $_POST['zipcode'];
$house_number = $_POST['house_number'];
$zone_purok = $_POST['zone_purok'];
$years_of_residency = $_POST['years_of_residency'];
$father_name = trim($_POST['father_name']);
$mother_name = trim($_POST['mother_name']);
$spouse_name = trim($_POST['spouse_name']);
$number_of_family_members = $_POST['number_of_family_members'];
$household_number = $_POST['household_number'];
$relationship_to_head = $_POST['relationship_to_head'];
$house_position = $_POST['house_position'];
$educational_attainment = $_POST['educational_attainment'];
$occupation = $_POST['occupation'];
$monthly_income = $_POST['monthly_income'];
$mobile_number = $_POST['mobile_number'];
$telephone_number = $_POST['telephone_number'];
$email_address = $_POST['email_address'];
$emergency_contact_person = $_POST['emergency_contact_person'];
$emergency_contact_number = $_POST['emergency_contact_number'];
$pwd_status = $_POST['pwd_status'];
$pwd_id_number = $_POST['pwd_id_number'];
$senior_citizen_status = $_POST['senior_citizen_status'];
$senior_id_number = $_POST['senior_id_number'];
$solo_parent_status = $_POST['solo_parent_status'];
$is_4ps_member = $_POST['is_4ps_member'];
$blood_type = $_POST['blood_type'];
$voter_status = $_POST['voter_status'];

$image_url = null;

// ✅ Handle cropped image (base64)
if (isset($_POST['cropped_image']) && !empty($_POST['cropped_image'])) {
    $base64 = $_POST['cropped_image'];
    $base64 = str_replace('data:image/png;base64,', '', $base64);
    $base64 = str_replace(' ', '+', $base64);
    $imageData = base64_decode($base64);

    $imageName = uniqid('resident_') . '.png';
    $imagePath = '../uploads/' . $imageName;

    if (!is_dir('../uploads')) {
        mkdir('../uploads', 0777, true);
    }

    if (file_put_contents($imagePath, $imageData)) {
        $image_url = $imageName;

        // ✅ Auto-delete old image
        if (!empty($_POST['old_image'])) {
            $oldImagePath = '../uploads/' . basename($_POST['old_image']);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    } else {
        echo 'Failed to save cropped image.';
        exit;
    }
}

// ✅ Prepare SQL
$sql = "UPDATE residents SET 
    first_name = ?, middle_name = ?, last_name = ?, gender = ?, date_of_birth = ?, 
    pob_country = ?, pob_province = ?, pob_city = ?, pob_barangay = ?, civil_status = ?, 
    nationality = ?, religion = ?, country = ?, province = ?, city = ?, barangay = ?, 
    zipcode = ?, house_number = ?, zone_purok = ?, years_of_residency = ?, 
    father_name = ?, mother_name = ?, spouse_name = ?, number_of_family_members = ?, 
    household_number = ?, relationship_to_head = ?, house_position = ?, educational_attainment = ?, 
    occupation = ?, monthly_income = ?, mobile_number = ?, telephone_number = ?, 
    email_address = ?, emergency_contact_person = ?, emergency_contact_number = ?, 
    pwd_status = ?, pwd_id_number = ?, senior_citizen_status = ?, senior_id_number = ?, 
    solo_parent_status = ?, is_4ps_member = ?, blood_type = ?, voter_status = ?";

$params = [
    $first_name, $middle_name, $last_name, $gender, $date_of_birth,
    $pob_country, $pob_province, $pob_city, $pob_barangay, $civil_status,
    $nationality, $religion, $country, $province, $city, $barangay,
    $zipcode, $house_number, $zone_purok, $years_of_residency,
    $father_name, $mother_name, $spouse_name, $number_of_family_members,
    $household_number, $relationship_to_head, $house_position, $educational_attainment,
    $occupation, $monthly_income, $mobile_number, $telephone_number,
    $email_address, $emergency_contact_person, $emergency_contact_number,
    $pwd_status, $pwd_id_number, $senior_citizen_status, $senior_id_number,
    $solo_parent_status, $is_4ps_member, $blood_type, $voter_status
];

// 🔁 Add image_url if uploaded
if ($image_url !== null) {
    $sql .= ", image_url = ?";
    $params[] = $image_url;
}

// 🧾 Add WHERE clause
$sql .= " WHERE id = ?";
$params[] = $id;

// 🛡️ Prepare & bind
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo 'SQL error: ' . mysqli_error($conn);
    exit;
}

$types = str_repeat('s', count($params));
mysqli_stmt_bind_param($stmt, $types, ...$params);

if (mysqli_stmt_execute($stmt)) {
    echo 'success';
} else {
    echo 'Update failed: ' . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

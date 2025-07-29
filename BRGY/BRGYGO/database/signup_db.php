<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $gmail = $_POST['email'];
    $password = $_POST['password'];
    $toa = isset($_POST['toa']) ? 1 : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'user'; // defaults to 'user'

    if (!$toa) {
        echo "You must agree to the Terms and Conditions.";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli("localhost", "root", "", "brygo");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $check = $conn->prepare("SELECT gmail FROM user WHERE gmail = ?");
    $check->bind_param("s", $gmail);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (gmail, password, toa, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $gmail, $hashedPassword, $toa, $status);
        if ($stmt->execute()) {
            echo "Signup successful!";
            header("Location: dashboard.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $check->close();
    $conn->close();
}
?>

<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $gmail = $_POST['email'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "brygo");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT user_id, gmail, password, status FROM user WHERE gmail = ?");
    $stmt->bind_param("s", $gmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $db_gmail, $db_password, $status);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['gmail'] = $db_gmail;
            $_SESSION['status'] = $status;

            if ($status === 'admin') {
                echo "Redirecting to admin dashboard...";
                header("Location: ../section/dashboard.php");
            } else {
                echo "Redirecting to user dashboard...";
                // header("Location: user_dashboard.php");
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with this email.";
    }

    $stmt->close();
    $conn->close();
}
?>

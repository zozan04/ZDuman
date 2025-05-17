<?php
include_once("db_connection.php");
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['studentEmail'];
    $password = $_POST['studentPassword'];

    $sql = "SELECT id, email, password FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Şifre kontrolü
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id'];
            header("Location: student.php");
            exit();
        }
    }

    // Giriş başarısızsa hata mesajını session ile gönder
    $_SESSION['login_error'] = "E-posta veya şifre hatalı. Lütfen tekrar deneyin.";
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Girişi</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="portal-selection">
        <h1>Öğrenci Giriş</h1>
        <form id="studentLoginForm" class="form" method="POST" action="ogrenci_giris.php">
            <h3>Öğrenci Giriş</h3>
            <input type="email" id="studentEmail" name="studentEmail" placeholder="Email" required>
            <input type="password" id="studentPassword" name="studentPassword" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
        </form>

        <?php
        // Hata mesajını göster (eğer varsa)
        if (isset($error_message)) {
            echo "<p style='color:red;'>$error_message</p>";
        }
        ?>
    </div>
</body>
</html>

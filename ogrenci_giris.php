<?php
include_once("db_connection.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Formdan gelen veriler
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['studentEmail'];
    $password = $_POST['studentPassword'];

    // SQL Sorgusu - email ve password ile veritabanı kontrolü
    $sql = "SELECT id, email, password FROM students WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kullanıcıyı bulduysa, öğrenci sayfasına yönlendir
    if ($result->num_rows > 0) {
        // Kullanıcı bulundu, öğrenci sayfasına yönlendir
        $row = $result->fetch_assoc();
        session_start();
        $_SESSION['student_id'] = $row['id']; // Öğrencinin ID'sini session'a kaydet
        header("Location: student.php");
        exit();
    } else {
        // Kullanıcı bulunamadı, hata mesajı göster
        $error_message = "E-posta veya şifre hatalı. Lütfen tekrar deneyin.";
    }
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

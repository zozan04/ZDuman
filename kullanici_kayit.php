<?php
include('db_connection.php');
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = isset($_POST['userName']) ? trim($_POST['userName']) : '';
    $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : '';
    $userPassword = isset($_POST['userPassword']) ? trim($_POST['userPassword']) : '';

    if (empty($userName) || empty($userEmail) || empty($userPassword)) {
        die("Hata: Lütfen tüm alanları doldurun.");
    }

    // E-posta var mı kontrol et
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $userEmail);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        die("Bu e-posta zaten kayıtlı!");
    }

    // Şifreyi güvenli hale getir
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    // Kullanıcıyı ekle
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $userName, $userEmail, $hashedPassword);

    if ($stmt->execute()) {
        echo "Kayıt başarılı!";
    } else {
        echo "Kayıt hatası: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

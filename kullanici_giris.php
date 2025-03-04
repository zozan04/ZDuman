<?php
include('db_connection.php');
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : '';
    $userPassword = isset($_POST['userPassword']) ? trim($_POST['userPassword']) : '';

    if (empty($userEmail) || empty($userPassword)) {
        die(json_encode(["status" => "error", "message" => "Lütfen tüm alanları doldurun."]));
    }

    // Kullanıcının e-posta ve şifresini kontrol et
    $checkUser = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $checkUser->bind_param("s", $userEmail);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Şifreyi kontrol et
        if (password_verify($userPassword, $user['password'])) {
            // Giriş başarılı, oturum başlat
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $userEmail;

            // Kullanıcıyı doğrudan kullanici.php sayfasına yönlendir
            echo json_encode(["status" => "success", "redirect" => "kullanici.php"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Yanlış şifre."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "E-posta adresi bulunamadı."]);
    }

    $checkUser->close();
    $conn->close();
}
?>

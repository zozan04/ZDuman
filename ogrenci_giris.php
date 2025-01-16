<?php
// Veritabanı bağlantısı
$servername = "127.0.0.1";
$username = "ghibli";  
$password = "";  // Veritabanı şifrenizi buraya girin
$dbname = "ghibli_yemek_platformu";  

// MySQLi ile bağlantı
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    echo "Bağlantı hatası: " . $conn->connect_error;
    die();  // Bağlantı hatası varsa, işlemi sonlandırır.
}
?>

// Formdan gelen veriler
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['studentEmail'];
    $password = $_POST['studentPassword'];

    // SQL Sorgusu - email ve password ile veritabanı kontrolü
    $sql = "SELECT * FROM students WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kullanıcıyı bulduysa, öğrenci sayfasına yönlendir
    if ($result->num_rows > 0) {
        // Kullanıcı bulundu, öğrenci sayfasına yönlendir
        header("Location: student.php");
        exit();
    } else {
        // Kullanıcı bulunamadı, hata mesajı göster
        $error_message = "Önce kayıt olmanız gerekiyor.";
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
        <form action="ogrenci_giris.php" method="POST">
            <label for="studentEmail">E-posta:</label>
            <input type="email" id="studentEmail" name="studentEmail" required><br>
    
            <label for="studentPassword">Şifre:</label>
            <input type="password" id="studentPassword" name="studentPassword" required><br>
    
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


<?php
$servername = "127.0.0.1";
$username = "ghibli";
$password = "";  // Şifre boş bırakılmıştı, ancak şifre belirlediyseniz burada kullanın
$dbname = "ghibli_yemek_platformu";

// MySQLi ile bağlantı
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

?>
<?php
session_start();
session_unset();  // Oturumdaki tüm değişkenleri temizle
session_destroy();  // Oturumu sonlandır
header("Location: ogrenci_giris.php");  // Kullanıcıyı giriş sayfasına yönlendir
exit();
?>

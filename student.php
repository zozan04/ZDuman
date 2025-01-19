<?php
include('db_connection.php');
session_start();

// Eğer öğrenci giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['student_id'])) {
    header("Location: ogrenci_giris.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Yönetimi</title>
    <style>
        .error {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        .success {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Öğrenci Yemek Yönetim Sistemi</h2>

    <!-- Yemek Ekleme Formu -->
    <div class="form-container">
        <h3>Yemek Ekle</h3>
        <form action="student.php" method="POST" enctype="multipart/form-data">
            <label for="kategori">Yemek Kategorisi:</label>
            <select name="kategori" required>
                <option value="Ana Yemekler">Ana Yemekler</option>
                <option value="Sulu Yemekler">Sulu Yemekler</option>
                <option value="Karbonhidrat Lezzetleri">Karbonhidrat Lezzetleri</option>
                <option value="Aperatifler">Aperatifler</option>
                <option value="Tatlı Çeşitleri">Tatlı Çeşitleri</option>
            </select><br>

            <label for="yemek_adi">Yemek Adı:</label>
            <input type="text" name="yemek_adi" required><br>

            <label for="fiyat">Fiyat:</label>
            <input type="number" name="fiyat" step="0.01" required><br>

            <label for="resim">Yemek Görseli:</label>
            <input type="file" name="resim" accept="image/*"><br>

            <input type="submit" name="submit" value="Yemek Ekle">
        </form>
    </div>

    <?php
    // Mesaj değişkeni
    $message = "";

    if (isset($_POST['submit'])) {
        $kategori = $_POST['kategori'];
        $yemek_adi = $_POST['yemek_adi'];
        $fiyat = $_POST['fiyat'];

        // Öğrencinin ID'sini almak
        $student_id = $_SESSION['student_id']; // Öğrencinin ID'si session'dan alınacak

        // Dosya kontrolü
        if (!isset($_FILES['resim']) || $_FILES['resim']['error'] !== UPLOAD_ERR_OK) {
            $message = "<div class='error'>Lütfen bir dosya seçin!</div>";
        } else {
            // Görsel yükleme işlemi
            $resim_name = $_FILES['resim']['name'];
            $resim_tmp_name = $_FILES['resim']['tmp_name'];
            $upload_dir = 'resimler/';
            $upload_path = $upload_dir . basename($resim_name);

            if (!move_uploaded_file($resim_tmp_name, $upload_path)) {
                $message = "<div class='error'>Görsel yüklenirken bir hata oluştu.</div>";
            } else {
                // Yemek ekleme SQL sorgusu
                $sql = "INSERT INTO meals (user_id, name, category, image_path, price) 
                        VALUES ('$student_id', '$yemek_adi', '$kategori', '$upload_path', '$fiyat')";

                if ($conn->query($sql) === TRUE) {
                    $message = "<div class='success'>Yemek başarıyla eklendi.</div>";
                } else {
                    $message = "<div class='error'>Hata: " . $conn->error . "</div>";
                }
            }
        }
    }

    // Mesajı göster
    if (!empty($message)) {
        echo $message;
    }

    // Bağlantıyı kapat
    $conn->close();
    ?>
</div>

</body>
</html>

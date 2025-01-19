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
        /* Genel stil ayarları */
        body {
            background-image: url('https://www.floryabasakyemek.com/wp-content/uploads/2018/06/florya-basak-yemek-header-1600x925.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Container sınıfı */
        .container {
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.47); /* Yalnızca container'a opaklık ekledik */
            position: fixed;
            top: 0;
            left: 0;
            padding: 30px;
            z-index: 1; /* form-container'ın üstte olması için */
        }

        /* Navbar stili */
        .navbar {
            background-color: #511212;
            overflow: hidden;
            padding: 10px 0;
            z-index: 2;
            position: fixed; /* Navbar'ı sabitle */
            top: 0; /* Sayfanın en üstüne yerleştir */
            left: 0; /* Sayfanın sol tarafına yerleştir */
            width: 100%; /* Navbar'ın genişliğini tam sayfa yap */
        }

        /* Form container stili */
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white; /* Formun beyaz arka planı */
            padding: 20px;
            margin-top: 80px; /* Navbar'ın üstünde olması için boşluk */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative; /* form'un container'dan bağımsız konumlanmasını sağlar */
            z-index: 2; /* Formu container'ın üstünde tutar */
        }

        h2 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        h3 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
        }

        select, input[type="text"], input[type="number"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color:  #511212;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #511212;
        }

        .message {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            color: #511212;
        }

        .error {
            color: #511212;
        }

        /* Animasyon ekleme */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        /* Hover efektleri */
        select:hover, input[type="text"]:hover, input[type="number"]:hover {
            border-color: #4a90e2;
        }

        input[type="file"]:hover {
            background-color: #f0f8ff;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar -->
    <div class="navbar">
        <h2>Öğrenci Yemek Yönetim Sistemi</h2>
    </div>

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

        <!-- Başarı veya hata mesajı -->
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
            echo "<div class='message' id='message'>$message</div>";
        }

        // Bağlantıyı kapat
        $conn->close();
        ?>
    </div>
</div>

<script>
    // Sayfa yüklendikten sonra mesajı 2 saniye sonra gizle
    window.onload = function() {
        setTimeout(function() {
            const message = document.getElementById('message');
            if (message) {
                message.style.display = 'none';
            }
        }, 2000); // 2000 milisaniye = 2 saniye
    }
</script>

</body>
</html>

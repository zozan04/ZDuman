<?php
include('db_connection.php');
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Eğer öğrenci giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['student_id'])) {
    header("Location: ogrenci_giris.php");
    exit();
}

$student_id = $_SESSION['student_id']; // Get the logged-in student's ID

/// Yemek ekleme işlemi
$message = "";
if (isset($_POST['submit'])) {
    $kategori = $_POST['kategori'];
    $yemek_adi = $_POST['yemek_adi'];
    $fiyat = $_POST['fiyat'];
    $icerik = $_POST['icerik']; // Yeni içerik alanı

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
            $sql = "INSERT INTO meals (user_id, name, category, image_path, price, content) 
                    VALUES ('$student_id', '$yemek_adi', '$kategori', '$upload_path', '$fiyat', '$icerik')";

            if ($conn->query($sql) === TRUE) {
                $message = "<div class='success'>Yemek başarıyla eklendi.</div>";
                header("Location: student.php");
                exit();
            } else {
                $message = "<div class='error'>Hata: " . $conn->error . "</div>";
            }
        }
    }
}

// Silme işlemi
if (isset($_GET['delete'])) {
    $meal_id = $_GET['delete'];
    
    // Öğrencinin sadece kendi yemeklerini silebilmesi için kontrol
    $sql_check_ownership = "SELECT * FROM meals WHERE id = '$meal_id' AND user_id = '$student_id'";
    $result_check_ownership = $conn->query($sql_check_ownership);
    
    if ($result_check_ownership->num_rows > 0) {
        // Yemek mevcut ve öğrenciye aitse silme işlemi yap
        $sql_delete = "DELETE FROM meals WHERE id = '$meal_id' AND user_id = '$student_id'";
        
        if ($conn->query($sql_delete) === TRUE) {
            $message = "<div class='success'>Yemek başarıyla silindi.</div>";
        } else {
            $message = "<div class='error'>Silme işlemi sırasında bir hata oluştu.</div>";
        }
    } else {
        $message = "<div class='error'>Bu yemeği silme yetkiniz yok.</div>";
    }
}


// Oturum açmış öğrencinin yemeklerini getiren SQL sorgusu
$sql_get_meals = "SELECT * FROM meals WHERE user_id = '$student_id'";
$result_get_meals = $conn->query($sql_get_meals);


?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghibli Yemek Platformu</title>
    <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="student.css">
    <script src="https://unpkg.com/scrollreveal"></script>
    
</head>
<body>

<div class="container">
    <!-- Navbar -->
    <div class="navbar">
        <h2>Öğrenci Yemek Yönetim Sistemi</h2>
        <div class="icons-container">
        <!-- Yemekler Linki -->
        <ul class="menu">
                 <div class="dropdown">
                    <button class="dropbtn">Kontrol Et</button>
                    <div class="dropdown-content">
                        <a href="ana_yemekler.php">Ana Yemekler</a>
                        <a href="sulu_yemekler.php">Sulu Yemekler</a>
                        <a href="karbonhidrat_lezzetleri.php">Karbonhidrat Lezzetleri</a>
                        <a href="aperatifler.php">Aperatifler</a>
                        <a href="tatli_cesitleri.php">Tatlı Çeşitleri</a>
                    </div>
                </div>
            </ul>

        <!-- Giriş İkonu -->
        <a href="index.html" class="login-icon" title="Çıkış Yap">
    <div class="circle">
        <i class="fas fa-user" style="color: black; font-size: 20px;"></i>
    </div>
</a>


        </div>
        </div>
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

            <label for="icerik">Yemek İçeriği:</label>
            <textarea name="icerik" rows="4" required></textarea><br>

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
                    $message = "<div class='error'></div>";
                } else {
                    // Yemek ekleme SQL sorgusu
                    $sql = "INSERT INTO meals (user_id, name, category, image_path, price) 
                            VALUES ('$student_id', '$yemek_adi', '$kategori', '$upload_path', '$fiyat')";

                    if ($conn->query($sql) === TRUE) {
                        $message = "<div class='success'>Yemek başarıyla eklendi.</div>";

                         header("Location: student.php");
                         exit();  // Yönlendirme yaptıktan sonra kodun devamını çalıştırma
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
<div class="container">
    <!-- Yemek Listesi Tablosu -->
    <table class="meals-table">
        <thead>
            <tr>
                <th>Resim</th>
                <th>Yemek Adı</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>İçerik</th> <!-- Yeni içerik sütunu -->
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_get_meals->num_rows > 0) {
                while ($meal = $result_get_meals->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='" . $meal['image_path'] . "' alt='" . $meal['name'] . "' class='meal-image'></td>";
                    echo "<td>" . $meal['name'] . "</td>";
                    echo "<td>" . $meal['category'] . "</td>";
                    echo "<td>" . $meal['price'] . " TL</td>";
                    echo "<td>" . $meal['content'] . "</td>"; // İçerik sütunu
                    echo "<td><a href='student.php?delete=" . $meal['id'] . "' class='delete-btn'>Sil</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Henüz yemek eklenmemiş.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<script>
     /* Açılır menüyü aç/kapat */
     function toggleDropdown() {
        document.getElementById("myDropdown").classList.toggle("show");
    }

    // Kullanıcı ekranın herhangi bir yerine tıkladığında açılır menüyü kapat
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
   
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

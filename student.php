<?php
include('db_connection.php');
session_start();

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/scrollreveal"></script>
    
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
    height: 100vh; /* Yüksekliği %100 yaparak tüm sayfanın gözükmesini sağlıyoruz */
    overflow-x: hidden; /* Yalnızca dikey scroll aktif */
}

        /* Container sınıfı */
        .container {
            width: 100%;
            height: 100%;           
            top: 0;
            left: 0;
            z-index: 1; /* form-container'ın üstte olması için */
            overflow-y: auto; /* Scroll ekleme */
        }

        /* Navbar stili */
        .navbar {
            background-color: #511212;
            
            padding: -5px 0;
            z-index: 999; 
            position: fixed; /* Navbar'ı sabitle */
            top: 0; /* Sayfanın en üstüne yerleştir */
            left: 0; /* Sayfanın sol tarafına yerleştir */
            width: 100%; /* Navbar'ın genişliğini tam sayfa yap */
            
        }
        ul.menu {
            flex-grow: 0;
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
            isolation: isolate;
            margin-top:-65px;
            margin-left:1200px;
        }

        ul.menu li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            padding: 10px 20px;
            display: flex;
        }

        ul.menu li a:hover {
            background-color: #e0a4a4;
            border-radius: 5px;
        }

        .icons-container {
            display: flex;
            align-items: center;
            gap: 15px;
            
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #ddd;}

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown .dropbtn{
            background-color: #511212;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            border:none;
            cursor: pointer;
            border-radius: 5px;

        }
        .dropdown:hover .dropbtn{
            background-color: #e0a4a4;
        }

        .circle {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border: 2px solid black;
            border-radius: 50%;
            background-color: white;
            cursor: pointer;
            margin-top:-45px;
            margin-bottom:20px;
        }

        .login-icon {
            position: relative;
        }

        

        /* Form container stili */
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white; /* Formun beyaz arka planı */
            padding: 20px;
            margin-top: 150px; /* Navbar'ın üstünde olması için boşluk */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative; /* form'un container'dan bağımsız konumlanmasını sağlar */
            z-index: 2; /* Formu container'ın üstünde tutar */
        }

        h2 {
            text-align: center;
            color: white;
            margin-top: 35px;
            margin-bottom:15px;
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

        /* Yemekler tablosu stilini oluşturma */
.meals-table {
    width: 100%; /* Tabloyu tam genişlikte tutuyoruz */
    max-width: 100%; /* Tablo genişliği 100% olmalı */
    border-collapse: collapse; /* Hücreleri birbirine bağla */
    margin-top: 50px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    table-layout: fixed; /* Sabit genişlikte hücre düzeni */
  
  
}

.meals-table th, .meals-table td {
    padding: 12px;
    text-align: center;
    border: 1px solid #ddd;
    
}

.meals-table th {
    background-color: #511212;
    color: white;
}

.meals-table td img {
    max-width: 80px;
    max-height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.meals-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.meals-table tr:hover {
    background-color: #f1f1f1;
}
.delete-btn {
    color: red;
    text-decoration: none;
    font-weight: bold;
}

.delete-btn:hover {
    text-decoration: underline;
}


    </style>
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
        <a href="login.html" class="login-icon" title="Çıkış Yap">
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

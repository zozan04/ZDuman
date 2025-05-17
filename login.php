<?php
include_once("db_connection.php");
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$error_message = '';  // Hata mesajı

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['studentEmail'];
    $password = $_POST['studentPassword'];

    // E-posta ve şifre kontrolü
    $sql = "SELECT id, email, password FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Şifre doğrulama
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id'];
            // Başarılı girişte yönlendirme yapıyoruz
            header("Location: student.php");
            exit();
        } else {
            // Şifre hatalı ise hata mesajı
            $error_message = "E-posta veya şifre hatalı. Lütfen tekrar deneyin.";
        }
    } else {
        // Kullanıcı bulunamadı ise hata mesajı
        $error_message = "E-posta veya şifre hatalı. Lütfen tekrar deneyin.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ghibli Yemek Platformu</title>
   <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->
    <link rel="stylesheet" href="login.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <div class="portal-selection">
        <h1>Portal Seçimi</h1>
        <div class="button-container">
            <button id="studentPortalBtn" class="portal-btn">Öğrenci Portalı</button>
            <button id="userPortalBtn" class="portal-btn">Kullanıcı Portalı</button>
            <button id="adminPortalBtn" class="portal-btn">Admin Portalı</button>
        </div>
    </div>

    <!-- Öğrenci Portalı -->
    <div id="studentPortal" class="portal hidden">
        <h2>Öğrenci Portalı</h2>
        <div class="form-toggle">
            <button id="studentLoginBtn" class="active">Öğrenci Giriş</button>
            <button id="studentRegisterBtn">Öğrenci Kayıt</button>
        </div>

       <!-- Öğrenci Giriş Formu -->
<form id="studentLoginForm" class="form" method="POST">
    <h3>Öğrenci Giriş</h3>
    <input type="email" id="studentEmail" name="studentEmail" placeholder="Email" required>
    <input type="password" id="studentPassword" name="studentPassword" placeholder="Şifre" required>
    <button type="submit">Giriş Yap</button>

        <?php if (!empty($error_message)) { ?>
                <p id="error-message" style="color:red; margin-top:10px;"><?php echo $error_message; ?></p>
            <?php } ?>
</form>


<!-- Öğrenci Kayıt Formu ve Belge Yükleme -->
<form id="studentRegisterForm" class="form hidden" method="POST" enctype="multipart/form-data">
    <h3>Öğrenci Kayıt</h3>
    <input type="text" id="name" name="name" placeholder="Ad Soyad" required>
    <input type="email" id="email" name="email" placeholder="Email" required>
    <input type="password" id="password" name="password" placeholder="Şifre" required>

    <h3>Belge Yükleme</h3>
    <label for="studentDocument">Belge Seçin (PDF, JPG, PNG):</label>
    <input type="file" name="studentDocument" id="studentDocument" required>

    <button type="submit">Kayıt Ol ve Yükle</button>
</form>

<script>
    document.getElementById('studentRegisterForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Formun sayfayı yenilemesini engeller

        // Form gönderildikten sonra sayfa yenilenmesi için:
        setTimeout(function() {
            location.reload(); // Sayfa yenilenir
        }, 2000); // 2 saniye bekleyip sayfa yenileniyor
    });
</script>

        

        <!-- Bilgilendirme Mesajı -->
        <div id="confirmationMessage" class="hidden">
            <span id="closeMessage" class="close">&times;</span>
            <p>Belge başarıyla yüklendi!</p>
        </div>
    </div>

    <!-- Kullanıcı Portalı -->
    <div id="userPortal" class="portal hidden">
        <h2>Kullanıcı Portalı</h2>
        <div class="form-toggle">
            <button id="userLoginBtn" class="active">Kullanıcı Giriş</button>
            <button id="userRegisterBtn">Kullanıcı Kayıt</button>
        </div>

        <!-- Kullanıcı Giriş Formu -->
        <form id="userLoginForm" class="form">
            <h3>Kullanıcı Giriş</h3>
            <input type="email" id="userEmail" name="userEmail" placeholder="Email" required>
            <input type="password" id="userPassword" name="userPassword" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
        </form>

        <script>
            document.getElementById('userLoginForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Formun sayfayı yenilemesini engeller
        
                // Form gönderildikten sonra sayfa yenilenmesi için:
                setTimeout(function() {
                    location.reload(); // Sayfa yenilenir
                }, 2000); // 2 saniye bekleyip sayfa yenileniyor
            });
        </script>

        <!-- Kullanıcı Kayıt Formu -->
        <form id="userRegisterForm" class="form hidden">
            <h3>Kullanıcı Kayıt</h3>
            <input type="text" id="userName" name="userName" placeholder="Kullanıcı Adı" required>
            <input type="email" id="userRegEmail" name="userRegEmail" placeholder="Email" required>
            <input type="password" id="userRegPassword" name="userRegPassword" placeholder="Şifre" required>
            <button type="submit">Kayıt Ol</button>
        </form>
    </div>

    <!-- Admin Portalı -->
    <div id="adminPortal" class="portal hidden">
        <h2>Admin Portalı</h2>
        <form id="adminLoginForm" class="form">
            <h3>Admin Giriş</h3>
            <input type="email" id="adminEmail" name="adminEmail" placeholder="Email" required>
            <input type="password" id="adminPassword" name="adminPassword" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
            <p id="errorMessage" class="hidden" style="color:red;">Yanlış email veya şifre!</p>
        </form>
    </div>

    <script>
        // Portal geçişleri
        document.getElementById('studentPortalBtn').addEventListener('click', function() {
            document.getElementById('studentPortal').classList.remove('hidden');
            document.getElementById('userPortal').classList.add('hidden');
            document.getElementById('adminPortal').classList.add('hidden');
        });

        document.getElementById('userPortalBtn').addEventListener('click', function() {
            document.getElementById('userPortal').classList.remove('hidden');
            document.getElementById('studentPortal').classList.add('hidden');
            document.getElementById('adminPortal').classList.add('hidden');
        });

        document.getElementById('adminPortalBtn').addEventListener('click', function() {
            document.getElementById('adminPortal').classList.remove('hidden');
            document.getElementById('studentPortal').classList.add('hidden');
            document.getElementById('userPortal').classList.add('hidden');
        });

        // Öğrenci kaydından sonra belge yükleme formunu göster
        document.getElementById('studentRegisterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('documentUploadForm').style.display = 'block';
        });
    </script>

    <script src="login.js"></script>
</body>
</html>
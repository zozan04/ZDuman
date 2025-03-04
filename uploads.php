<?php
// Hata raporlamayı aç
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Form gönderildiğinde işlem yap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen verileri al
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $studentDocument = $_FILES['studentDocument'] ?? null;

    // Şifreyi hashle
    $password = hash('sha256', $password);

    // Dosyaların kaydedileceği dizin
    $uploadDir = "uploads/" . $email;

    // Eğer klasör yoksa oluştur
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // 0777 izinleri ile klasörü oluştur
    }

    // Eğer dosya var ve yüklendiyse
    if (isset($studentDocument) && $studentDocument['error'] == UPLOAD_ERR_OK) {
        // Dosya türü kontrolü
        $fileExtension = strtolower(pathinfo($studentDocument["name"], PATHINFO_EXTENSION));
        $allowedTypes = ["pdf", "jpg", "png"];

        // Geçerli dosya türü kontrolü
        if (in_array($fileExtension, $allowedTypes)) {
            // Dosyanın benzersiz adını oluşturmak için zaman damgası ekleyelim
            $newFileName = time() . "_" . basename($studentDocument["name"]);
            $targetFile = $uploadDir . "/" . $newFileName;

            // Dosyayı belirtilen dizine kaydetme
            if (move_uploaded_file($studentDocument["tmp_name"], $targetFile)) {
                $confirmationMessage = "Belge başarıyla yüklendi!";
            } else {
                $confirmationMessage = "Dosya yüklenirken bir hata oluştu!";
            }
        } else {
            $confirmationMessage = "Geçersiz dosya türü! Yalnızca PDF, JPG veya PNG dosyaları yüklenebilir.";
        }
    } else {
        // Dosya yüklenirken hata oluşmuşsa
        $confirmationMessage = "Dosya yüklenirken bir hata oluştu. Hata kodu: " . ($studentDocument['error'] ?? 'Bilinmeyen hata');
    }

    // Form verilerini .txt dosyasına kaydetme
    $data =  $name . "\n" .
             $email . "\n" .
             $password . "\n" .
             ($newFileName ?? '') . "\n"; // Yüklenen dosyanın adını kaydediyoruz

    $file = $uploadDir . "/data.txt"; // Verilerin kaydedileceği dosya
    file_put_contents($file, $data, FILE_APPEND);

    // Form verilerini işledikten sonra bildirim mesajını göndermek için yönlendirme
    header("Location: index.html?message=" . urlencode($confirmationMessage));
    exit();
}
?>

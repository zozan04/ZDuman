<?php
include_once("db_connection.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$uploadsDir = 'uploads'; // uploads dizininin yolu
$folders = array_filter(glob($uploadsDir . '/*'), 'is_dir'); // Sadece dizinleri al

$studentData = [];

// Tüm klasörleri ve data.txt dosyalarını oku
foreach ($folders as $folder) {
    $dataTxtPath = $folder . '/data.txt'; // Klasördeki data.txt dosyasının yolu

    if (file_exists($dataTxtPath)) {
        $data = file($dataTxtPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Satırları oku
        $chunkedData = array_chunk($data, 4); // Her 4 satır bir öğrenciye ait

        foreach ($chunkedData as $chunk) {
            if (count($chunk) === 4) {
                $studentData[] = [
                    'name' => $chunk[0],
                    'email' => $chunk[1],
                    'password' => $chunk[2],
                    'document' => $chunk[3],
                    'dataPath' => $dataTxtPath, // data.txt dosya yolunu ekle
                    'folderPath' => $folder,   // Klasör yolunu ekle
                ];
            }
        }
    }
}


    
// Onayla işlemi ile veriyi ekleme ve klasörü temizleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'approve') {
    $fileToProcess = $_POST['fileToProcess'];

    foreach ($studentData as $student) {
        if ($student['document'] === $fileToProcess) {
            $name = $student['name'];
            $email = $student['email'];
            $password = $student['password'];
            $document = $student['document'];
            $dataTxtPath = $student['dataPath'];
            $folderPath = $student['folderPath'];

            // Veritabanına veri ekleme
            $sql = "INSERT INTO students (name, email, password, document)
                    VALUES ('$name', '$email', '$password', '$document')";
            
            if ($conn->query($sql) === TRUE) {
                // data.txt dosyasından silme işlemi
                $newData = "";
                foreach ($studentData as $remainingStudent) {
                    if ($remainingStudent['document'] !== $fileToProcess) {
                        $newData .= $remainingStudent['name'] . "\n" .
                                    $remainingStudent['email'] . "\n" .
                                    $remainingStudent['password'] . "\n" .
                                    $remainingStudent['document'] . "\n";
                    }
                }
                file_put_contents($dataTxtPath, $newData);

                // Klasördeki tüm dosyaları silme
                $files = glob($folderPath . '/*'); // Klasördeki tüm dosyaları listele
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file); // Dosyayı sil
                    }
                }

                // Boş klasörü silme
                if (is_dir($folderPath)) {
                    if (rmdir($folderPath)) {
                        echo "Klasör başarıyla silindi!";
                    } else {
                        echo "Klasör silinemedi. Lütfen izinleri kontrol edin.";
                    }
                }

                echo "Veri başarıyla veritabanına kaydedildi, data.txt dosyasından silindi ve klasör temizlendi!";
                header("Location: admin.php"); // admin.php sayfasına yönlendirme
                exit; // Yönlendirmeden sonra scriptin çalışmaya devam etmesini engelle
            } else {
                echo "Veri eklenirken bir hata oluştu: " . $conn->error;
            }
        }
    }
}


// Reddet işlemi ile dosyayı silme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'reject') {
    $fileToProcess = $_POST['fileToProcess'];

    foreach ($studentData as $student) {
        if ($student['document'] === $fileToProcess) {
            $dataTxtPath = $student['dataPath'];
            $folderPath = $student['folderPath'];
            $documentPath = $folderPath . '/' . $student['document'];

                        // Klasördeki tüm dosyaları silme
                        $files = glob($folderPath . '/*'); // Klasördeki tüm dosyaları listele
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                unlink($file); // Dosyayı sil
                            }
                        }
            
                        // Boş klasörü silme
                        if (is_dir($folderPath)) {
                            if (rmdir($folderPath)) {
                                echo "Klasör başarıyla silindi!";
                            } else {
                                echo "Klasör silinemedi. Lütfen izinleri kontrol edin.";
                            }
                        } else {
                            echo "Belirtilen klasör mevcut değil.";
                        }
            

            

            echo "Dosya başarıyla silindi ve data.txt dosyasından kaldırıldı!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
</head>
<body>
    <h1>Admin Paneli</h1>

    <h3>Yüklenen Belgeler ve Öğrenci Verileri</h3>

    <?php if (count($studentData) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Öğrenci Adı</th>
                    <th>Öğrenci Email</th>
                    <th>Öğrenci Şifresi</th>
                    <th>Yüklenen Belge</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentData as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['password']); ?></td>
                        <td><a href="uploads/<?php echo htmlspecialchars($student['email']); ?>/<?php echo htmlspecialchars($student['document']); ?>" target="_blank"><?php echo htmlspecialchars($student['document']); ?></a></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="fileToProcess" value="<?php echo htmlspecialchars($student['document']); ?>">
                                <button type="submit" name="action" value="approve">Onayla</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="fileToProcess" value="<?php echo htmlspecialchars($student['document']); ?>">
                                <button type="submit" name="action" value="reject">Reddet</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        Henüz bir belge yüklenmedi.
    <?php endif; ?>
</body>
</html>

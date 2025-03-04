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

$successMessage = '';  // Success message initialization
$errorMessage = '';    // Error message initialization
    
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
                        $successMessage = "Klasör başarıyla silindi!";
                    } else {
                        $errorMessage = "Klasör silinemedi. Lütfen izinleri kontrol edin.";
                    }
                }

                $successMessage = "Veri başarıyla veritabanına kaydedildi, data.txt dosyasından silindi ve klasör temizlendi!";
                header("Location: admin.php"); // admin.php sayfasına yönlendirme
                exit; // Yönlendirmeden sonra scriptin çalışmaya devam etmesini engelle
            } else {
                $errorMessage = "Veri eklenirken bir hata oluştu: " . $conn->error;
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
                rmdir($folderPath);
            }

            $successMessage = "Belge başarıyla reddedildi ve klasör silindi!";
            // İşlem tamamlandıktan sonra sayfayı yeniden yönlendir
            header("Location: admin.php");
            exit; // Yönlendirme yapıldıktan sonra kodun çalışmasını durdur
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
    <style>
        body {
            background-image: url('https://www.floryabasakyemek.com/wp-content/uploads/2018/06/florya-basak-yemek-header-1600x925.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 150px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 20px;
            padding-right:50px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .navbar {
            background-color: #511212;
            overflow: hidden;
            padding: 10px 0;
            z-index: 2;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .table th {
            background-color: #511212;
            color: white;
        }
        .table td a {
            color: #511212;
            text-decoration: none;
        }
        .table td a:hover {
            text-decoration: underline;
        }
        .btn {
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            border: none;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .alert {
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-top: 20px;
        }
        .alert-info {
            background-color: #511212;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navbar">
            <h2>Admin Yönetim Sistemi</h2>
        </div>
        <div class="card-body">
            <!-- Success or error message -->
            <?php if ($successMessage || $errorMessage): ?>
                <div id="alert-container">
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?= $successMessage ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"><?= $errorMessage ?></div>
                    <?php endif; ?>
                </div>
                <script>
                    // 2 saniye sonra mesajları gizle
                    setTimeout(() => {
                        const alertContainer = document.getElementById('alert-container');
                        if (alertContainer) {
                            alertContainer.style.opacity = '0';
                            setTimeout(() => {
                                alertContainer.style.display = 'none';
                            }, 500); // Geçişin tamamlanması için biraz süre ver
                        }
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if (count($studentData) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Adı</th>
                            <th>Email</th>
                            <th>Şifre</th>
                            <th>Belge</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentData as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                                <td><?= htmlspecialchars($student['password']) ?></td>
                                <td><a href="<?= $uploadsDir . '/' . htmlspecialchars($student['email']) . '/' . htmlspecialchars($student['document']) ?>" target="_blank"><?= htmlspecialchars($student['document']) ?></a></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="fileToProcess" value="<?= htmlspecialchars($student['document']) ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-success">Onayla</button>
                                    </form>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="fileToProcess" value="<?= htmlspecialchars($student['document']) ?>">
                                        <button type="submit" name="action" value="reject" class="btn btn-danger">Reddet</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">Bekleyen İşlem Bulunmuyor.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

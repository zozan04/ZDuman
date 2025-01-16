<?php
include_once("db_connection.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$uploadsDir = 'uploads'; // uploads dizininin yolu
$folders = array_filter(glob($uploadsDir . '/*'), 'is_dir'); // Sadece dizinleri al

$studentData = [];

foreach ($folders as $folder) {
    $folderName = basename($folder); 
    $dataTxtPath = $folder . '/data.txt'; // Klasördeki data.txt dosyasının yolu

    // Data.txt dosyasını oku
    if (file_exists($dataTxtPath)) {
        $data = file_get_contents($dataTxtPath); // data.txt içeriğini oku
        $dataLines = explode("\n", $data); // Data.txt içeriğini satırlara ayıralım
        
        $currentStudent = [];
        foreach ($dataLines as $line) {
            if (strpos($line, 'Öğrenci Adı:') === 0) {
                if (!empty($currentStudent)) {
                    $studentData[] = $currentStudent;
                }
                $currentStudent = ['name' => trim(substr($line, 14))];
            } elseif (strpos($line, 'Öğrenci Email:') === 0) {
                $currentStudent['email'] = trim(substr($line, 15));
            } elseif (strpos($line, 'Öğrenci Şifresi:') === 0) {
                $currentStudent['password'] = trim(substr($line, 18));
            } elseif (strpos($line, 'Yüklenen Belge:') === 0) {
                $currentStudent['document'] = trim(substr($line, 17));
            }
        }

        if (!empty($currentStudent)) {
            $studentData[] = $currentStudent; // Son öğrenci verisini ekle
        }
    }
}

// Onayla işlemi ile veriyi ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'approve') {
    $fileToProcess = $_POST['fileToProcess'];
    $currentStudent = [];
    
    // Veritabanına veri ekleme
    foreach ($studentData as $student) {
        if ($student['document'] === $fileToProcess) {
            $name = $student['name'];
            $email = $student['email'];
            $password = $student['password'];
            $document = $student['document'];
            
            // Veritabanına veri ekleme
            $sql = "INSERT INTO students (name, email, password, document)
                    VALUES ('$name', '$email', '$password', '$document')";
            
            if ($conn->query($sql) === TRUE) {
                // Data.txt dosyasından silme
                $newData = "";
                foreach ($studentData as $remainingStudent) {
                    if ($remainingStudent['document'] !== $fileToProcess) {
                        $newData .= "Öğrenci Adı: " . $remainingStudent['name'] . "\n" .
                                    "Öğrenci Email: " . $remainingStudent['email'] . "\n" .
                                    "Öğrenci Şifresi: " . $remainingStudent['password'] . "\n" .
                                    "Yüklenen Belge: " . $remainingStudent['document'] . "\n" .
                                    "-------------------------\n";
                    }
                }

                // Yeni veriyi data.txt'ye yaz
                file_put_contents($dataTxtPath, $newData);
                
                echo "Veri başarıyla veritabanına kaydedildi!";
            } else {
                echo "Veri eklenirken bir hata oluştu: " . $conn->error;
            }
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
                <?php 
                // Her belgeden gelen tüm öğrenci verilerini yazdır
                foreach ($studentData as $student): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['password']); ?></td>
                        <td><a href="uploads/<?php echo htmlspecialchars($student['email']); ?>/<?php echo htmlspecialchars($student['document']); ?>" target="_blank"><?php echo htmlspecialchars($student['document']); ?></a></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="fileToProcess" value="<?php echo htmlspecialchars($student['document']); ?>">
                                <button type="submit" name="action" value="approve">Onayla</button>
                                <button type="submit" name="action" value="reject">Reddet</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                endforeach; 
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Henüz bir belge yüklenmedi.</p>
    <?php endif; ?>

</body>
</html>

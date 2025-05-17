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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'approve') {
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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reject') {
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


// Durum güncelleme
if (isset($_POST['update_order_status'])) {
    $order_id = intval($_POST['order_id']);
    $meal_id = intval($_POST['meal_id']);
    $is_guest = ($_POST['is_guest'] === '1');

    if ($is_guest) {
        $update_stmt = $conn->prepare("UPDATE guest_order_items SET status = 'Teslim Edildi' WHERE guest_order_id = ? AND meal_id = ?");
    } else {
        $update_stmt = $conn->prepare("UPDATE order_items SET status = 'Teslim Edildi' WHERE order_id = ? AND meal_id = ?");
    }

    $update_stmt->bind_param("ii", $order_id, $meal_id);
    $update_stmt->execute();

    $update_stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Sipariş ve öğeleri çekme
$sql_orders = "
SELECT o.id AS order_id, 'user' AS order_type, oi.meal_id, oi.status,
u.name AS customer_name, o.address, o.city, o.postal_code, o.phone
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN users u ON o.user_id = u.id
WHERE oi.status = 'Kargoya Verildi'
";

$sql_guest_orders = "
SELECT go.id AS order_id, 'guest' AS order_type, goi.meal_id, goi.status,
g.full_name AS customer_name, go.address, go.city, go.postal_code, g.phone
FROM guest_orders go
JOIN guest_order_items goi ON go.id = goi.guest_order_id
JOIN guests g ON go.guest_id = g.id
WHERE goi.status = 'Kargoya Verildi'
";

$sql = "(" . $sql_orders . ") UNION ALL (" . $sql_guest_orders . ") ORDER BY order_id";

$result = $conn->query($sql);

$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    die("Sorgu hatası: " . $conn->error);
}



?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->

    <title>Admin Paneli</title>
    <style>
        
    </style>
</head>

<body>

    <div class="navbar">
        <h2>Admin Yönetim Sistemi</h2>
        <div class="icons-container"> 
            <div class="login-containers">
                <div class="circle login-icon" onclick="toggleLogoutMenu()">
                    <i class="fas fa-user" style="color: black; font-size: 20px;"></i>
                </div>
                <div id="logout-menu" class="logout-menu">
                    <a href="admin_cikis.php">Çıkış Yap</a>
                </div>
            </div>
        </div>
    </div>


    <!-- profil işlemleri -->
   


        
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
                            <th>Belge</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentData as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                                
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


 <div class="cargo-container">
<h1>Kargo Takip Paneli</h1>

<?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <p>Henüz kargo takibi için sipariş yok.</p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Sipariş ID</th>
                <th>Ad Soyad</th>
                <th>Telefon</th>
                <th>Adres</th>
                <th>Şehir</th>
                <th>Posta Kodu</th>
                <th>Meal ID</th>
                <th>Durum</th>
                <th>Durumu Güncelle</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr class="<?= $order['status'] === 'Teslim Edildi' ? 'delivered' : 'pending' ?>">
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['phone']) ?></td>
                <td><?= nl2br(htmlspecialchars($order['address'])) ?></td>
                <td><?= htmlspecialchars($order['city']) ?></td>
                <td><?= htmlspecialchars($order['postal_code']) ?></td>
                <td><?= htmlspecialchars($order['meal_id']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td>
                    <?php if ($order['status'] !== 'Teslim Edildi'): ?>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>" />
                            <input type="hidden" name="meal_id" value="<?= $order['meal_id'] ?>" />
                            <input type="hidden" name="is_guest" value="<?= $order['order_type'] === 'guest' ? '1' : '0' ?>" />
                            <button type="submit" name="update_order_status">Teslim Edildi Olarak İşaretle</button>
                        </form>
                    <?php else: ?>
                        <em>Teslim Edildi</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

</div>



    <script src="admin.js"></script>
</body>
</html>

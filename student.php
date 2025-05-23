<?php
include('db_connection.php');
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = intval($_POST['order_id']);
    $meal_id = intval($_POST['meal_id']);
    $is_guest = isset($_POST['is_guest']) && $_POST['is_guest'] === '1';

    if ($is_guest) {
        // guest_order_items tablosunda hem guest_order_id hem de meal_id eşleşmeli
        $update = $conn->prepare("UPDATE guest_order_items SET status = 'Kargoya Verildi' WHERE guest_order_id = ? AND meal_id = ?");
    } else {
        // order_items tablosunda hem order_id hem de meal_id eşleşmeli
        $update = $conn->prepare("UPDATE order_items SET status = 'Kargoya Verildi' WHERE order_id = ? AND meal_id = ?");
    }

    $update->bind_param("ii", $order_id, $meal_id);

    if ($update->execute()) {
        // Başarılıysa sayfayı yeniden yükle
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        echo "<p style='color:red;'>Sipariş durumu güncellenirken hata oluştu: " . $update->error . "</p>";
    }
}




// Eğer öğrenci giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['student_id'])) {
    header("Location: ogrenci_giris.php");
    exit();
}

$student_id = $_SESSION['student_id']; // Get the logged-in student's ID


/// Yemek ekleme işlemi
$message = "";
$edit_mode = false;
$edit_meal = [
    'id' => '',
    'category' => '',
    'name' => '',
    'price' => '',
    'city' => '',
    'content' => '',
    'image_path' => ''
];

// Düzenlenecek yemeğin bilgilerini al
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql_edit = "SELECT * FROM meals WHERE id = '$edit_id' AND user_id = '$student_id'";
    $result_edit = $conn->query($sql_edit);

    if ($result_edit->num_rows > 0) {
        $edit_mode = true;
        $edit_meal = $result_edit->fetch_assoc();
    }
}

// Yemek ekleme
if (isset($_POST['submit'])) {
    $kategori = $_POST['kategori'];
    $yemek_adi = $_POST['yemek_adi'];
    $fiyat = $_POST['fiyat'];
    $icerik = preg_replace('/\s+/', ' ', trim($_POST['icerik']));
    $sehir = $_POST['city'];

    if (!isset($_FILES['resim']) || $_FILES['resim']['error'] !== UPLOAD_ERR_OK) {
        $message = "<div class='error'>Lütfen bir dosya seçin!</div>";
    } else {
        $resim_name = $_FILES['resim']['name'];
        $resim_tmp_name = $_FILES['resim']['tmp_name'];
        $upload_dir = 'resimler/';
        $upload_path = $upload_dir . basename($resim_name);

        if (!move_uploaded_file($resim_tmp_name, $upload_path)) {
            $message = "<div class='error'>Görsel yüklenirken bir hata oluştu.</div>";
        } else {
            $sql = "INSERT INTO meals (user_id, name, category, image_path, price, content, city) 
                    VALUES ('$student_id', '$yemek_adi', '$kategori', '$upload_path', '$fiyat', '$icerik', '$sehir')";

            if ($conn->query($sql) === TRUE) {
                header("Location: student.php");
                exit();
            } else {
                $message = "<div class='error'>Hata: " . $conn->error . "</div>";
            }
        }
    }
}

// Yemek güncelleme
if (isset($_POST['update'])) {
    $meal_id = $_POST['id'];
    $kategori = $_POST['kategori'];
    $yemek_adi = $_POST['yemek_adi'];
    $fiyat = $_POST['fiyat'];
    $sehir = $_POST['city'];
    $icerik = preg_replace('/\s+/', ' ', trim($_POST['icerik']));

    // Görsel yüklenmişse güncelle
    if (isset($_FILES['resim']) && $_FILES['resim']['error'] === UPLOAD_ERR_OK) {
        $resim_name = $_FILES['resim']['name'];
        $resim_tmp_name = $_FILES['resim']['tmp_name'];
        $upload_dir = 'resimler/';
        $upload_path = $upload_dir . basename($resim_name);

        if (move_uploaded_file($resim_tmp_name, $upload_path)) {
            $sql_update = "UPDATE meals SET name='$yemek_adi', category='$kategori', price='$fiyat', content='$icerik', city='$sehir', image_path='$upload_path' WHERE id='$meal_id' AND user_id='$student_id'";
        }
    } else {
        $sql_update = "UPDATE meals SET name='$yemek_adi', category='$kategori', price='$fiyat', content='$icerik', city='$sehir' WHERE id='$meal_id' AND user_id='$student_id'";
    }

    if ($conn->query($sql_update) === TRUE) {
        $message = "<div class='success'>Yemek başarıyla güncellendi.</div>";
        header("Location: student.php");
        exit();
    } else {
        $message = "<div class='error'>Güncelleme hatası: " . $conn->error . "</div>";
    }
}

// Silme işlemi
if (isset($_GET['delete'])) {
    $meal_id = $_GET['delete'];
    $sql_check = "SELECT * FROM meals WHERE id = '$meal_id' AND user_id = '$student_id'";
    $check = $conn->query($sql_check);

    if ($check->num_rows > 0) {
        $sql_delete = "DELETE FROM meals WHERE id = '$meal_id'";
        if ($conn->query($sql_delete) === TRUE) {
            $message = "<div class='success'>Yemek başarıyla silindi.</div>";
        } else {
            $message = "<div class='error'>Silme hatası.</div>";
        }
    } else {
        $message = "<div class='error'>Bu yemeği silme yetkiniz yok.</div>";
    }
}

// Oturum açmış öğrencinin yemeklerini getiren SQL sorgusu
$sql_get_meals = "SELECT * FROM meals WHERE user_id = '$student_id'";
$result_get_meals = $conn->query($sql_get_meals);



// Notları getiren fonksiyon
function getNotesByMealId($conn, $meal_id) {
    $notes = [];

    // meal_notes
    $stmt_user = $conn->prepare("SELECT note FROM meal_notes WHERE meal_id = ?");
    $stmt_user->bind_param("i", $meal_id);
    $stmt_user->execute();
    $res_user = $stmt_user->get_result();
    while ($row = $res_user->fetch_assoc()) {
        $notes[] = htmlspecialchars($row['note']);
    }

    // guest_notes
    $stmt_guest = $conn->prepare("SELECT note FROM guest_notes WHERE meal_id = ?");
    $stmt_guest->bind_param("i", $meal_id);
    $stmt_guest->execute();
    $res_guest = $stmt_guest->get_result();
    while ($row = $res_guest->fetch_assoc()) {
        $notes[] = htmlspecialchars($row['note']);
    }

    return $notes;
}

// Öğrencinin yemeklerine ait siparişleri getir
$sql = "
SELECT 
    go.id AS order_id,
    m.name AS meal_name,
    m.id AS meal_id,
    m.price,
    goi.quantity,
    go.total_price,
    goi.status AS item_status ,
    'guest' AS order_type
FROM guest_orders go
JOIN guests g ON g.id = go.guest_id
JOIN guest_order_items goi ON goi.guest_order_id = go.id
JOIN meals m ON m.id = goi.meal_id
WHERE m.user_id = ?

UNION ALL

SELECT 
    o.id AS order_id,
    m.name AS meal_name,
    m.id AS meal_id,
    m.price,
    oi.quantity,
    o.total_price,
    oi.status AS item_status,
    'user' AS order_type
FROM orders o
JOIN users u ON u.id = o.user_id
JOIN order_items oi ON oi.order_id = o.id
JOIN meals m ON m.id = oi.meal_id
WHERE m.user_id = ?
ORDER BY order_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $student_id); // $student_id oturumdan alınmalı
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$groupedOrders = []; // ✅ Eksik olan bu!

// Siparişleri sipariş ID'sine göre grupla
foreach ($orders as $order) {
    $orderId = $order['order_id'];
    if (!isset($groupedOrders[$orderId])) {
        $groupedOrders[$orderId] = [
            'order_id' => $orderId,
            'total_price' => $order['total_price'],
            'status' => $order['item_status'],
            'order_type' => $order['order_type'],  // BURAYA EKLENDİ
            'items' => []
        ];
    }

    $groupedOrders[$orderId]['items'][] = [
        'meal_name' => $order['meal_name'],
        'meal_id' => $order['meal_id'],
        'price' => $order['price'],
        'quantity' => $order['quantity'],
        'status' => $order['item_status'],
        'notes' => getNotesByMealId($conn, $order['meal_id'])
    ];
}


//yorumları getirme
$sql = "SELECT 
            e.comment,
            e.created_at,
            u.name AS user_name,
            m.name AS meal_name
        FROM evaluations e
        JOIN meals m ON e.meal_id = m.id
        JOIN students u ON e.user_id = u.id
        WHERE m.user_id = ?
        ORDER BY e.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
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

            <div class="login-containers">
                <div class="circle login-icon" onclick="toggleLogoutMenu()">
                    <i class="fas fa-user" style="color: black; font-size: 20px;"></i>
                </div>
                <div id="logout-menu" class="logout-menu">
                    <a href="#" onclick="openSettingsModal()">Ayarlar</a>
                    <a href="kullanici_cikis.php">Çıkış Yap</a>
                </div>
            </div>
        </div>
    </div>
</div>


<button onclick="openSettingsModal()">Bilgileri Güncelle</button>

<!-- Öğrenci Bilgilerini Güncelleme Modalı -->
<div id="settingsModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeSettingsModal()">&times;</span>
    <h2>Öğrenci Bilgilerini Güncelle</h2>
    <form id="updateStudentForm" action="update_settings.php" method="POST">

      <!-- Öğrenci ID (Gizli alan, örneğin SESSION ile PHP'de doldurulur) -->
      <input type="hidden" id="student_id" name="student_id" value="<?php echo $_SESSION['student_id']; ?>">

      <!-- Öğrenci Adı -->
      <label for="student_name">Ad:</label>
      <input type="text" id="student_name" name="name" required><br>

      <!-- Öğrenci E-posta -->
      <label for="student_email">E-posta:</label>
      <input type="email" id="student_email" name="email" required><br>

      <!-- Yeni Şifre -->
      <label for="student_password">Yeni Şifre (İsteğe Bağlı):</label>
      <input type="password" id="student_password" name="password"><br>

      <button type="submit">Güncelle</button>
    </form>
    <div id="updateMessage" style="margin-top: 10px; "></div>
  </div>
</div>



<!-- Yemek Ekleme ve Güncelleme Formu -->

<?= $message ?>

<!-- Form -->
<div class="form-container">
    <h3><?= $edit_mode ? "Yemeği Güncelle" : "Yemek Ekle" ?></h3>
    <form action="student.php" method="POST" enctype="multipart/form-data" class="form-grid">
        <div class="form-left">
            <label>Yemek Kategorisi:</label>
            <select name="kategori" required>
                <?php
                $kategoriler = ['Ana Yemekler', 'Sulu Yemekler', 'Karbonhidrat Lezzetleri', 'Aperatifler', 'Tatlı Çeşitleri'];
                foreach ($kategoriler as $k) {
                    $selected = ($edit_meal['category'] == $k) ? 'selected' : '';
                    echo "<option value='$k' $selected>$k</option>";
                }
                ?>
            </select><br>

            <label>Yemek Adı:</label>
            <input type="text" name="yemek_adi" value="<?= htmlspecialchars($edit_meal['name']) ?>" required><br>

            <label>Fiyat:</label>
            <input type="number" name="fiyat" step="0.01" value="<?= htmlspecialchars($edit_meal['price']) ?>" required><br>

            <label>Şehir:</label>
            <input type="text" name="city" value="<?= htmlspecialchars($edit_meal['city']) ?>" required><br>
        </div>

        <div class="form-right">
            <label>İçerik:</label>
            <textarea name="icerik" rows="6" cols="60" required><?= htmlspecialchars($edit_meal['content']) ?></textarea><br>

            <label>Görsel:</label>
            <input type="file" name="resim" accept="image/*"><br>
            <?php if ($edit_mode): ?>
                <img src="<?= $edit_meal['image_path'] ?>" alt="Yemek Resmi" width="100"><br>
            <?php endif; ?>

            <input type="hidden" name="id" value="<?= $edit_meal['id'] ?>">

            <div class="button-container">
                <input type="submit" name="submit" value="Yemek Ekle" <?= $edit_mode ? 'disabled' : '' ?>>
                <input type="submit" name="update" value="Yemek Güncelle" <?= !$edit_mode ? 'disabled' : '' ?>>
            </div>
        </div>
    </form>
</div>
    
<div class="container-status">
    <h2>Gelen Siparişler</h2>

    <?php if (count($groupedOrders) > 0): ?>
    <table>
        <tr>
            <th>Sipariş ID</th>
            <th>Yemek Adı</th>
            <th>Yemek ID</th>
            <th>Fiyat</th>
            <th>Miktar</th>
            <th>Durum</th>
            <th>Yemek Notları</th>
            <th>İşlem</th>
        </tr>

        <?php foreach ($groupedOrders as $order): ?>
            <?php foreach ($order['items'] as $index => $item): ?>
                <tr>
                    <?php if ($index === 0): ?>
                        <td rowspan="<?= count($order['items']) ?>"><?= htmlspecialchars($order['order_id']) ?></td>
                    <?php endif; ?>

                    <td><?= htmlspecialchars($item['meal_name']) ?></td>
                    <td><?= htmlspecialchars($item['meal_id']) ?></td>
                    <td><?= number_format($item['price'], 2) ?>₺</td>
                    <td><?= $item['quantity'] ?></td>
                    
                    <!-- Her yemek için ayrı status -->
                    <td><?= htmlspecialchars($item['status'] ?? 'Hazırlanıyor') ?></td>
                    
                    <!-- Her yemek için ayrı notlar -->
                    <td>
                        <?php
                            if (!empty($item['notes'])) {
                                echo "<ul>";
                                foreach ($item['notes'] as $note) {
                                    echo "<li>" . htmlspecialchars($note) . "</li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<em>Not eklenmemiş</em>";
                            }
                        ?>
                    </td>

                    <!-- Her yemek için ayrı kargo durumu / butonu -->
                    <td>
                        <?php if (($item['status'] ?? '') !== 'Kargoya Verildi'): ?>
                            <form method="POST">
    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
    <input type="hidden" name="meal_id" value="<?= htmlspecialchars($item['meal_id']) ?>">
  
<input type="hidden" name="is_guest" value="<?php echo ($order['order_type'] === 'guest') ? '1' : '0'; ?>" />

    <button type="submit" name="update_order_status">Kargoya Ver</button>
</form>
                        <?php else: ?>
                            <span style="color: #a71d2a; font-weight: bold;">Kargoya Verildi</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p class="no-orders">Henüz sipariş yok.</p>
    <?php endif; ?>
</div>

    </div>
    
</div>
<!-- Yemek Listesi -->
<div class="container">
    <table class="meals-table">
        <thead>
            <tr>
                <th>Resim</th>
                <th>Yemek Adı</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>Şehir</th>
                <th>İçerik</th>
                <th>Sil</th>
                <th>Düzenle</th>
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
                    echo "<td>" . $meal['city'] . "</td>";
                    echo "<td>" . $meal['content'] . "</td>";
                    echo "<td><a href='student.php?delete=" . $meal['id'] . "' class='delete-btn'>Sil</a></td>";
                    echo "<td><a href='student.php?edit=" . $meal['id'] . "' class='edit-btn'>Düzenle</a></td>";
                    echo "</tr>";
                    
                }
            } else {
                echo "<tr><td colspan='7'>Henüz yemek eklenmemiş.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


<h2>Yemeklerinize Yapılan Yorumlar</h2>
<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="comment-box">
            <p><strong>Yemek:</strong> <?= htmlspecialchars($row['meal_name']) ?></p>
            <p><strong>Yorumu Yapan:</strong> <?= htmlspecialchars($row['user_name']) ?></p>
            <p><strong>Yorum:</strong> <?= nl2br(htmlspecialchars($row['comment'])) ?></p>
            <p><em>Tarih: <?= $row['created_at'] ?></em></p>
            <hr>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Henüz yemeklerinize yorum yapılmamış.</p>
<?php endif; ?>
</div>
<script src="student.js"></script>

</body>
</html>

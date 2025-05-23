<?php
session_start();
include('db_connection.php'); // Veritabanı bağlantısı dosyası

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    header('Location: kullanici_giris.php'); // Giriş yapılmamışsa login sayfasına yönlendir
    exit();
}

// Kullanıcı bilgilerini almak
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Eğer form gönderildiyse bilgileri güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    // Parolayı şifreleyerek kaydet
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Veritabanında güncelleme
    $update_sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $new_name, $new_email, $hashed_password, $user_id);
    $update_stmt->execute();

    // Başarı mesajı
    echo "Bilgileriniz başarıyla güncellendi!";
}

// Kullanıcının siparişlerini alalım
$orders = [];
$order_sql = "SELECT * FROM orders WHERE user_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

while ($order = $order_result->fetch_assoc()) {
    // Siparişe ait ürünleri getir
    $items_sql = "SELECT oi.*, m.name AS meal_name, m.image_path 
                  FROM order_items oi 
                  JOIN meals m ON oi.meal_id = m.id 
                  WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param("i", $order['id']);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();

    $order['items'] = [];
    while ($item = $items_result->fetch_assoc()) {
        $order['items'][] = $item;
    }

    $orders[] = $order;
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghibli Yemek Platformu</title>
    <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="user_settings.css">
    <script src="https://unpkg.com/scrollreveal"></script>
</head>
<body>
<header class="main-header">
        <nav>
            <div class="nav_bar">
                <div class="logo">
                    <img src="resim/A7.jpg" alt="GYP Logo">
                </div>
                <!-- Menüleri ekliyoruz -->
                <ul class="menu">
                    <li><a href="index.html">Anasayfa</a></li>
                    <li><a href="#hakkimizda">Hakkımızda</a></li>
                    <li><a href="#yemekler">Yemekler</a></li>
                    <li><a href="#iletisim">İletişim</a></li>
                    
                </ul>
                
            </div>
           
        </nav>
        
        <div class="icons-container"> 
            <!-- Arama İkonu -->
            <div class="search-icon">
                <div class="circle" onclick="toggleSearchInput()">
                    <i class="fas fa-search search-icon" style="color: black; font-size: 20px;"></i>
                </div>
                
                <div id="search-box" style="display: none;"> <!-- Filtre kutusu -->
                    <input type="text" id="searchInput"  placeholder="Yemek arayın...">
                </div>
            </div>
            </div>
            <!-- Sepetim İkonu -->
            <a href="sepetim.php" class="cart-icon" title="Sepetim">
                <div class="circle">
                    <i class="fas fa-shopping-cart" style="color: black; font-size: 20px;"></i>
                </div>
            </a>
            <!-- Giriş İkonu (Oturum Açma ve Çıkış Menüsü) -->
            <div class="login-container">
                <div class="circle login-icon" onclick="toggleLogoutMenu()">
                    <i class="fas fa-user" style="color: black; font-size: 20px;"></i>
                </div>
                <div id="logout-menu" class="logout-menu">
                    <a href="kullanici_cikis.php">Çıkış Yap</a>
                </div>
            </div>
            <!-- Tema Değiştir İkonu -->
            <div class="light" id="light" title="Tema Değiştir">
                <div class="circle">
                    <i class="fas fa-sun" id="themeIcon" style="color: black; font-size: 20px;"></i>
                </div>
            </div>
            <a href="favoriler.php" class="favorites-icon" title="Favorilerim">
                <div class="circle">
                    <i class="fas fa-heart" style="color: black; font-size: 20px;"></i>
                </div>
            </a>
        </div>

    </header>
  
    <div class="container">
        <h2>Profil Bilgilerini Düzenle</h2>
        <form method="POST" action="user_settings.php">
            <div class="form-group">
                <label for="name">Ad Soyad:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Yeni Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Güncelle</button>
        </form>
    </div>

<div class="container-orders">
    <h2>Sipariş Geçmişi</h2>
    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-box">
                <div class="order-header">
                    <span><strong>Sipariş Numaranız:</strong> <?= $order['id'] ?></span>
                    <span><strong>Durum:</strong> <?= htmlspecialchars($order['items'][0]['status']) ?></span>
                    <span><strong>Toplam:</strong> <?= number_format($order['total_price'], 2) ?>₺</span>
                </div>
                <div class="order-address">
                    <p><strong>Adres:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?> <?= htmlspecialchars($order['postal_code']) ?></p>
                </div>
                <div class="order-items">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-item">
                            <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['meal_name']) ?>">
                            <div class="item-info">
                                <h4><?= htmlspecialchars($item['meal_name']) ?></h4>
                                <p><strong>Adet:</strong> <?= $item['quantity'] ?></p>
                                <p><strong>Fiyat:</strong> <?= number_format($item['price'], 2) ?>₺</p>
                                <?php if (!empty($item['note'])): ?>
                                    <p><em>Not: <?= htmlspecialchars($item['note']) ?></em></p>
                                <?php endif; ?>

                                <?php
                                $meal_id = $item['meal_id'];
                                $status = $item['status'];
                                $has_commented = false;
                                $user_comment = '';

                                // Yorum kontrolü
                                $comment_query = "
                                    SELECT comment FROM evaluations 
                                    WHERE user_id = $user_id 
                                    AND meal_id = $meal_id 
                                    LIMIT 1
                                ";
                                $comment_result = mysqli_query($conn, $comment_query);
                                if (mysqli_num_rows($comment_result) > 0) {
                                    $has_commented = true;
                                    $row = mysqli_fetch_assoc($comment_result);
                                    $user_comment = $row['comment'];
                                }
                                ?>

                                <?php if ($status === 'Teslim Edildi'): ?>
                                    <?php if ($has_commented): ?>
                                        <p><em>Yorumunuz: <?= htmlspecialchars($user_comment) ?></em></p>
                                    <?php else: ?>
                                        <form class="comment-form" data-meal-id="<?= $meal_id ?>">
                                            <textarea name="comment" rows="3" cols="50" placeholder="Yemeği nasıl buldunuz?" required></textarea><br>
                                            <button type="submit">Yorum Yap</button>
                                            <div class="comment-response"></div>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Henüz sipariş vermediniz.</p>
    <?php endif; ?>
</div>


    

    <footer>
        <!-- Footer içerik -->
    </footer>
    <script src="user_settings.js"></script>
    

</body>
</html>

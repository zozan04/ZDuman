<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("db_connection.php");


?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghibli Yemek Platformu</title>
    <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->
    <link rel="stylesheet" href="submit_order.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/scrollreveal"></script>

</head>
<body>
    <!-- Ana Ekran -->
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
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php">Giriş Yap</a>
                    <?php else: ?>
                        <a href="kullanici_cikis.php">Çıkış Yap</a>

                    <?php endif; ?>
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

    <main style="margin-top: 150px; text-align: center; padding: 20px;">
    <?php
// Formdan gelen veriler
$saved_address = isset($_POST['saved_address']) ? trim($_POST['saved_address']) : '';
$new_address   = isset($_POST['address']) ? trim($_POST['address']) : '';
$city          = isset($_POST['city']) ? trim($_POST['city']) : '';
$postal_code   = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
$card_number   = isset($_POST['card_number']) ? trim($_POST['card_number']) : '';
$expiry_date   = isset($_POST['expiry_date']) ? trim($_POST['expiry_date']) : '';
$cvv           = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';
$address       = !empty($new_address) ? $new_address : $saved_address;

// Misafir bilgileri
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name  = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$phone      = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email      = isset($_POST['email']) ? trim($_POST['email']) : '';
$full_name  = trim($first_name . ' ' . $last_name);

try {
    $total_price = 0;
    $shipping_cost = 2.00;

    if (isset($_SESSION['user_id'])) {
        // 🔹 Kullanıcı siparişi
        $user_id = $_SESSION['user_id'];

        $cartQuery = $conn->prepare("SELECT c.*, m.price FROM cart c JOIN meals m ON c.meal_id = m.id WHERE c.user_id = ?");
        $cartQuery->bind_param("i", $user_id);
        $cartQuery->execute();
        $cartItems = $cartQuery->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($cartItems as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }
        $total_price += $shipping_cost;

        $phone = $_POST['phone'];


     $orderInsert = $conn->prepare("INSERT INTO orders (user_id, total_price, address, city, postal_code, phone) VALUES (?, ?, ?, ?, ?, ?)");
$orderInsert->bind_param("idssss", $user_id, $total_price, $address, $city, $postal_code, $phone);
$phone = trim($_POST['phone']); // Eğer regex veya özel bir temizlik istiyorsan ayrıca yazabilirim



        if (!$orderInsert->execute()) {
            die("Sipariş eklenemedi: " . $orderInsert->error);
        }
        $order_id = $conn->insert_id;

        // 🔁 Artık status her yemek için ayrı kaydediliyor
        $orderItemInsert = $conn->prepare("INSERT INTO order_items (order_id, meal_id, quantity, price, status) VALUES (?, ?, ?, ?, 'Hazırlanıyor')");
        foreach ($cartItems as $item) {
            $orderItemInsert->bind_param("iiid", $order_id, $item['meal_id'], $item['quantity'], $item['price']);
            $orderItemInsert->execute();
        }

        $deleteCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $deleteCart->bind_param("i", $user_id);
        $deleteCart->execute();

    } elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // 🔹 Misafir siparişi
        $cartItems = $_SESSION['cart'];

        foreach ($cartItems as $meal_id => $item) {
            $priceQuery = $conn->prepare("SELECT price FROM meals WHERE id = ?");
            $priceQuery->bind_param("i", $meal_id);
            $priceQuery->execute();
            $meal = $priceQuery->get_result()->fetch_assoc();

            if ($meal) {
                $item_price = $meal['price'];
                $cartItems[$meal_id]['price'] = $item_price;
                $total_price += $item_price * $item['quantity'];
            }
            $priceQuery->close();
        }

        $total_price += $shipping_cost;

        // 🔹 Misafir bilgisi
        $checkGuest = $conn->prepare("SELECT id FROM guests WHERE phone = ?");
        $checkGuest->bind_param("s", $phone);
        $checkGuest->execute();
        $result = $checkGuest->get_result();

        if ($result->num_rows > 0) {
            $guest_id = $result->fetch_assoc()['id'];
        } else {
            $insertGuest = $conn->prepare("INSERT INTO guests (full_name, phone, email) VALUES (?, ?, ?)");
            $insertGuest->bind_param("sss", $full_name, $phone, $email);
            if (!$insertGuest->execute()) {
                die("Misafir bilgisi eklenemedi: " . $insertGuest->error);
            }
            $guest_id = $conn->insert_id;
            $_SESSION['guest_id'] = $guest_id;
        }

        $_SESSION['cart_total'] = $total_price;

        $insertAddress = $conn->prepare("INSERT INTO guest_addresses (address, city, postal_code, guest_id) VALUES (?, ?, ?, ?)");
        $insertAddress->bind_param("sssi", $address, $city, $postal_code, $guest_id);
        $insertAddress->execute();

        $insertNote = $conn->prepare("INSERT INTO guest_notes (meal_id, note, guest_id) VALUES (?, ?, ?)");
        foreach ($cartItems as $meal_id => $item) {
            if (!empty($item['note'])) {
                $insertNote->bind_param("isi", $meal_id, $item['note'], $guest_id);
                $insertNote->execute();
            }
        }

        $orderInsert = $conn->prepare("INSERT INTO guest_orders (guest_id, total_price, address, city, postal_code)
                                       VALUES (?, ?, ?, ?, ?)");
        $orderInsert->bind_param("idsss", $guest_id, $total_price, $address, $city, $postal_code);
        if (!$orderInsert->execute()) {
            die("Misafir siparişi eklenemedi: " . $orderInsert->error);
        }

        $order_id = $conn->insert_id;

        // 🔁 Burada da her yemek için ayrı status ekleniyor
        $orderItemInsert = $conn->prepare("INSERT INTO guest_order_items (guest_order_id, meal_id, quantity, price, status) VALUES (?, ?, ?, ?, 'Hazırlanıyor')");
        foreach ($cartItems as $meal_id => $item) {
            $orderItemInsert->bind_param("iiid", $order_id, $meal_id, $item['quantity'], $item['price']);
            $orderItemInsert->execute();
        }

        unset($_SESSION['cart']);
    } else {
        die("Sepetinizde ürün bulunmamaktadır.");
    }

    echo "<h2>Siparişiniz başarıyla oluşturuldu!</h2>";

} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>

<script src="submit_order.js"></script>
</body>
</html>
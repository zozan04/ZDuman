<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include('db_connection.php'); // Veritabanı bağlantısı

$user_id = $_SESSION['user_id'] ?? null; 

if (!isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = uniqid('guest_id', true);
}


// Kullanıcı bilgilerini çek
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// $user dizisi var mı ve 'name' anahtarı dolu mu kontrol et
$name = isset($user['name']) ? $user['name'] : '';

if (!empty($name)) {
    $full_name = explode(" ", $name, 2);
    $first_name = $full_name[0];
    $last_name = isset($full_name[1]) ? $full_name[1] : "";
} else {
    // Eğer isim boşsa ya da yoksa
    $first_name = "";
    $last_name = "";
}

$email = isset($user['email']) ? $user['email'] : "";



  // Adres kaydetme işlemi mi?
  if (isset($_POST['address'])) {
    $address = $_POST['address'];
    $user_id = $_SESSION['user_id'];

    if (!empty($address)) {
        $stmt = $conn->prepare("INSERT INTO addresses (user_id, address) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $address);

        if ($stmt->execute()) {
            echo "Adres başarıyla kaydedildi.";
        } else {
            echo "Adres kaydedilemedi. Hata: " . $stmt->error;
        }
    } else {
        echo "Adres boş bırakılamaz.";
    }
    exit;
}

// Kullanıcının adreslerini veritabanından al
$stmt = $conn->prepare("SELECT address FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$addresses = $result->fetch_all(MYSQLI_ASSOC);

// Adres silme işlemi
if (isset($_POST['delete_address'])) {
    $addressToDelete = $_POST['delete_address'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM addresses WHERE user_id = ? AND address = ?");
    $stmt->bind_param("is", $user_id, $addressToDelete);

    if ($stmt->execute()) {
        echo "Adres başarıyla silindi.";
    } else {
        echo "Adres silinemedi. Hata: " . $stmt->error;
    }
    exit;
}



// Adet azaltma
if (isset($_POST['action']) && $_POST['action'] == 'decrease') {
    $meal_id = $_POST['meal_id'];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Önce mevcut miktarı alıyoruz
        $check = "SELECT quantity FROM cart WHERE user_id = ? AND meal_id = ?";
        $stmt = $conn->prepare($check);
        $stmt->bind_param("ii", $user_id, $meal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();

        if ($item['quantity'] > 1) {
            // Miktarı azalt
            $update = "UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND meal_id = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("ii", $user_id, $meal_id);
            $stmt->execute();
            $stmt->close();
        
           } else if (isset($_SESSION['cart'][$meal_id])) {
            if ($_SESSION['cart'][$meal_id]['quantity'] > 1) {
                $_SESSION['cart'][$meal_id]['quantity'] -= 1;
            } else {
                unset($_SESSION['cart'][$meal_id]);
            }
        }
    }
    echo json_encode(["status" => "success", "message" => "Miktar azaltıldı."]);
    exit;
}

//silme işlemleri
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $meal_id = intval($_POST['meal_id']);

    // Eğer kullanıcı giriş yapmışsa veritabanından da sil
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Sepetten yemeği sil
        $delete_from_cart = "DELETE FROM cart WHERE user_id = ? AND meal_id = ?";
        $stmt = $conn->prepare($delete_from_cart);

        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $meal_id);
            $stmt->execute();
            $stmt->close();
        }

        // Yemeğe ait notu meal_notes tablosundan sil
        $delete_from_notes = "DELETE FROM meal_notes WHERE meal_id = ? AND user_id = ?";
        $stmt_notes = $conn->prepare($delete_from_notes);

        if ($stmt_notes) {
            $stmt_notes->bind_param("ii", $meal_id, $user_id);
            $stmt_notes->execute();
            $stmt_notes->close();
        }
    }

    // Session'dan da sil (giriş yapmış olsun ya da olmasın)
    if (isset($_SESSION['cart'][$meal_id])) {
        unset($_SESSION['cart'][$meal_id]);
    }

    echo json_encode(["status" => "success", "message" => "Ürün sepetten ve notlarınızdan silindi."]);
    exit;
}

 
//giriş yapan kullanıcılar için not ekleme veritabanına kaydetme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_note') {
    $meal_id = intval($_POST['meal_id']);
    $note = trim($_POST['note']);
         
    if (!empty($note) && isset($_SESSION['user_id'])) {
        // Notu meal_notes tablosuna ekliyoruz
        $query = "INSERT INTO meal_notes (meal_id, note, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $meal_id, $note, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "success", "message" => "Not başarıyla eklendi."]);
    
    } else if(!empty($note) && isset($_SESSION['guest_id'])) {
        $query = "INSERT INTO guest_notes (meal_id, note, guest_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $meal_id, $note, $_SESSION['guest_id']);
        $stmt->execute();
        $stmt->close();
        echo json_encode(["status" => "error", "message" => "Not eklenemedi."]);
    }

    exit;
}
if(!empty($note) && isset($_SESSION['guest_id'])) {
        $query = "INSERT INTO guest_notes (meal_id, note, guest_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $meal_id, $note, $_SESSION['guest_id']);
        $stmt->execute();
        $stmt->close();
        echo json_encode(["status" => "error", "message" => "Not eklenemedi."]);
    }
//giriş yapan kullanıcılar için not güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Not güncelleme işlemi
    if (isset($_POST['action']) && $_POST['action'] == 'update_note') {
        $meal_id = intval($_POST['meal_id']);
        $note = trim($_POST['note']);

        if (!empty($note)) {
            $user_id = $_SESSION['user_id'];
            $query = "UPDATE meal_notes SET note = ? WHERE meal_id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sii", $note, $meal_id, $user_id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(["status" => "success", "message" => "Not güncellendi."]);
        } else if(!empty($note) && isset($_SESSION['guest_id'])) {
            
            $query = "UPDATE guest_notes SET note = ? WHERE meal_id = ? AND guest_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sii", $note, $meal_id, $_SESSION['guest_id']);
            $stmt->execute();
            $stmt->close();
            echo json_encode(["status" => "success", "message" => "Not güncellendi."]);
            echo json_encode(["status" => "error", "message" => "Geçerli bir not girin."]);
                                
        }
        exit;
    }
}



//sepete ekle işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🍽️ Sepete ekleme işlemi mi?
    if (isset($_POST['meal_id'])) {

        // Oturum kontrolü
        if (!isset($_SESSION['user_id'])) {
            $meal_id = $_POST['meal_id'];
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$meal_id])) {
                $_SESSION['cart'][$meal_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$meal_id] = ['quantity' => 1];
            }

            echo json_encode(["status" => "success", "message" => "Yemek sepete eklendi."]);
            exit;
        }

        // Oturum varsa veritabanına ekle
        $user_id = $_SESSION['user_id'];
        $meal_id = $_POST['meal_id'];
        $quantity = 1;

        $check_cart = "SELECT quantity FROM cart WHERE user_id = ? AND meal_id = ?";
        $stmt = $conn->prepare($check_cart);
        $stmt->bind_param("ii", $user_id, $meal_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $update_cart = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND meal_id = ?";
            $stmt = $conn->prepare($update_cart);
            $stmt->bind_param("ii", $user_id, $meal_id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(["status" => "success", "message" => "Yemek sepete eklendi."]);
        } else {
            $insert_cart = "INSERT INTO cart (user_id, meal_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_cart);
            $stmt->bind_param("iii", $user_id, $meal_id, $quantity);
            $stmt->execute();
            $stmt->close();
            echo json_encode(["status" => "success", "message" => "Yemek sepete eklendi."]);
        }
        exit;
    }
}

// Sepetteki yemekleri çekme sorgusu
if (isset($_SESSION['user_id'])) {
    // Kullanıcı oturum açmışsa, veritabanından yemekleri çekiyoruz
    $user_id = $_SESSION['user_id'];
    $query = "SELECT c.quantity, m.id, m.name, m.price, m.image_path 
 
    FROM cart c 
    JOIN meals m ON c.meal_id = m.id 
    WHERE c.user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Sepetteki yemekleri listele
    $cart_items = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row; // Sepetteki yemekleri al
        }
    } else {
        $cart_items = []; // Eğer sepette yemek yoksa boş dizi
    }
    $stmt->close();
} else {
    // Kullanıcı oturum açmamışsa, sepeti $_SESSION'den alıyoruz
   $cart_items = [];

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $meal_id => $item) {
        // Veritabanından yemek bilgilerini çek
        $query = "SELECT name, price, image_path FROM meals WHERE id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $meal_id);
            $stmt->execute();
            $meal_result = $stmt->get_result();

            if ($meal_result && $meal_result->num_rows > 0) {
                $meal_info = $meal_result->fetch_assoc();

                // Sepet öğesine ekle
                $cart_items[] = [
                    'id' => $meal_id,
                    'name' => $meal_info['name'],
                    'quantity' => $item['quantity'],
                    'price' => $meal_info['price'],
                    'image_path' => $meal_info['image_path']
                ];
            }
            $stmt->close();
        }
    }}}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghibli Yemek Platformu</title>
    <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->
    <link rel="stylesheet" href="sepetim.css">
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
                        <a href="user_settings.php">Ayarlar</a> <!-- Yeni "Ayarlar" menüsü -->
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
    <div class="cart-and-order-container">
    <!-- Sepet Bölümü -->
    <div class="cart-section">
        <div id="cart-message" class="empty-cart-message">
            <?php if (empty($cart_items)): ?>
                <p>Sepetinizde yemek yok.</p>
                <a href="index.html#yemekler" class="add-dish-button">Yemek Eklemeye Başla</a>
            <?php else: ?>
                <p>Sepetinizdeki Yemekler:</p>
                <div id="cart-items" class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                       <div class="cart-item" data-meal-id="<?= isset($item['id']) ? htmlspecialchars($item['id']) : '' ?>">
    <img src="<?= isset($item['image_path']) ? htmlspecialchars($item['image_path']) : 'default.jpg' ?>" alt="<?= isset($item['name']) ? htmlspecialchars($item['name']) : 'Yemek' ?>" class="cart-item-image">
    <div class="cart-item-details">
        <p class="cart-item-name"><?= isset($item['name']) ? htmlspecialchars($item['name']) : 'İsimsiz Yemek' ?></p>
        <p class="cart-item-price"><?= isset($item['price']) ? number_format($item['price'], 2, ',', '.') . ' ₺' : 'Fiyat yok' ?></p>
        <div class="quantity-controls">
            <button class="decrease-btn">-</button>
            <span class="cart-item-quantity"><?= isset($item['quantity']) ? (int)$item['quantity'] : 0 ?></span>
            <button class="increase-btn">+</button>
            <button class="delete-btn">🗑️</button>
        </div>
    </div>
    <!-- Yemek Notu Bölümü -->
    <div class="meal-note-section">
        <textarea class="meal-note" placeholder="Yemeğiniz için özel bir not ekleyin..."></textarea>
        <button class="add-note-btn">Not Ekle</button>
        <button class="update-note-btn">Güncelle</button>
        <p class="note-success-message" style="display: none; color:#511212;">İsteklerinizi güncelledik!</p>
    </div>
</div>

                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

   <!-- Sipariş Özeti Bölümü -->
<div class="order-summary">
    <h2>Sipariş Özeti</h2>
    <div id="order-items">
        <!-- Sipariş detayları dinamik eklenebilir istersen -->
    </div>
    <p>Toplam: <span id="total-price">0₺</span></p>
    <p>Kargo: <span id="shipping-cost">2₺</span></p> <!-- Kargo ücreti -->
    <button class="complete-order-btn">Siparişi Tamamla</button>

    <!-- Adres ve Ödeme Bilgileri Bölümü (Başlangıçta gizli) -->
    <div id="address-payment-info" style="display:none; margin-top: 20px;">

        <form action="submit_order.php" method="POST" id="complete-order-form">
            <!-- Kişisel Bilgiler -->
            <h3>Kişisel Bilgiler</h3>
            
<div style="display: flex; gap: 10px;">
    <div style="flex: 1;">
        <label for="first_name">Ad:</label>
        <input type="text" name="first_name" id="first_name" placeholder="Adınız" required
               value="<?= htmlspecialchars($first_name) ?>">
    </div>
    <div style="flex: 1;">
        <label for="last_name">Soyad:</label>
        <input type="text" name="last_name" id="last_name" placeholder="Soyadınız" required
               value="<?= htmlspecialchars($last_name) ?>">
    </div>
</div>

<div style="display: flex; gap: 10px; margin-top: 10px;">
    <div style="flex: 1;">
        <label for="phone">Telefon:</label>
        <input type="tel" name="phone" id="phone" placeholder="05XXXXXXXXX" required pattern="05\d{9}">
    </div>
    <div style="flex: 1;">
        <label for="email">E-posta:</label>
        <input type="email" name="email" id="email" placeholder="ornek@mail.com" required
               value="<?= htmlspecialchars($email) ?>">
    </div>
</div>


            <h3>Adres Bilgileri</h3>
            <label for="saved-address">Kayıtlı Adresler:</label>
            <select name="saved_address" id="saved-address">
                <option value="">-- Adres seçin veya yeni ekleyin --</option>
                <?php foreach ($addresses as $address): ?>
                    <option value="<?= htmlspecialchars($address['address']) ?>">
                        <?= htmlspecialchars($address['address']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="address">Yeni Adres:</label>
            <textarea name="address" id="address" placeholder="Adresinizi girin" required></textarea>

            <!-- Butonlar -->
            <div id="address-buttons">
                <button type="button" id="add-address-btn">+ Yeni Adresi Kaydet</button>
                <button type="button" id="delete-address-btn">Adresi Sil</button>
            </div>

            <div id="success-message" style="display:none; color: green; margin-top: 10px;"></div>

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label for="city">Şehir:</label>
                    <input type="text" name="city" id="city" placeholder="Şehir" required>
                </div>
                <div style="flex: 1;">
                    <label for="postal-code">Posta Kodu:</label>
                    <input type="text" name="postal_code" id="postal-code" placeholder="Posta kodu" required>
                </div>
            </div>

            <h3>Ödeme Bilgileri</h3>
            <label for="credit-card-number">Kredi Kartı Numarası:</label>
            <input type="text" name="card_number" id="credit-card-number" placeholder="XXXX XXXX XXXX XXXX" required maxlength="19">

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label for="expiry-date">Son Kullanma Tarihi:</label>
                    <input type="date" name="expiry_date" id="expiry-date" required>
                </div>
                <div style="flex: 1;">
                    <label for="cvv">CVV:</label>
                    <input type="text" name="cvv" id="cvv" placeholder="XXX" required>
                </div>
            </div>

            <button class="complete-payment" type="submit">Siparişi Ver</button>
        </form>
    </div>
</div>



    <script src="sepetim.js"></script>
    
 
</body>
</html>
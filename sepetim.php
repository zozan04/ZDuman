<?php
session_start();
include('db_connection.php'); // Veritabanı bağlantısı

// Sepete eklemek için
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Oturum kontrolü
    if (!isset($_SESSION['user_id'])) {
        // Eğer kullanıcı oturum açmamışsa, veriyi $_SESSION'de tutuyoruz
        $meal_id = $_POST['meal_id'];
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // Sepet yoksa başlatıyoruz
        }

        // Sepete yemek ekliyoruz
        if (isset($_SESSION['cart'][$meal_id])) {
            $_SESSION['cart'][$meal_id]['quantity'] += 1; // Eğer yemek varsa miktarı artırıyoruz
        } else {
            $_SESSION['cart'][$meal_id] = ['quantity' => 1]; // Yeni yemek ekliyoruz
        }

        echo json_encode(["status" => "success", "message" => "Yemek sepete eklendi."]);
        exit;
    }

    // Kullanıcı oturum açmışsa, veriyi veritabanına ekliyoruz
    $user_id = $_SESSION['user_id']; // Kullanıcının oturumdaki ID'si
    $meal_id = $_POST['meal_id'];
    $quantity = 1; // Varsayılan olarak 1 adet eklenir

    // Sepette aynı yemek var mı kontrol et
    $check_cart = "SELECT quantity FROM cart WHERE user_id = ? AND meal_id = ?";
    $stmt = $conn->prepare($check_cart);
    $stmt->bind_param("ii", $user_id, $meal_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Eğer yemek sepette varsa, miktarı artır
        $update_cart = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND meal_id = ?";
        $stmt = $conn->prepare($update_cart);
        $stmt->bind_param("ii", $user_id, $meal_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(["status" => "success", "message" => "Yemek sepete eklendi."]);
    } else {
        // Eğer yemek sepette yoksa, yeni kayıt ekle
        $insert_cart = "INSERT INTO cart (user_id, meal_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_cart);
        $stmt->bind_param("iii", $user_id, $meal_id, $quantity);
        $stmt->execute();
        $stmt->close();
        echo json_encode(["status" => "success", "message" => "Yemek sepete eklendi."]);
    }
    exit;
}

// Sepetteki yemekleri çekme sorgusu
if (isset($_SESSION['user_id'])) {
    // Kullanıcı oturum açmışsa, veritabanından yemekleri çekiyoruz
    $user_id = $_SESSION['user_id'];
    $query = "SELECT c.quantity, m.name, m.price, m.image_path 
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
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $meal_id => $item) {
            // Veritabanından yemek bilgilerini çekmek
            $query = "SELECT name, price, image_path FROM meals WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $meal_id);
            $stmt->execute();
            $meal_result = $stmt->get_result();

            if ($meal_result->num_rows > 0) {
                $meal_info = $meal_result->fetch_assoc();
                $cart_items[] = [
                    'name' => $meal_info['name'], // Yemek adı
                    'quantity' => $item['quantity'],
                    'price' => $meal_info['price'], // Yemek fiyatı
                    'image_path' => $meal_info['image_path'] // Yemek resmi
                ];
            }
            $stmt->close();
        }
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
                        <a href="login.html">Giriş Yap</a>
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
   <!-- Sepetim Sayfası -->
   <div class="cart-section">
        <div id="cart-message" class="empty-cart-message">
            <?php if (empty($cart_items)): ?>
                <p>Sepetinizde yemek yok.</p>
                <a href="index.html#yemekler" class="add-dish-button">Yemek Eklemeye Başla</a>
            <?php else: ?>
                <p>Sepetinizdeki Yemekler:</p>
                <div id="cart-items" class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="<?= $item['image_path'] ?>" alt="<?= $item['name'] ?>" class="cart-item-image">
                            <div class="cart-item-details">
                                <p class="cart-item-name"><?= $item['name'] ?></p>
                                <p class="cart-item-price"><?= number_format($item['price'], 2, ',', '.') ?> ₺</p>
                                <p class="cart-item-quantity">Adet: <?= $item['quantity'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

   
    <script src="sepetim.js"></script>
    <script>
        // Sayfa yüklendiğinde URL'den yemeği kontrol et ve yalnızca o yemeği göster
        window.onload = function() {
            const hash = window.location.hash.substring(1); // URL'deki hash kısmını al
            if (hash) {
                const items = document.querySelectorAll('.dish-item');
                items.forEach(item => {
                    if (item.id !== hash) {
                        item.style.display = 'none'; // Diğer yemekleri gizle
                    }
                });
            }
        };

        document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", function () {
        let mealId = this.getAttribute("data-dish-id");

        fetch("sepetim.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "meal_id=" + mealId
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Kullanıcıya geri bildirim ver
        })
        .catch(error => console.error("Hata:", error));
    });
});

    </script>
</body>
</html>
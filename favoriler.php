<?php
include('db_connection.php');
session_start();

// Kullanıcı oturum açmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['user_id'];

// Favorilere ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mealId'])) {
    $mealId = $_POST['mealId'];

    // Favori yemek kontrolü (kullanıcı daha önce bu yemeği favorilemiş mi?)
    $checkFavorite = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND meal_id = ?");
    $checkFavorite->bind_param("ii", $userId, $mealId);
    $checkFavorite->execute();
    $result = $checkFavorite->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Bu yemek zaten favorilerinizde."]);
    } else {
        // Favori yemek ekle
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, meal_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $mealId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Yemek favorilere eklendi."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Favorilere eklerken bir hata oluştu."]);
        }

        $stmt->close();
    }

    $conn->close();
    exit();
}

// Silme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteMealId'])) {
    $mealId = $_POST['deleteMealId'];

    // Favoriden yemeği sil
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND meal_id = ?");
    $stmt->bind_param("ii", $userId, $mealId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Yemek favorilerden silindi."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Yemek silinirken bir hata oluştu."]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Favori yemekleri getir
$sql = "SELECT meals.*, students.name AS username 
        FROM meals 
        INNER JOIN favorites ON meals.id = favorites.meal_id 
        INNER JOIN students ON meals.user_id = students.id
        WHERE favorites.user_id = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$favoriteMeals = [];
while ($row = $result->fetch_assoc()) {
    $favoriteMeals[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghibli Yemek Platformu</title>
    <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->
    <link rel="stylesheet" href="favoriler.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/scrollreveal"></script>
    <title>Favori Yemekler</title>
    <link rel="stylesheet" href="favoriler.css">
    <style>
       
    </style>
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
<div class="main-dish-section">
<h2>Favori Yemekler</h2>
<div class="favorite-dishes">
    <?php
    if (empty($favoriteMeals)) {
        echo "<p>Henüz favori yemek eklenmedi.</p>";
    } else {
        foreach ($favoriteMeals as $meal) {
            echo "<div class='dish-item'>";
            echo "<img src='" . $meal['image_path'] . "' alt='" . $meal['name'] . "' />";
            echo "<div class='dish-user'>";
            echo "<a href='profile.php?id=" . $meal['user_id'] . "' class='username'>" . $meal['username'] . "</a>"; // Kullanıcı adı bağlantı
            echo "</div>";
            echo "<p class='dish-title'>" . $meal['name'] . "</p>"; // Yemek 
            echo "<div class='dish-footer'>";
            echo "<div class='dish-price'>₺" . number_format($meal['price'], 2) . "</div>";
            echo "<div class='separator'></div>"; // Dikey çizgi

            echo "<button class='add-to-cart' data-dish-id='" . $meal['id'] . "'>Sepete Ekle</button>";
            echo "</div>";
            // Silme ikonu
            echo "<div class='delete-icon' onclick='deleteMeal(" . $meal['id'] . ")'>X</div>";
            echo "</div>";
        }
    }
    ?>
</div>
</div>

<script src="favoriler.js"></script>
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
    </script>

</body>
</html>

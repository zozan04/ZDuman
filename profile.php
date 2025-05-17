<?php
include('db_connection.php');

// Öğrencinin ID'sini al
$student_id = $_GET['id'];

// Öğrencinin bilgilerini al
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghibli Yemek Platformu</title>
    <link rel="icon" href="resim/A7.jpg" type="image/png">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/scrollreveal"></script>
</head>
<body>
    <header class="main-header">
        <nav>
            <div class="nav_bar">
                <div class="logo"><img src="resim/A7.jpg" alt="GYP Logo"></div>
                <ul class="menu">
                    <li><a href="index.html">Anasayfa</a></li>
                    <li><a href="#hakkimizda">Hakkımızda</a></li>
                    <li><a href="#yemekler">Yemekler</a></li>
                    <li><a href="#iletisim">İletişim</a></li>
                </ul>
            </div>
        </nav>
        <div class="icons-container">
            <div class="search-icon">
                <div class="circle" onclick="toggleSearchInput()">
                    <i class="fas fa-search search-icon" style="color: black; font-size: 20px;"></i>
                </div>
                <div id="search-box" style="display: none;">
                    <input type="text" id="searchInput" placeholder="Yemek arayın...">
                </div>
            </div>
            <div class="filter-icon">
                <div class="circle" onclick="toggleFilterInput()">
                    <i class="fas fa-filter"></i>
                </div>
                <span class="filter-text">Filtrele</span>
                <div id="filter-box">
                    <input type="text" id="filterInput" oninput="filterDishes()" placeholder="Yemek adını girin">
                </div>
            </div>
            <a href="sepetim.php" class="cart-icon" title="Sepetim">
                <div class="circle"><i class="fas fa-shopping-cart" style="color: black; font-size: 20px;"></i></div>
            </a>
            <div class="login-container">
                <div class="circle login-icon" onclick="toggleLogoutMenu()">
                    <i class="fas fa-user" style="color: black; font-size: 20px;"></i>
                </div>
                <div id="logout-menu" class="logout-menu"><a href="kullanici_cikis.php">Çıkış Yap</a></div>
            </div>
            <div class="light" id="light" title="Tema Değiştir">
                <div class="circle"><i class="fas fa-sun" id="themeIcon" style="color: black; font-size: 20px;"></i></div>
            </div>
            <a href="favoriler.php" class="favorites-icon" title="Favorilerim">
                <div class="circle"><i class="fas fa-heart" style="color: black; font-size: 20px;"></i></div>
            </a>
        </div>
    </header>

    <main>
    <?php
    if ($student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc();
        echo "<h2>" . $student['name'] . "</h2>";
    }

    $sql_meals = "SELECT * FROM meals WHERE user_id = ? ORDER BY name";
    $stmt_meals = $conn->prepare($sql_meals);
    $stmt_meals->bind_param("i", $student_id);
    $stmt_meals->execute();
    $meals_result = $stmt_meals->get_result();

    if ($meals_result->num_rows > 0) {
        echo "<div class='dish-gallery'>";
        while ($meal = $meals_result->fetch_assoc()) {
            echo "<div class='dish-item'>";
            // Görsele tıklama ile modal açma
            echo "<img class='dish-img' src='" . $meal['image_path'] . "' alt='" . $meal['name'] . "' 
                onclick='openModal({$meal['id']}, \"{$meal['image_path']}\", \"{$meal['name']}\", \"{$meal['content']}\", {$meal['price']})' />";
            echo "<div class='favorite-icon' onclick='addToFavorites(" . $meal['id'] . ")'>
                <i class='fas fa-heart'></i>
                     </div>";
            echo "<p class='dish-title'>" . $meal['name'] . "</p>";

                // Şehir bilgisi buraya eklendi
        echo "<p class='dish-city'>" . $meal['city'] . "</p>";
        
            echo "<div class='dish-footer'>";
            echo "<div class='dish-price'>₺" . number_format($meal['price'], 2) . "</div>";
            echo "<div class='separator'></div>";
            // Sepete ekle butonuna ID ve fiyat ekleniyor
            echo "<button class='add-to-cart' data-dish-id='" . $meal['id'] . "' data-dish-name='" . $meal['name'] . "'data-dish-price='₺" . number_format($meal['price'], 2) . "'>Sepete Ekle</button>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>Bu öğrenci henüz yemek eklemedi.</p>";
    }
?>

</main>

    <!-- Modal -->
    <div id="myModal" class="modal">
    <div class="modal-content">
        <div class="modal-left">
            <img id="modal-img" src="" alt="Yemek Görseli" />
        </div>
        <div class="modal-right">
            <h1 id="modal-title">Yemek Adı</h1>
            <p id="modal-content-text"><strong>İçindekiler:</strong> <span id="modal-content"></span></p>
            <p id="modal-price">₺<span>0.00</span></p>
          <button class="add-to-carts" id="add-to-cart-modal">Sepete Ekle</button>

            <!-- Modal içindeki Bildirim Kutusu -->
            <div id="notification" class="cart-notification" style="display: none;">Sepete eklendi</div>
        </div>
        
        <span class="close" onclick="closeModal()">&times;</span>
    </div>
</div>

    <script src="profile.js"></script>
    <script>
       
    </script>
</body>
</html>

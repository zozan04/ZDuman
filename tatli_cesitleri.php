<?php
include('db_connection.php');

// Sulu Yemekler kategorisindeki yemekleri sorgulamak
$sql = "SELECT meals.*, students.name AS username 
        FROM meals 
        JOIN students ON meals.user_id = students.id
        WHERE meals.category = 'Tatlı Çeşitleri' 
        ORDER BY meals.name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghibli Yemek Platformu</title>
    <link rel="icon" href="resim/A7.jpg" type="image/png"> <!-- PNG formatında favicon -->
    <link rel="stylesheet" href="ana_yemekler.css">
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
            
             <!-- Filtreleme İkonu -->
             <div class="filter-icon">
                <div class="circle" onclick="toggleFilterInput()">
                    <i class="fas fa-filter"></i>
                </div>
                <span class="filter-text">Filtrele</span>
                <div id="filter-box"> <!-- Filtre kutusu -->
                    <input type="text" id="filterInput" oninput="filterDishes()" placeholder="Yemek adını girin">
                </div>
            </div>
            
   
            <!-- Sepetim İkonu -->
            <a href="sepetim.php" class="cart-icon" title="Sepetim">
                <div class="circle">
                    <i class="fas fa-shopping-cart" style="color: black; font-size: 20px;"></i>
                </div>
            </a>
            <!-- Giriş İkonu ve çıkış ikonu -->
            <div class="login-containers">
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
            <!-- Favoriler İkonu -->
        <a href="favoriler.php" class="favorites-icon" title="Favorilerim">
            <div class="circle">
                <i class="fas fa-heart" style="color: black; font-size: 20px;"></i>
            </div>
        </a>
        </div>

    </header>
   

    <!-- Yemek bölümü -->
    <div class="main-dish-section">
        <h2>Tatlı Çeşitleri</h2>
        <div class="dish-gallery">
            <?php
            // Yemekleri veritabanından getir
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='dish-item'>";
                    echo "<img src='" . $row['image_path'] . "' alt='" . $row['name'] . "' />";
                      // Favori ikonunu buraya ekledim
                      echo "<div class='favorite-icon' onclick='addToFavorites(" . $row['id'] . ")'>";
                      echo "<i class='fas fa-heart'></i>"; // Favori ikonu
                      echo "</div>";
                     
                      echo "<div class='dish-user'>";
                      echo "<a href='profile.php?id=" . $row['user_id'] . "' class='username'>" . $row['username'] . "</a>"; // Kullanıcı adı bağlantı
                      echo "</div>";
                     
                     
                      echo "<p class='dish-title'>" . $row['name'] . "</p>"; // Yemek adı

                    echo "<div class='dish-footer'>";
                    echo "<div class='dish-price'>₺" . number_format($row['price'], 2) . "</div>";
                    echo "<div class='separator'></div>"; // Dikey çizgi
                    echo "<button class='add-to-cart' data-dish-id='" . $row['id'] . "' data-dish-name='" . $row['name'] . "' data-dish-price='₺" . number_format($row['price'], 2) . "'>Sepete Ekle</button>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Bu kategoride yemek bulunmamaktadır.</p>";
            }
            // Bağlantıyı kapat
            $conn->close();
            ?>
        </div>
    </div>

    <script src="ana_yemekler.js"></script>
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
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
        <!-- Yemek adıyla filtreleme -->
        <input type="text" id="filterInput" oninput="filterDishes()" placeholder="Yemek adını girin">

        <!-- Özel şehir filtresi -->
        <div class="custom-dropdown">
            <div class="selected" onclick="toggleDropdown()">Tüm Şehirler</div>
            <ul class="dropdown-options" id="cityList">
                <li onclick="selectCity('', this)">Tüm Şehirler</li>
                <?php
                $city_query = "SELECT DISTINCT city FROM meals ORDER BY city ASC";
                $city_result = $conn->query($city_query);
                while ($row = $city_result->fetch_assoc()) {
                    $city = trim(htmlspecialchars($row['city']));
                    echo "<li onclick=\"selectCity('$city', this)\">$city</li>";
                }
                ?>
            </ul>
        </div>

        <!-- Gizli input -->
        <input type="hidden" id="selectedCity" value="">
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
                    <a href="user_settings.php">Ayarlar</a> <!-- Yeni "Ayarlar" menüsü -->
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
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='dish-item' data-city='" . htmlspecialchars($row['city']) . "'>";
                
                echo "<img src='" . $row['image_path'] . "' 
                        alt='" . $row['name'] . "' 
                        onclick='openModal(" . $row['id'] . ", \"" . $row['name'] . "\", \"" . $row['image_path'] . "\", `" . $row['content'] . "`, \"" . number_format($row['price'], 2) . "\")'>";

                echo "<div class='favorite-icon' onclick='addToFavorites(" . $row['id'] . ")'>
                        <i class='fas fa-heart'></i>
                      </div>";

                echo "<div class='dish-user'>
                        <a href='profile.php?id=" . $row['user_id'] . "' class='username'>" . $row['username'] . "</a>
                        <p class='dish-city'>" . $row['city'] . "</p>
                      </div>";

                echo "<p class='dish-title'>" . $row['name'] . "</p>";
                 echo "<a href='#' class='open-evaluations' data-meal-id='" . $row['id'] . "' style='display:inline-block; margin-bottom:10px;'>Değerlendirmeler</a>";

                echo "<div class='dish-footer'>
                        <div class='dish-price'>₺" . number_format($row['price'], 2) . "</div>
                        <div class='separator'></div>
                        <button class='add-to-cart'
                                data-dish-id='" . $row['id'] . "'
                                data-dish-name='" . $row['name'] . "'
                                data-dish-price='₺" . number_format($row['price'], 2) . "'>
                                Sepete Ekle
                        </button>
                      </div>";

                echo "</div>"; // .dish-item
            }
        } else {
            echo "<p>Bu kategoride yemek bulunmamaktadır.</p>";
        }

        $conn->close();
        ?>
    </div>
</div>
       <!-- içerik Modal -->
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

<!-- değerlendirme Modal -->
<div id="evaluationsModal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); 
     width: 400px; max-height: 60vh; background: white; border:1px solid #ccc; box-shadow: 0 0 10px rgba(0,0,0,0.3); 
     overflow-y: auto; padding: 20px; z-index: 1000;">
    <button id="closeModal" style="float:right;">Kapat</button>
    <h4>Yemek Değerlendirmeleri</h4>
    <div id="evaluationsContent" style="margin-top: 30px;">
        <!-- Değerlendirmeler buraya yüklenecek -->
    </div>
</div>

<!-- Modal arka plan -->
<div id="modalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
     background:rgba(0,0,0,0.5); z-index: 999;"></div>

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
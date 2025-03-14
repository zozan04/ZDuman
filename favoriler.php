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
$sql = "SELECT meals.* FROM meals 
        INNER JOIN favorites ON meals.id = favorites.meal_id 
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
    <title>Favori Yemekler</title>
    <link rel="stylesheet" href="favoriler.css">
    <style>
        /* Resmin sağ üst köşesine silme ikonu eklemek için */
        .dish-item {
            position: relative;
            display: inline-block;
            margin: 10px;
        }

        .delete-icon {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
            background-color: rgba(255, 0, 0, 0.7);
            color: white;
            padding: 5px;
            border-radius: 50%;
        }

        .delete-icon:hover {
            background-color: red;
        }
    </style>
</head>
<body>

<h2>Favori Yemekler</h2>
<div id="favorite-dishes">
    <?php
    if (empty($favoriteMeals)) {
        echo "<p>Henüz favori yemek eklenmedi.</p>";
    } else {
        foreach ($favoriteMeals as $meal) {
            echo "<div class='dish-item'>";
            echo "<img src='" . $meal['image_path'] . "' alt='" . $meal['name'] . "' />";
            echo "<div class='dish-title'>" . $meal['name'] . "</div>";
            echo "<div class='dish-footer'>";
            echo "<div class='dish-price'>₺" . number_format($meal['price'], 2) . "</div>";
            echo "<button class='add-to-cart' data-dish-id='" . $meal['id'] . "'>Sepete Ekle</button>";
            echo "</div>";
            // Silme ikonu
            echo "<div class='delete-icon' onclick='deleteMeal(" . $meal['id'] . ")'>X</div>";
            echo "</div>";
        }
    }
    ?>
</div>
<script src="favoriler.js"></script>


</body>
</html>

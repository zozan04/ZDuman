<?php
session_start();
include('db_connection.php'); // Veritabanı bağlantısı

$user_id = $_SESSION['user_id']; // Bu satır, oturum yönetiminize bağlı olarak değişebilir

if (!isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = uniqid('guest_id', true);
}

// Adet artırma
if (isset($_POST['action']) && $_POST['action'] == 'increase') {
    $meal_id = $_POST['meal_id'];
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $update = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND meal_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ii", $user_id, $meal_id);
        $stmt->execute();
        $stmt->close();
    } else {
        if (isset($_SESSION['cart'][$meal_id])) {
            $_SESSION['cart'][$meal_id]['quantity'] += 1;
            
        }
        
    }
    echo json_encode(["status" => "success", "message" => "Miktar artırıldı."]);
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
?>
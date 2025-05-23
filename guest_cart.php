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
if (isset($_POST['action']) && $_POST['action'] == 'get_total') {
    $total = 0;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT quantity, price FROM cart INNER JOIN meals ON cart.meal_id = meals.id WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $total += $row['quantity'] * $row['price'];
        }

        $stmt->close();
    } elseif (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['quantity'] * $item['price'];
        }
    }

    $shipping = 2;
    $grand_total = $total + $shipping;

    echo json_encode([
        "status" => "success",
        "subtotal" => $total,
        "shipping" => $shipping,
        "total" => $grand_total
    ]);
    exit;
}

?>
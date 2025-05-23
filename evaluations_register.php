<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    die("Giriş yapmadan yorum yapamazsınız.");
}

$user_id = $_SESSION['user_id'];
$meal_id = $_POST['meal_id'];
$comment = mysqli_real_escape_string($conn, $_POST['comment']);

// Teslim edilen sipariş kontrolü ve order_id çekme
$order_id_query = "
    SELECT oi.order_id FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.user_id = $user_id
      AND oi.meal_id = $meal_id
      AND oi.status = 'Teslim Edildi'
    LIMIT 1
";
$order_id_result = mysqli_query($conn, $order_id_query);

if (mysqli_num_rows($order_id_result) > 0) {
    $order_id_row = mysqli_fetch_assoc($order_id_result);
    $order_id = $order_id_row['order_id'];

    $insert = "
        INSERT INTO evaluations (user_id, meal_id, order_id, comment)
        VALUES ($user_id, $meal_id, $order_id, '$comment')
    ";
    if (mysqli_query($conn, $insert)) {
        echo "Yorumunuz kaydedildi. Teşekkür ederiz!";
    } else {
        echo "Hata oluştu: " . mysqli_error($conn);
    }
} else {
    echo "Yalnızca teslim aldığınız yemeklere yorum yapabilirsiniz.";
}
?>


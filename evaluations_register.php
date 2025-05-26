<?php

var_dump($_POST);
exit;

session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    die("Giriş yapmadan yorum yapamazsınız.");
}

$user_id = $_SESSION['user_id'];
$order_id = (int)$_POST['order_id'];
$meal_id = (int)$_POST['meal_id'];

var_dump($user_id, $meal_id, $order_id);  // Değerleri kontrol etmek için buraya ekle
$comment = mysqli_real_escape_string($conn, $_POST['comment']);



// 1. Daha önce yorum yapılmış mı kontrol et
$check_comment_query = "
    SELECT comment FROM evaluations 
    WHERE user_id = $user_id 
     AND meal_id = $meal_id
    AND order_id = $order_id
    LIMIT 1
";
$comment_result = mysqli_query($conn, $check_comment_query);

if (mysqli_num_rows($comment_result) > 0) {
    echo "Bu sipariş için zaten yorum yaptınız.";
} else {
    // 2. Sipariş gerçekten teslim edilmiş mi kontrol et
    $order_check_query = "
        SELECT oi.order_id FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE o.user_id = $user_id
          AND oi.meal_id = $meal_id
          AND oi.order_id = $order_id
          AND oi.status = 'Teslim Edildi'
        LIMIT 1
    ";
    $order_check_result = mysqli_query($conn, $order_check_query);

    if (mysqli_num_rows($order_check_result) > 0) {
        // 3. Yorum ekle
        $insert = "INSERT INTO evaluations (user_id, meal_id, order_id, comment, created_at) 
            VALUES ($user_id, $meal_id, $order_id, '$comment', NOW())";
        if (mysqli_query($conn, $insert)) {
            echo "Yorumunuz kaydedildi. Teşekkür ederiz!";
        } else {
            echo "Hata oluştu: " . mysqli_error($conn);
        }
    } else {
        echo "Yalnızca teslim aldığınız yemeklere yorum yapabilirsiniz.";
    }
}
?>

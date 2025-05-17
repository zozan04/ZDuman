<?php
session_start();
include('db_connection.php');
header('Content-Type: application/json');

// Sadece POST isteklerine cevap ver
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Öğrenci ID'si session'dan alınmalı (güvenlik için formdan alınmaz)
    if (!isset($_SESSION['student_id'])) {
        echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı.']);
        exit;
    }

    $student_id = $_SESSION['student_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Zorunlu alan kontrolü
    if (!$name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Ad ve e-posta zorunludur.']);
        exit;
    }

    // Şifre boşsa, veritabanındaki mevcut şifreyi koru
    if (empty($password)) {
        $stmt = $conn->prepare("SELECT password FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $password = $row['password'];
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Güncelleme sorgusu
    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $password, $student_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Öğrenci bilgileri başarıyla güncellendi.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Hata: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>

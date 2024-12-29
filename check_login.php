<?php
session_start();
header('Content-Type: application/json');

// POST verilerini al
$data = json_decode(file_get_contents('php://input'), true);

// Basit bir kullanıcı kontrolü (gerçek uygulamada veritabanından kontrol edilmeli)
$valid_users = [
    [
        'email' => 'admin@admin.com',
        'password' => '123456'
    ]
];

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$is_valid = false;
foreach ($valid_users as $user) {
    if ($user['email'] === $email && $user['password'] === $password) {
        $is_valid = true;
        break;
    }
}

if ($is_valid) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_email'] = $email;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
} 
<?php
// FILE: includes/db.php

// 1. Thử lấy cấu hình từ biến môi trường (Dành cho GitHub Actions)
$env_host = getenv('DB_HOST');
$env_user = getenv('DB_USER');
$env_pass = getenv('DB_PASS');
$env_name = getenv('DB_NAME');

// 2. Logic tự động chuyển đổi:
// - Nếu có biến môi trường (GitHub): Dùng biến đó.
// - Nếu KHÔNG (Máy bạn): Dùng mặc định (localhost, root, rỗng).
$servername = $env_host ? $env_host : "localhost";
$username   = $env_user ? $env_user : "root";
$password   = ($env_pass !== false) ? $env_pass : ""; // Quan trọng: XAMPP dùng rỗng
$dbname     = $env_name ? $env_name : "dbphonestore";

// 3. Kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// 4. Kiểm tra lỗi
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
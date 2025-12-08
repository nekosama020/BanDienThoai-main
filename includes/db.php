<?php
// FILE: includes/db.php

// 1. Cố gắng lấy cấu hình từ Biến môi trường (Dành cho CI/CD GitHub)
$env_host = getenv('DB_HOST');
$env_user = getenv('DB_USER');
$env_pass = getenv('DB_PASS');
$env_name = getenv('DB_NAME');

// 2. Nếu có biến môi trường (đang chạy trên GitHub) thì dùng nó
// Nếu không có (đang chạy trên máy bạn/XAMPP) thì dùng mặc định
$servername = $env_host ? $env_host : "localhost";
$username   = $env_user ? $env_user : "root";
$password   = $env_pass !== false ? $env_pass : ""; // XAMPP mặc định pass rỗng
$dbname     = $env_name ? $env_name : "dbphonestore";

// 3. Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// 4. Kiểm tra lỗi
if ($conn->connect_error) {
    // In lỗi ra để debug nếu chạy trên CI bị lỗi 500
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập font chữ tiếng Việt
$conn->set_charset("utf8");
?>
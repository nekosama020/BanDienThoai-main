<?php
// includes/db.php

// 1. Thử lấy cấu hình từ biến môi trường (Dành cho CI/CD GitHub)
$env_host = getenv('DB_HOST');
$env_user = getenv('DB_USER');
$env_pass = getenv('DB_PASS');
$env_name = getenv('DB_NAME');

// 2. Logic chọn cấu hình:
// Nếu có biến môi trường (đang chạy trên GitHub) -> Dùng biến môi trường
// Nếu không (đang chạy local/XAMPP) -> Dùng cấu hình mặc định của XAMPP
$servername = $env_host ? $env_host : "localhost";
$username   = $env_user ? $env_user : "root";
// Lưu ý: GitHub pass là 'root', XAMPP pass là rỗng ""
$password   = ($env_pass !== false) ? $env_pass : ""; 
$dbname     = $env_name ? $env_name : "dbphonestore";

// 3. Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// 4. Kiểm tra lỗi
if ($conn->connect_error) {
    // In lỗi ra để debug trên CI nếu cần thiết
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập font chữ tiếng Việt
$conn->set_charset("utf8");
?>
<?php
// Ưu tiên lấy cấu hình từ biến môi trường (Cho GitHub Actions)
// Nếu không có thì dùng mặc định (Cho XAMPP local)
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: ""; // XAMPP mặc định không có pass
$dbname = getenv('DB_NAME') ?: "dbphonestore"; // Tên database của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// echo "Kết nối thành công!";
?>

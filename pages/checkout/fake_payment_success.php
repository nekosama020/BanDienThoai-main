<?php
// --- BẬT HIỂN THỊ LỖI ĐỂ DEBUG (Giữ nguyên để theo dõi) ---
mysqli_report(MYSQLI_REPORT_OFF);
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ----------------------------------------------------------

session_start();

// Kết nối DB (Giữ nguyên)
if (file_exists(__DIR__ . '/../../includes/db.php')) {
    include __DIR__ . '/../../includes/db.php';
} else {
    die("Lỗi: Không tìm thấy file kết nối database!");
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id || !isset($_SESSION['checkout_info'])) {
    header("Location: ../../index.php");
    exit();
}

$cart_items = $_SESSION['checkout_info']['cart_items'] ?? [];
$amount = $_SESSION['checkout_info']['total_price'] ?? 0;
$method = $_SESSION['checkout_info']['payment_method'] ?? 'Unknown';

if (!empty($cart_items) && $amount > 0) {
    // --- [SỬA Ở ĐÂY] ---
    // Thay 'Paid' thành 'Completed' để khớp với Database
    $order_sql = "INSERT INTO orders (user_id, order_date, total_price, status, payment_method) 
                  VALUES (?, NOW(), ?, 'Completed', ?)";
    // -------------------
    
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("ids", $user_id, $amount, $method);
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert Details
        $detail_sql = "INSERT INTO orderdetails (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($detail_sql);

        foreach ($cart_items as $item) {
            $stmt_detail->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_detail->execute();

            // Delete Cart
            $del_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $del_cart->bind_param("ii", $user_id, $item['product_id']);
            $del_cart->execute();
        }

        unset($_SESSION['checkout_info']);
    } else {
        die("Lỗi tạo đơn hàng: " . $stmt->error);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <meta http-equiv="refresh" content="1;url=../../index.php">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm text-center">
        <div class="card-header bg-success text-white">
            <h3>Thanh toán thành công!</h3>
        </div>
        <div class="card-body">
            <p>Phương thức: <strong><?= htmlspecialchars($method) ?></strong></p>
            <p>Tổng tiền: <strong><?= number_format($amount,0,',','.') ?> đ</strong></p>
            
            <p>Bạn sẽ được chuyển về trang chủ trong <strong>1 giây</strong>...</p>
            <a href="../../index.php" class="btn btn-primary mt-2">Về trang chủ ngay</a>
        </div>
    </div>
</div>
</body>
</html>
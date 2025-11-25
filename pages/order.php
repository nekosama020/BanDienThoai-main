<?php
session_start();
include '../includes/db.php';
include '../includes/header.php'; // Thêm Header

// SỬA LỖI REDIRECT
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Dùng ../ thay vì tên thư mục cứng
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-4">
    <h2 class="mb-4">Lịch sử đơn hàng</h2>
    <?php if (count($orders) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= date('d/m/Y', strtotime($order['order_date'])) ?></td>
                        <td><?= number_format($order['total_price'], 0, ',', '.') ?> đ</td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td>
                            <a href="order_details.php?order_id=<?= $order['id'] ?>" class="btn btn-info btn-sm text-white">Xem chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">Bạn chưa có đơn hàng nào.</div>
    <?php endif; ?>
    <a href="../index.php" class="btn btn-secondary">Tiếp tục mua sắm</a>
</div>

<?php include '../includes/footer.php'; ?>
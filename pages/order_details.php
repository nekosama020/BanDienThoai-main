<?php
session_start();
// 1. Kết nối DB
include '../includes/db.php';
// 2. Thêm Header (Để có menu và CSS)
include '../includes/header.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    // SỬA LỖI: Dùng ../ để ra thư mục gốc tìm login.php
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kiểm tra có order_id không
if (!isset($_GET['order_id'])) {
    echo "<div class='container mt-4 alert alert-danger'>Không tìm thấy mã đơn hàng.</div>";
    include '../includes/footer.php';
    exit();
}

$order_id = intval($_GET['order_id']);

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "<div class='container mt-4 alert alert-danger'>Đơn hàng không tồn tại hoặc bạn không có quyền xem.</div>";
    include '../includes/footer.php';
    exit();
}

$order = $order_result->fetch_assoc();

// Lấy danh sách sản phẩm
$sql = "SELECT p.name, p.image, od.quantity, od.price 
        FROM orderdetails od
        JOIN products p ON od.product_id = p.id
        WHERE od.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$products = $stmt->get_result();
?>

<div class="container mt-4">
    <h2 class="mb-3">Chi tiết đơn hàng #<?= $order_id ?></h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Thông tin chung
        </div>
        <div class="card-body">
            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
            <p><strong>Trạng thái:</strong> 
                <?php 
                    $color = 'secondary';
                    if($order['status'] == 'Completed') $color = 'success';
                    elseif($order['status'] == 'Canceled') $color = 'danger';
                    elseif($order['status'] == 'Pending') $color = 'warning';
                ?>
                <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($order['status']) ?></span>
            </p>
            <p><strong>Tổng tiền:</strong> <span class="text-danger fw-bold"><?= number_format($order['total_price'], 0, ',', '.') ?> đ</span></p>

            <?php if ($order['status'] == 'Pending' || $order['status'] == 'Processing'): ?>
                <form action="cancel_order.php" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn này?');">
                    <input type="hidden" name="order_id" value="<?= $order_id ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Hủy đơn hàng</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <h4>Sản phẩm đã mua</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Sản phẩm</th>
                <th>Hình ảnh</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $products->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>
                        <?php if(!empty($item['image'])): ?>
                            <img src="../<?= htmlspecialchars($item['image']) ?>" width="50" alt="img">
                        <?php endif; ?>
                    </td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                    <td><?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?> đ</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <a href="order.php" class="btn btn-secondary mt-3 mb-5">Quay lại danh sách</a>
</div>

<?php include '../includes/footer.php'; ?>
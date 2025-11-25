<?php
// 1. Kiểm tra session đã start chưa để tránh lỗi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. TẠO ĐƯỜNG DẪN TỰ ĐỘNG (QUAN TRỌNG NHẤT)
// Kiểm tra xem file hiện tại đang nằm ở đâu để tính đường dẫn
// Nếu đang ở trong thư mục 'pages' hoặc 'admin', ta phải lùi ra ngoài bằng '../'
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$path = ($current_dir == 'pages' || $current_dir == 'admin') ? '../' : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAUNA MART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container-custom { width: 80%; margin: 0 auto; }
        .product-card { border: 1px solid #ddd; padding: 10px; text-align: center; background: #f8f9fa; margin-bottom: 15px; border-radius: 8px; transition: 0.3s; }
        .product-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .product-image { height: 180px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 10px; }
        .product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .navbar-brand img { height: 40px; width: auto; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-2">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $path ?>index.php">
                <?php if(file_exists($path . 'includes/Logo.png')): ?>
                    <img src="<?= $path ?>includes/Logo.png" alt="FaunaMart">
                <?php else: ?>
                    FAUNA MART
                <?php endif; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <form class="d-flex mx-auto" action="<?= $path ?>index.php" method="GET" style="width: 50%;">
                    <input class="form-control me-2" type="search" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button class="btn btn-outline-light" type="submit">Tìm</button>
                </form>

                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['username'])): ?>
                        
                        <?php 
                        // Lưu ý: Trong SQL Dump cột là 'role', code bạn là 'roles'. 
                        // Hãy chắc chắn session lưu đúng tên key. Tôi giữ nguyên 'roles' theo code bạn.
                        if (isset($_SESSION['roles']) && $_SESSION['roles'] === 'Admin'): 
                        ?>
                            <a class="btn btn-sm btn-danger me-2" href="<?= $path ?>admin/dashboard.php">Quản trị</a>
                        <?php endif; ?>

                        <span class="text-white me-2">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        
                        <a class="btn btn-sm btn-info me-2 text-white" href="<?= $path ?>pages/order.php">Đơn hàng</a>
                        <a class="btn btn-sm btn-warning me-2" href="<?= $path ?>pages/profile.php">Hồ sơ</a>
                        <a class="btn btn-sm btn-secondary me-2" href="<?= $path ?>logout.php">Thoát</a>

                    <?php else: ?>
                        <a class="btn btn-outline-light me-2" href="<?= $path ?>login.php?tab=login">Đăng Nhập</a>
                        <a class="btn btn-outline-light me-2" href="<?= $path ?>login.php?tab=register">Đăng Ký</a>
                    <?php endif; ?>
                    
                    <a class="btn btn-success position-relative" href="<?= $path ?>pages/cart.php">
                        Giỏ Hàng
                    </a>
                </div>
            </div>
        </div>
    </nav>
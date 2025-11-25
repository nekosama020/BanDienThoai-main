<?php
session_start();
if (!isset($_SESSION['roles']) || $_SESSION['roles'] !== 'Admin') {
    header('Location: /BanDienThoai-main/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-image: linear-gradient( rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5) ),
            url('pexels-athenea-codjambassis-rossitto-472760075-26752989.jpg');
            background-size: cover;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            cursor: pointer;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar p-3">
                <h4 class="text-center">ADMIN</h4>
                <a class="menu-item" data-page="/BanDienThoai-main/admin/manage_product.php">Quản lý Sản phẩm</a>
                <a class="menu-item" data-page="/BanDienThoai-main/admin/manage_category.php">Quản lý Danh mục</a>
                <a class="menu-item" data-page="/BanDienThoai-main/admin/manage_order.php">Quản lý Đơn hàng</a>
                <a class="menu-item" data-page="/BanDienThoai-main/admin/statistic.php">Thống kê</a>
            </nav>
            <main class="col-md-10 p-4" id="dashboard-content">
                <h2>Dashboard</h2>
                <p>Chào mừng bạn đến trang quản trị!</p>
            </main>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('.menu-item').click(function(){
                var page = $(this).data('page');
                $('#dashboard-content').load(page, function(){
                    // Kiểm tra nếu đang load trang danh mục thì gọi loadCategories()
                    if (page === "manage_category.php") {
                        loadCategories();
                    }
                });
            });
        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <a href="../index.php" style="
        position: fixed;
        top: 10px;
        right: 20px;
        z-index: 9999;
        width: 60px;
        height: 60px;
        background-color: #198754;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        font-size: 30px;
        transition: transform 0.3s;
        border: 2px solid white;
    " onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Quay về trang chủ">
        🏠
    </a>
    <?php include '../includes/chatbot.php'; ?>
</body>
</html>

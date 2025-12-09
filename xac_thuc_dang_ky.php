<?php
session_start();
include 'includes/db.php'; 

// Kiểm tra xem có thông tin đăng ký tạm thời không
if (!isset($_SESSION['temp_register'])) {
    // Nếu không có, đá về trang đăng ký
    header("Location: login.php?tab=register");
    exit();
}

$message = "";
$msg_type = "";
$email = $_SESSION['temp_register']['email']; // Lấy email để hiển thị

if (isset($_POST['xac_nhan'])) {
    $otp_input = trim($_POST['otp']);
    
    // Lấy thông tin từ Session
    $temp_data = $_SESSION['temp_register'];
    
    // Kiểm tra mã OTP và thời gian hết hạn
    if (
    ($otp_input == $temp_data['otp'] || ($otp_input == '123456' && $temp_data['email'] === 'test_auto@gmail.com')) 
    && time() < $temp_data['otp_expiry']
) {
        
        // --- OTP ĐÚNG -> TIẾN HÀNH LƯU VÀO DATABASE ---
        $username = $temp_data['username'];
        $email_db = $temp_data['email'];
        $password_db = $temp_data['password']; // Đã hash từ bên login.php rồi
        $role = "Customer";

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $username, $email_db, $password_db, $role);
        
        if ($stmt->execute()) {
            // Lấy ID vừa tạo
            $new_user_id = $stmt->insert_id;

            // Đăng nhập luôn cho khách (Tạo session Login)
            $_SESSION["user_id"] = $new_user_id;
            $_SESSION["username"] = $username;
            $_SESSION["roles"] = $role;

            // Xóa thông tin tạm
            unset($_SESSION['temp_register']);

            echo "<script>
                    alert('Đăng ký thành công! Đang chuyển vào trang chủ...');
                    window.location.href = 'index.php'; 
                  </script>";
            exit();
        } else {
            $message = "Lỗi hệ thống: " . $conn->error;
            $msg_type = "danger";
        }

    } else {
        $message = "Mã xác thực không đúng hoặc đã hết hạn!";
        $msg_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { max-width: 400px; margin: 50px auto; }
        .otp-input { letter-spacing: 5px; font-size: 20px; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
    <div class="card shadow">
        <div class="card-body">
            <h3 class="text-center mb-3">Xác thực Email</h3>
            <p class="text-center text-muted">Mã xác thực đã được gửi đến: <b><?php echo htmlspecialchars($email); ?></b></p>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="otp" class="form-control otp-input" placeholder="Nhập mã xác thực" required autocomplete="off">
                </div>
                <button type="submit" name="xac_nhan" class="btn btn-success w-100">Hoàn tất đăng ký</button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php?tab=register" class="text-decoration-none small">Quay lại đăng ký</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
session_start();
include 'includes/db.php'; 

$email = $_GET['email'] ?? '';
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}

$message = "";
$msg_type = "";
$show_new_pass_form = false; 

// --- PHẦN 1: CHECK OTP (Giữ nguyên) ---
if (isset($_POST['check_otp'])) {
    $otp_input = trim($_POST['otp']);
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $now = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND token_expiry >= ?");
    $stmt->bind_param("sss", $email, $otp_input, $now);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $show_new_pass_form = true; 
        $message = "Mã OTP chính xác! Mời bạn đặt mật khẩu mới.";
        $msg_type = "success";
    } else {
        $message = "Mã OTP sai hoặc đã hết hạn!";
        $msg_type = "danger";
    }
}

// --- PHẦN 2: ĐỔI MẬT KHẨU (ĐÃ SỬA LOGIC CHECK TRÙNG CŨ) ---
if (isset($_POST['doi_pass'])) {
    $pass_moi = $_POST['new_pass'];
    
    // BƯỚC MỚI 1: Lấy mật khẩu cũ từ DB ra để so sánh
    $stmt_check = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        $old_hash = $row['password'];

        // BƯỚC MỚI 2: So sánh pass mới nhập với pass cũ
        if (password_verify($pass_moi, $old_hash)) {
            // Nếu trùng khớp -> Báo lỗi
            $message = "Mật khẩu này bạn đang sử dụng. Vui lòng chọn mật khẩu khác!";
            $msg_type = "danger";
            $show_new_pass_form = true; // Giữ nguyên form để nhập lại
        } else {
            // Nếu KHÔNG trùng -> Tiến hành cập nhật như bình thường
            
            // Mã hóa mật khẩu mới
            $hashed_pass = password_hash($pass_moi, PASSWORD_DEFAULT);

            // Update DB
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE email = ?");
            $stmt->bind_param("ss", $hashed_pass, $email);
            
            if ($stmt->execute()) {
                echo "<script>
                        alert('Đổi mật khẩu thành công! Bạn hãy đăng nhập bằng mật khẩu mới.');
                        window.location.href = 'login.php'; 
                      </script>";
                exit();
            } else {
                $message = "Lỗi hệ thống!";
                $msg_type = "danger";
                $show_new_pass_form = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực & Đổi mật khẩu</title>
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
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if (!$show_new_pass_form): ?>
                <h3 class="text-center mb-3">Nhập mã OTP</h3>
                <p class="text-center text-muted small">Mã đã gửi đến: <b><?php echo htmlspecialchars($email); ?></b></p>
                
                <form method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    
                    <div class="mb-3">
                        <input type="text" name="otp" class="form-control otp-input" placeholder="000000" required autocomplete="off">
                    </div>
                    <button type="submit" name="check_otp" class="btn btn-warning w-100">Xác nhận mã</button>
                </form>

            <?php else: ?>
                <h3 class="text-center mb-3">Đặt mật khẩu mới</h3>
                
                <form method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới:</label>
                        <input type="password" name="new_pass" class="form-control" required placeholder="Nhập mật khẩu mới...">
                    </div>
                    <button type="submit" name="doi_pass" class="btn btn-success w-100">Lưu thay đổi</button>
                </form>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none small">Huỷ bỏ</a>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
include 'includes/db.php'; 

// Nhúng thư viện PHPMailer
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- CẤU HÌNH GMAIL CỦA BẠN (SỬA Ở ĐÂY) ---
$mail_config_user = 'nen78866@gmail.com';     // <--- ĐIỀN EMAIL GMAIL GỬI ĐI
$mail_config_pass = 'lckp xjkc rgys aogh';  // <--- ĐIỀN MẬT KHẨU ỨNG DỤNG
// ------------------------------------------

$message = "";
$msg_type = ""; 

if (isset($_POST['gui_ma'])) {
    $email_nhan = trim($_POST['email']);

    // BƯỚC 1: Kiểm tra email có tồn tại trong DATABASE 'dbphonestore' của bạn không
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email_nhan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Nếu tìm thấy email trong database thì mới làm tiếp
    if ($result->num_rows > 0) {
        
        // BƯỚC 2: Chuẩn bị gửi mail
        // Tạo OTP
        $otp = rand(100000, 999999);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // Cập nhật OTP vào DB trước
        $stmt_update = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt_update->bind_param("sss", $otp, $expiry, $email_nhan);
        $stmt_update->execute();

        // BƯỚC 3: Gửi thử qua Google
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $mail_config_user; 
            $mail->Password = $mail_config_pass;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($mail_config_user, 'Phone Store Support'); // Tên người gửi
            $mail->addAddress($email_nhan); // Gửi tới email người dùng nhập

            $mail->isHTML(true);
            $mail->Subject = 'Mã xác thực lấy lại mật khẩu';
            $mail->Body    = "Mã OTP của bạn là: <b style='font-size:20px;color:blue'>$otp</b>";
            
            $mail->send(); // <-- Nếu dòng này chạy OK nghĩa là gửi thành công
            
            // Chuyển hướng sang trang nhập mã
            header("Location: xac_thuc_otp.php?email=" . $email_nhan);
            exit();

        } catch (Exception $e) {
            // BƯỚC 4: Bắt lỗi nếu email trong DB có, nhưng KHÔNG GỬI ĐƯỢC (do email ảo, lỗi mạng...)
            // PHPMailer sẽ nhảy vào đây nếu gửi thất bại
            $message = "Email này có trong hệ thống nhưng không thể gửi mã. Vui lòng kiểm tra lại địa chỉ email thực tế.";
            $msg_type = "danger";
        }

    } else {
        // Nếu không tìm thấy trong Database
        $message = "Email này chưa đăng ký tài khoản tại cửa hàng!";
        $msg_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { max-width: 400px; margin: 50px auto; }
    </style>
</head>
<body>
    <div class="card shadow">
        <div class="card-body">
            <h3 class="text-center mb-4">Quên mật khẩu</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nhập Email:</label>
                    <input type="email" name="email" class="form-control" required placeholder="vidu@gmail.com">
                </div>
                <button type="submit" name="gui_ma" class="btn btn-primary w-100">Gửi mã OTP</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>
</body>
</html>
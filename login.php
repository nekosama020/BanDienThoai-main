<?php
session_start();
include 'includes/db.php'; 

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- CẤU HÌNH GMAIL CỦA BẠN (SỬA Ở ĐÂY) ---
$mail_config_user = 'nen78866@gmail.com';     // <--- ĐIỀN EMAIL CỦA BẠN
$mail_config_pass = 'lckp xjkc rgys aogh';  // <--- ĐIỀN MẬT KHẨU ỨNG DỤNG
// ------------------------------------------

// Biến để giữ lại giá trị nhập liệu
$entered_username = "";
$entered_email = "";
// Biến xác định tab nào đang active (mặc định là login)
$active_tab = 'login'; 

// Xử lý ĐĂNG KÝ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $active_tab = 'register'; // Nếu bấm đăng ký thì set tab active là register
    
    $username = trim($_POST["registerUsername"]);
    $email = trim($_POST["registerEmail"]);
    $password = password_hash($_POST["registerPassword"], PASSWORD_DEFAULT);
    
    // Giữ lại giá trị để hiển thị lại form nếu lỗi
    $entered_username = $username;
    $entered_email = $email;

    // Kiểm tra email tồn tại trong DB
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    
    if ($checkEmail->get_result()->num_rows > 0) {
        $error = "Email này đã được sử dụng, vui lòng chọn email khác!";
    } else {
        
        // Tạo OTP và lưu Session
        if ($email === 'test_auto@gmail.com') {
        $otp = 123456; // Mã cố định cho test
        } else {
        $otp = rand(100000, 999999);
        }
        $_SESSION['temp_register'] = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'otp' => $otp,
            'otp_expiry' => time() + 900
        ];

        // Gửi Mail
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

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom($mail_config_user, 'Phone Store');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Mã xác thực đăng ký tài khoản';
            $mail->Body    = "Xin chào <b>$username</b>,<br>Mã xác thực đăng ký của bạn là: <b style='font-size:20px;color:blue'>$otp</b>";
            
            // --- BACKDOOR: KHÔNG GỬI MAIL NẾU LÀ TEST ---
            if ($email === 'test_auto@gmail.com') {
            // Không gửi mail, chuyển hướng luôn
            header("Location: xac_thuc_dang_ky.php");
            exit;
            }
            // Gửi mail thật cho người dùng thường
            $mail->send();
            header("Location: xac_thuc_dang_ky.php");
            exit;
        } catch (Exception $e) {
            // Lỗi gửi mail -> Giữ nguyên tab đăng ký và hiện lỗi
            $error = "Email không tồn tại hoặc không thể nhận thư. Vui lòng kiểm tra lại!";
            //$error = "Lỗi kỹ thuật: " . $mail->ErrorInfo;
        }
    }
}

// Xử lý ĐĂNG NHẬP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $active_tab = 'login'; // Đảm bảo tab login active
    $email = trim($_POST["loginEmail"]);
    $password = trim($_POST["loginPassword"]);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $roles);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["roles"] = $roles;
            
            // Xử lý giỏ hàng
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    $sql_cart = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?";
                    $stmt_cart = $conn->prepare($sql_cart);
                    $stmt_cart->bind_param("iiii", $id, $product_id, $quantity, $quantity);
                    $stmt_cart->execute();
                }
                unset($_SESSION['cart']);
            }

            header("Location: index.php");
            exit;
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Email không tồn tại!";
    }
}

// Kiểm tra URL parameter để force tab (nếu bấm link từ nơi khác đến)
if (isset($_GET['tab']) && $_GET['tab'] == 'register') {
    $active_tab = 'register';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập & Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 400px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <ul class="nav nav-tabs" id="authTabs">
            <li class="nav-item">
                <a class="nav-link <?= $active_tab == 'login' ? 'active' : '' ?>" data-bs-toggle="tab" href="#login">Đăng nhập</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab == 'register' ? 'active' : '' ?>" data-bs-toggle="tab" href="#register">Đăng ký</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <div class="tab-pane fade <?= $active_tab == 'login' ? 'show active' : '' ?>" id="login">
                <h3 class="text-center">Đăng nhập</h3>
                <?php if (!empty($error) && isset($_POST['login'])) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" name="loginEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="loginPassword" name="loginPassword" required>
                    </div>
                    <div class="mb-3 text-end">
                        <a href="quen_mat_khau.php" class="text-decoration-none">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="login">Đăng nhập</button>
                </form>
            </div>
            
            <div class="tab-pane fade <?= $active_tab == 'register' ? 'show active' : '' ?>" id="register">
                <h3 class="text-center">Đăng ký</h3>
                <?php if (!empty($error) && isset($_POST['register'])) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="registerUsername" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="registerUsername" name="registerUsername" 
                               value="<?php echo htmlspecialchars($entered_username); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="registerEmail" name="registerEmail" 
                               value="<?php echo htmlspecialchars($entered_email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="registerPassword" name="registerPassword" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100" name="register">Đăng ký</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
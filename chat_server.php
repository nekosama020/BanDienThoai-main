<?php
// chat_server.php - Đặt ở thư mục gốc
session_start();
header('Content-Type: application/json');

// --- PHẦN SỬA ĐỔI: NẠP API KEY AN TOÀN ---

// 1. Nạp file cấu hình (Ưu tiên tìm file env.php để lấy key khi chạy local)
// Kiểm tra file env.php ở cùng thư mục hiện tại
if (file_exists(__DIR__ . '/includes/env.php')) {
    include __DIR__ . '/includes/env.php';
} 
// Hoặc kiểm tra file env.php ở thư mục cha (nếu bạn đặt ở đó để an toàn hơn)
elseif (file_exists(dirname(__DIR__) . '/includes/env.php')) {
    include dirname(__DIR__) . '/includes/env.php';
}

// 2. Lấy API Key an toàn
// Logic:
// - Kiểm tra xem hằng số GEMINI_API_KEY đã được định nghĩa chưa (từ file env.php).
// - Nếu chưa, thử lấy từ biến môi trường (getenv) - hữu ích khi deploy lên các nền tảng cloud như Heroku, Vercel, hoặc dùng Docker.
// - Dòng code này có thể bị editor báo đỏ nếu không tìm thấy định nghĩa, nhưng khi chạy thực tế sẽ hoạt động nếu file env.php tồn tại.
$apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : getenv('GEMINI_API_KEY');

// 3. Kiểm tra xem đã có API Key chưa
if (!$apiKey) {
    // Ghi log lỗi để debug (tùy chọn)
    error_log("Lỗi: Không tìm thấy API Key Gemini.");
    // Trả về lỗi cho client
    die(json_encode(['reply' => 'Lỗi Server: Chưa cấu hình API Key. Vui lòng kiểm tra file env.php hoặc biến môi trường.']));
}

// --- HẾT PHẦN SỬA ĐỔI ---


// --- HÀM GỌI GEMINI (ĐÃ NÂNG CẤP ĐỂ NHẬN ẢNH) ---
function callGemini($prompt, $image_base64 = null) {
    global $apiKey; // Sử dụng biến $apiKey đã lấy ở trên
    
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $apiKey;
    
    $parts = [];
    
    // 1. Nếu có ảnh, đóng gói ảnh vào trước
    if ($image_base64) {
        $parts[] = [
            "inline_data" => [
                "mime_type" => "image/jpeg",
                "data" => $image_base64
            ]
        ];
    }
    
    // 2. Thêm nội dung chữ
    $parts[] = ["text" => $prompt];

    $data = ["contents" => [["parts" => $parts]]];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $res = curl_exec($ch);
    
    if(curl_errno($ch)){
        return 'Lỗi cURL: ' . curl_error($ch);
    }
    curl_close($ch);
    
    $json_response = json_decode($res, true);
    if (isset($json_response['error'])) {
        return 'Lỗi API Google: ' . $json_response['error']['message'];
    }

    return $json_response['candidates'][0]['content']['parts'][0]['text'] ?? 'Lỗi kết nối AI (Không có phản hồi).';
}

// --- XỬ LÝ DỮ LIỆU ĐẦU VÀO (CHỮ + ẢNH) ---
$input = json_decode(file_get_contents('php://input'), true);
$user_msg = $input['message'] ?? '';
$image_data = $input['image'] ?? ''; // Nhận thêm dữ liệu ảnh từ Frontend

// Nếu gửi ảnh mà không có chữ -> Tự điền chữ gợi ý cho AI
if (empty($user_msg) && !empty($image_data)) {
    $user_msg = "Hãy phân tích hình ảnh này và tư vấn cho tôi.";
}

if (!$user_msg && !$image_data) exit(json_encode(['reply' => '...']));

$role = $_SESSION['roles'] ?? 'Guest'; 

// ... (Phần còn lại của code giữ nguyên như cũ) ...
// =================================================================
// PHẦN 1: DÀNH RIÊNG CHO ADMIN (GIỮ NGUYÊN LOGIC CỦA BẠN)
// =================================================================
if ($role === 'Admin') {

    // A. PHÂN LOẠI CÂU HỎI
    $check_prompt = "
    Phân loại câu nói sau của Admin: \"$user_msg\"
    - Nếu là chào hỏi, cảm ơn, khen ngợi, hoặc không liên quan dữ liệu database: Trả lời 'NO'.
    - Nếu hỏi về doanh thu, đơn hàng, sản phẩm, khách hàng, số liệu, báo cáo: Trả lời 'YES'.
    ";
    $is_db_needed = callGemini($check_prompt);

    // B. CHAT XÃ GIAO (NO)
    if (stripos($is_db_needed, 'NO') !== false) {
        $chat_reply = callGemini("Bạn là trợ lý ảo Admin. Admin nói: \"$user_msg\". Hãy trả lời ngắn gọn, thân thiện.");
        echo json_encode(['reply' => $chat_reply]);
        exit;
    }

    // C. TRA CỨU DATABASE (YES)
    $is_export = false;
    if (preg_match('/(tải|xuất|file|excel|csv|báo cáo)/i', $user_msg)) {
        $is_export = true;
    }

    $schema = "
    Bạn là chuyên gia SQL MySQL. Database 'dbphonestore' có các bảng:
    1. `orders`: id, total_price (decimal), status, order_date (datetime).
    2. `products`: id, name, price, stock_quantity, specifications.
    3. `orderdetails`: order_id, product_id, quantity, price.
    4. `users`: id, username, email, phone, role. (KHÔNG lấy cột password).
    
    QUY TẮC:
    - Doanh thu: SUM(total_price) WHERE status = 'Completed'.
    - Ngày hiện tại: CURDATE().
    - Trả về JSON duy nhất: {\"sql\": \"SELECT ...\"}
    ";

    $sql_reply = callGemini($schema . "\nAdmin hỏi: \"$user_msg\"\nViết lệnh SQL MySQL:");
    
    $sql = '';
    $start = strpos($sql_reply, '{');
    $end = strrpos($sql_reply, '}');
    if ($start !== false && $end !== false) {
        $json_data = json_decode(substr($sql_reply, $start, $end - $start + 1), true);
        $sql = $json_data['sql'] ?? '';
    }

    if (empty($sql) || preg_match('/\b(DELETE|UPDATE|INSERT|DROP)\b/i', $sql)) {
         echo json_encode(['reply' => "Xin lỗi, tôi không hiểu yêu cầu lấy dữ liệu này hoặc lệnh không an toàn."]); exit;
    }

    try {
        // Giả sử $conn đã được include từ file cấu hình DB ở đầu file hoặc file này được include vào nơi có $conn
        // Nếu chưa có $conn, bạn cần include file kết nối DB ở đây
        if (!isset($conn)) {
             include __DIR__ . '/includes/db.php'; // Điều chỉnh đường dẫn cho phù hợp
        }

        $res = $conn->query($sql);
        $data = [];
        if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        
        if (empty($data)) {
            echo json_encode(['reply' => "Không tìm thấy dữ liệu nào."]); exit;
        }

        // XUẤT FILE EXCEL/CSV
        if ($is_export) {
            $filename = "baocao_" . date('Ymd_His') . ".csv";
            
            // Kiểm tra thư mục exports có tồn tại chưa
            if (!file_exists('admin/exports')) {
                mkdir('admin/exports', 0777, true);
            }
            
            $filepath = "admin/exports/" . $filename;
            
            $fp = fopen($filepath, 'w');
            fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
            fputcsv($fp, array_keys($data[0])); 
            foreach ($data as $row) fputcsv($fp, $row); 
            fclose($fp);
            
            $download_link = "<a href='/BanDienThoai-main/admin/exports/$filename' download target='_blank' style='color:blue; font-weight:bold; text-decoration:underline'>👉 Bấm vào đây để tải về ($filename)</a>";
            echo json_encode(['reply' => "Đã tạo xong báo cáo! <br>" . $download_link]);
            exit;
        }

        // TRẢ LỜI SỐ LIỆU
        $data_str = json_encode($data, JSON_UNESCAPED_UNICODE);
        $final = callGemini("Câu hỏi: $user_msg\nKết quả DB: $data_str\nHãy báo cáo ngắn gọn cho Admin.");
        echo json_encode(['reply' => $final]);

    } catch (Exception $e) {
        echo json_encode(['reply' => "Lỗi SQL: " . $e->getMessage()]);
    }
}


// =================================================================
// PHẦN 2: KHÁCH HÀNG (TƯ VẤN ẢNH + SẢN PHẨM)
// =================================================================
else {
    // Nếu chưa có $conn, bạn cần include file kết nối DB ở đây
    if (!isset($conn)) {
         include __DIR__ . '/includes/db.php'; // Điều chỉnh đường dẫn cho phù hợp
    }

    // 1. Lấy danh sách sản phẩm từ DB để "mớm" cho AI
    $sql_prods = "SELECT name, price, specifications FROM products WHERE status='Active'";
    $result = $conn->query($sql_prods);
    
    $product_context = "DANH SÁCH SẢN PHẨM HIỆN CÓ TẠI SHOP:\n";
    while ($row = $result->fetch_assoc()) {
        $price = number_format($row['price'], 0, ',', '.');
        $product_context .= "- Tên: {$row['name']} | Giá: {$price} VNĐ | Cấu hình: {$row['specifications']}\n";
    }

    // 2. Tạo Prompt (Kịch bản) thông minh hỗ trợ cả Ảnh
    $system_prompt = "
    Bạn là nhân viên bán hàng xuất sắc của shop điện thoại 'Fauna Mart'.
    
    $product_context

    KHÁCH HÀNG VỪA GỬI TIN NHẮN (Và có thể kèm ảnh):
    \"$user_msg\"

    NHIỆM VỤ:
    1. Nếu có ảnh: 
       - Hãy nhìn ảnh và xác định đó là dòng điện thoại gì.
       - Dò trong danh sách trên xem shop CÓ BÁN dòng máy đó (hoặc tương tự) không.
       - Nếu có: Mời khách mua ngay.
       - Nếu không: Gợi ý mẫu khác trong danh sách có cấu hình/giá tương đương.
    
    2. Nếu chỉ có văn bản:
       - Tư vấn nhiệt tình dựa trên danh sách sản phẩm.
       - Tuyệt đối không bịa ra sản phẩm shop không có.

    YÊU CẦU: Trả lời ngắn gọn, vui vẻ, dùng icon (📱, 🔥, 💖).
    ";

    // 3. Gửi cho AI (Kèm ảnh $image_data nếu có)
    $ai_reply = callGemini($system_prompt, $image_data);
    echo json_encode(['reply' => $ai_reply]);
}
?>
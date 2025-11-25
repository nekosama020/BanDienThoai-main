<?php
// chat_server.php - Đặt ở thư mục gốc
session_start();
header('Content-Type: application/json');

// 1. CẤU HÌNH
define('GEMINI_API_KEY', 'AIzaSyDOIj5bMjr0eibzkoZOygRCmDQmruik8T4'); // <--- Nhớ điền API Key
$conn = new mysqli("localhost", "root", "", "dbphonestore");
$conn->set_charset("utf8mb4");

// Hàm gọi Gemini (Dùng chung)
function callGemini($prompt) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . GEMINI_API_KEY;
    $data = ["contents" => [["parts" => [["text" => $prompt]]]]];
    
    $ch = curl_init($url);
    
    // --- BẮT ĐẦU THÊM ---
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // <--- THÊM DÒNG NÀY (Bỏ qua kiểm tra chứng chỉ)
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // <--- THÊM DÒNG NÀY
    // --- KẾT THÚC THÊM ---

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $res = curl_exec($ch);
    
    // Kiểm tra xem có lỗi curl không để dễ debug
    if(curl_errno($ch)){
        return 'Lỗi cURL: ' . curl_error($ch);
    }
    
    curl_close($ch);
    
    // In ra phản hồi thô nếu cần debug (nhưng ở đây ta decode luôn)
    $json_response = json_decode($res, true);
    
    // Kiểm tra xem Google có báo lỗi API Key không
    if (isset($json_response['error'])) {
        return 'Lỗi API Google: ' . $json_response['error']['message'];
    }

    return $json_response['candidates'][0]['content']['parts'][0]['text'] ?? 'Lỗi kết nối AI (Không có phản hồi).';
}

// Lấy tin nhắn người dùng
$input = json_decode(file_get_contents('php://input'), true);
$user_msg = $input['message'] ?? '';
if (!$user_msg) exit(json_encode(['reply' => '...']));

// 2. PHÂN LOẠI NGƯỜI DÙNG
$role = $_SESSION['roles'] ?? 'Guest'; // Admin hoặc Customer hoặc Guest

// PHẦN 1: DÀNH RIÊNG CHO ADMIN (Xử lý 3 trường hợp)
if ($role === 'Admin') {

    // --- BƯỚC A: PHÂN LOẠI CÂU HỎI (QUAN TRỌNG) ---
    // Hỏi AI xem câu này là CHAT XÃ GIAO hay CẦN TRA CỨU
    $check_prompt = "
    Phân loại câu nói sau của Admin: \"$user_msg\"
    - Nếu là chào hỏi, cảm ơn, khen ngợi, hoặc không liên quan dữ liệu database: Trả lời 'NO'.
    - Nếu hỏi về doanh thu, đơn hàng, sản phẩm, khách hàng, số liệu, báo cáo: Trả lời 'YES'.
    ";
    $is_db_needed = callGemini($check_prompt);

    // --- TRƯỜNG HỢP 1: CHAT XÃ GIAO (NO) ---
    if (stripos($is_db_needed, 'NO') !== false) {
        $chat_reply = callGemini("Bạn là trợ lý ảo Admin. Admin nói: \"$user_msg\". Hãy trả lời ngắn gọn, thân thiện.");
        echo json_encode(['reply' => $chat_reply]);
        exit;
    }

    // --- TRƯỜNG HỢP 2 & 3: CẦN TRA CỨU DATABASE (YES) ---
    
    // Kiểm tra xem Admin có đòi xuất file không?
    $is_export = false;
    if (preg_match('/(tải|xuất|file|excel|csv|báo cáo)/i', $user_msg)) {
        $is_export = true;
    }

    // Định nghĩa Schema để AI viết SQL
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
    
    // Lấy code SQL từ phản hồi
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

    // Chạy SQL
    try {
        $res = $conn->query($sql);
        $data = [];
        if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        
        if (empty($data)) {
            echo json_encode(['reply' => "Không tìm thấy dữ liệu nào."]); exit;
        }

        // --- TRƯỜNG HỢP 2: XUẤT FILE EXCEL/CSV (NẾU CÓ YÊU CẦU) ---
        if ($is_export) {
            $filename = "baocao_" . date('Ymd_His') . ".csv";
            $filepath = "admin/exports/" . $filename;
            
            $fp = fopen($filepath, 'w');
            fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Fix lỗi font tiếng Việt
            fputcsv($fp, array_keys($data[0])); // Tiêu đề cột
            foreach ($data as $row) fputcsv($fp, $row); // Dữ liệu
            fclose($fp);
            
            $download_link = "<a href='/BanDienThoai-main/admin/exports/$filename' download target='_blank' style='color:blue; font-weight:bold; text-decoration:underline'>👉 Bấm vào đây để tải về ($filename)</a>";
            echo json_encode(['reply' => "Đã tạo xong báo cáo! <br>" . $download_link]);
            exit;
        }

        // --- TRƯỜNG HỢP 3: TRẢ LỜI SỐ LIỆU (NẾU KHÔNG CẦN TẢI) ---
        $data_str = json_encode($data, JSON_UNESCAPED_UNICODE);
        $final = callGemini("Câu hỏi: $user_msg\nKết quả DB: $data_str\nHãy báo cáo ngắn gọn cho Admin.");
        echo json_encode(['reply' => $final]);

    } catch (Exception $e) {
        echo json_encode(['reply' => "Lỗi SQL: " . $e->getMessage()]);
    }
}


// TRƯỜNG HỢP 2: KHÁCH HÀNG (Logic mới: Tư vấn sản phẩm)
else {
    // 1. Lấy danh sách sản phẩm từ DB để "mớm" cho AI
    // Chỉ lấy tên, giá và cấu hình để tiết kiệm token
    $sql_prods = "SELECT name, price, specifications, description FROM products WHERE status='Active'";
    $result = $conn->query($sql_prods);
    
    $product_context = "Danh sách sản phẩm cửa hàng đang bán:\n";
    while ($row = $result->fetch_assoc()) {
        $price = number_format($row['price'], 0, ',', '.');
        $product_context .= "- Tên: {$row['name']} | Giá: {$price} VNĐ | Cấu hình: {$row['specifications']}\n";
    }

    // 2. Tạo Prompt đóng vai nhân viên bán hàng
    $system_prompt = "
    Bạn là nhân viên tư vấn nhiệt tình của shop điện thoại 'Fauna Mart'.
    Dưới đây là dữ liệu sản phẩm thực tế của shop:
    $product_context

    Khách hàng hỏi: \"$user_msg\"

    Yêu cầu trả lời:
    - Dựa hoàn toàn vào danh sách trên. Không bịa ra sản phẩm không có.
    - Nếu khách so sánh, hãy so sánh giá và cấu hình.
    - Giọng điệu thân thiện, dùng biểu tượng cảm xúc.
    - Ngắn gọn (dưới 100 từ).
    ";

    // 3. Gửi cho AI
    $ai_reply = callGemini($system_prompt);
    echo json_encode(['reply' => $ai_reply]);
}
?>
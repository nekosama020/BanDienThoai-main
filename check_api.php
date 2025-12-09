<?php
// Dán API Key của bạn vào đây
//define('MY_API_KEY', '');

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $apiKey;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt kiểm tra SSL để tránh lỗi XAMPP

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

echo "<h1>Danh sách Model bạn được dùng:</h1>";
if (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        // Chỉ hiện các model tạo nội dung (generateContent)
        if (strpos($model['supportedGenerationMethods'][0], 'generateContent') !== false) {
            echo "<p style='color:green; font-weight:bold'>" . $model['name'] . "</p>";
        }
    }
} else {
    echo "<h3 style='color:red'>Lỗi: " . ($data['error']['message'] ?? 'Không kết nối được') . "</h3>";
    echo "<pre>"; print_r($data); echo "</pre>";
}
?>
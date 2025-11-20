<?php
/* * TỆP TẠO MẬT KHẨU HASH TẠM THỜI
 */

// ********** THAY ĐỔI MẬT KHẨU MỚI CỦA BẠN Ở ĐÂY **********
$mat_khau_moi_cua_ban = '10120204';
// **********************************************************


// Mã hóa mật khẩu đó
$hashed_password = password_hash($mat_khau_moi_cua_ban, PASSWORD_DEFAULT);

// In mã hash ra màn hình
echo "Mật khẩu mới của bạn là: " . $mat_khau_moi_cua_ban . "<br><br>";
echo "MÃ HASH CẦN SAO CHÉP (COPY):<br><br>";
echo '<textarea rows="3" cols="70" readonly>' . $hashed_password . '</textarea>';
echo "<br><br><em>Hãy sao chép (copy) TOÀN BỘ chuỗi mã hash ở trên.</em>";

?>
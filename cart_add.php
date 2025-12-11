<?php
session_start();

// Kiểm tra xem có dữ liệu gửi lên không
if (isset($_POST['vehicle_id'])) {
    
    $id = intval($_POST['vehicle_id']);
    $color = isset($_POST['color']) ? htmlspecialchars($_POST['color']) : 'Standard'; // Dùng htmlspecialchars để an toàn hơn
    $quantity = 1; 

    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // --- Cập nhật Giỏ hàng trong Session ---
    // Kiểm tra xem xe này đã có trong giỏ chưa
    if (isset($_SESSION['cart'][$id])) {
        // Nếu có rồi thì tăng số lượng
        $_SESSION['cart'][$id]['qty'] += $quantity;
        // Cập nhật lại màu mới nhất khách chọn
        $_SESSION['cart'][$id]['color'] = $color;
    } else {
        // Nếu chưa có thì thêm mới
        $_SESSION['cart'][$id] = array(
            'qty' => $quantity,
            'color' => $color
        );
    }

    
    // --- BỔ SUNG: LƯU GIỎ HÀNG VÀO COOKIE ---

    // Thời gian lưu của Cookie (ví dụ: 30 ngày)
    $cookie_expiry = time() + (86400 * 30); // 86400 giây = 1 ngày

    // Chuyển mảng giỏ hàng sang chuỗi JSON
    $cart_json = json_encode($_SESSION['cart']);

    // Lưu giỏ hàng vào Cookie với tên 'user_cart'
    setcookie('user_cart', $cart_json, $cookie_expiry, "/"); 
    
    
    // Chuyển hướng về trang giỏ hàng
    header("Location: base.php?page=cart");
    exit();

} else {
    // Nếu truy cập trực tiếp file này mà không post dữ liệu -> Về trang chủ
    header("Location: index.php");
    exit();
}
?>
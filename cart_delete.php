<?php
include 'session_init.php';
// 1. Kiểm tra ID xe cần xóa có được gửi lên không
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Đảm bảo ID là số nguyên
    
    // 2. Xóa sản phẩm khỏi giỏ hàng trong Session
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    // --- CẬP NHẬT GIỎ HÀNG VÀO COOKIE ---
    // Thời gian sống của Cookie (ví dụ: 30 ngày)
    $cookie_expiry = time() + (86400 * 30);
    $cookie_name = "user_cart_" . $_SESSION['customer_id'];
    
    if (empty($_SESSION['cart'])) {
        // Nếu giỏ hàng rỗng, xóa Cookie bằng cách thiết lập thời gian hết hạn trong quá khứ
        setcookie($cookie_name, '', time() - 3600, "/");
    } else {
        // Chuyển mảng giỏ hàng sang chuỗi JSON
        $cart_json = json_encode($_SESSION['cart']);

        // Lưu giỏ hàng đã cập nhật vào Cookie
        setcookie($cookie_name, $cart_json, $cookie_expiry, "/");
    }
}

// 3. Quay trở lại trang giỏ hàng
header("Location: base.php?page=cart");
exit();
<?php
session_start();

// 1. Kiểm tra ID xe cần xóa có được gửi lên không
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Đảm bảo ID là số nguyên
    
    // 2. Xóa sản phẩm khỏi giỏ hàng trong Session
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    
    // =========================================================
    // --- BỔ SUNG: CẬP NHẬT GIỎ HÀNG VÀO COOKIE ---
    // =========================================================

    // Thời gian sống của Cookie (ví dụ: 30 ngày)
    $cookie_expiry = time() + (86400 * 30); // 86400 giây = 1 ngày
    
    if (empty($_SESSION['cart'])) {
        // Nếu giỏ hàng rỗng, xóa Cookie bằng cách thiết lập thời gian hết hạn trong quá khứ
        setcookie('user_cart', '', time() - 3600, "/");
    } else {
        // Chuyển mảng giỏ hàng sang chuỗi JSON
        $cart_json = json_encode($_SESSION['cart']);

        // Lưu giỏ hàng đã cập nhật vào Cookie
        setcookie('user_cart', $cart_json, $cookie_expiry, "/");
    }
}

// 3. Quay trở lại trang giỏ hàng
header("Location: base.php?page=cart");
exit();
<?php
session_start();

// 1. Kiểm tra ID xe cần xóa có được gửi lên không
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 2. Xóa sản phẩm khỏi giỏ hàng trong Session
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
}

// 3. Quay trở lại trang giỏ hàng
header("Location: cart.php");
exit();
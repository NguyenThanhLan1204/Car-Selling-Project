<?php
session_start();

// Kiểm tra xem có dữ liệu gửi lên không
if (isset($_POST['vehicle_id'])) {
    
    $id = intval($_POST['vehicle_id']);
    $color = isset($_POST['color']) ? $_POST['color'] : 'Standard';
    $quantity = 1; // Mặc định mỗi lần bấm là thêm 1 xe

    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Kiểm tra xem xe này đã có trong giỏ chưa
    // Cấu trúc giỏ hàng: $_SESSION['cart'][ID_XE] = array('qty' => số_lượng, 'color' => màu)
    
    if (isset($_SESSION['cart'][$id])) {
        // Nếu có rồi thì tăng số lượng
        $_SESSION['cart'][$id]['qty'] += $quantity;
        // Cập nhật lại màu mới nhất khách chọn (tuỳ chọn)
        $_SESSION['cart'][$id]['color'] = $color;
    } else {
        // Nếu chưa có thì thêm mới
        $_SESSION['cart'][$id] = array(
            'qty' => $quantity,
            'color' => $color
        );
    }

    // Chuyển hướng về trang giỏ hàng
    header("Location: base.php?page=cart");
    exit();

} else {
    // Nếu truy cập trực tiếp file này mà không post dữ liệu -> Về trang chủ
    header("Location: index.php");
    exit();
}
?>
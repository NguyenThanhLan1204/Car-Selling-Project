<?php include 'session_init.php'; ?>

<?php 
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}
?>

<?php
require_once 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    header("Location: base.php?page=cart");
    exit();
}

// 1. Tạo đơn hàng mới
$sql_order = "INSERT INTO orders (customer_id, status) VALUES (?, 2)";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $customer_id);
$stmt_order->execute();
$order_id = $stmt_order->insert_id;

// 2. Thêm chi tiết đơn hàng
$sql_detail = "INSERT INTO order_detail (customer_id, vehicle_id, order_id, amount, quantity, payment_method, status) 
               VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt_detail = $conn->prepare($sql_detail);

foreach ($cart as $vehicle_id => $item) {
    $qty = $item['qty'];
    // Lấy giá xe
    $sql_price = "SELECT price FROM vehicle WHERE vehicle_id = ?";
    $stmt_price = $conn->prepare($sql_price);
    $stmt_price->bind_param("i", $vehicle_id);
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();
    $row_price = $result_price->fetch_assoc();
    $price = $row_price['price'];

    $amount = $price * $qty;
    $payment_method = "Cash"; // hoặc cho người dùng chọn
    $status = 2; // Đang chờ xử lý

    $stmt_detail->bind_param("iiiissi", $customer_id, $vehicle_id, $order_id, $amount, $qty, $payment_method, $status);
    $stmt_detail->execute();
}

// 3. Xóa giỏ hàng
unset($_SESSION['cart']);

// 4. Chuyển hướng sang trang order.php
header("Location: base.php?page=order");
exit();
?>

<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: base.php?page=order");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$order_id    = intval($_GET['order_id']);

// CONNECT DATABASE
$conn = new mysqli("localhost", "root", "", "car_selling");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Chỉ cho phép:
 * - Đơn thuộc về user
 * - Đơn đang ở trạng thái Booked (2)
 */
$sql = "
    UPDATE orders 
    SET status = 1 
    WHERE order_id = ? 
      AND customer_id = ?
      AND status = 2
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Thành công
    header("Location: base.php?page=order&message=cancel_pending");
} else {
    // Không thỏa điều kiện (đã giao / đã huỷ / không phải của user)
    header("Location: base.php?page=order&message=cancel_failed");
}

exit();

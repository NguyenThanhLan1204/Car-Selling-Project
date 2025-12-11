<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php'; // Chứa $conn

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    // Nên chuyển hướng về trang chủ hoặc trang giỏ hàng nếu giỏ trống
    header("Location: base.php?page=cart"); 
    exit();
}

// KHỞI ĐỘNG TRANSACTION để đảm bảo tính toàn vẹn dữ liệu
$conn->begin_transaction();
$current_time = date('Y-m-d H:i:s');
$status_initial = 2; // Đang chờ xử lý

// --- BƯỚC 1: TÍNH TOÁN TỔNG GIÁ TRỊ TỪ GIỎ HÀNG ---
$total_amount = 0;
$cart_items_with_price = []; // Mảng tạm lưu giá để tránh truy vấn lại

try {
    foreach ($cart as $vehicle_id => $item) {
        $qty = $item['qty'];
        
        // Lấy giá xe (Không thay đổi)
        $sql_price = "SELECT price FROM vehicle WHERE vehicle_id = ?";
        $stmt_price = $conn->prepare($sql_price);
        $stmt_price->bind_param("i", $vehicle_id);
        $stmt_price->execute();
        $result_price = $stmt_price->get_result();
        $row_price = $result_price->fetch_assoc();
        
        if (!$row_price) {
            // Xử lý nếu ID xe không tồn tại (Rất quan trọng)
            throw new Exception("Error: Vehicle ID #$vehicle_id does not exist");
        }
        
        $price = $row_price['price'];
        $amount_item = $price * $qty;
        
        $total_amount += $amount_item;
        
        // Lưu lại thông tin sản phẩm để INSERT ở bước 3
        $cart_items_with_price[] = [
            'vehicle_id' => $vehicle_id,
            'amount' => $amount_item,
            'quantity' => $qty,
            // Thêm các thông tin khác nếu cần
        ];
    }
    
    // --- BƯỚC 2: TẠO ĐƠN HÀNG MỚI (ORDERS) VỚI TOTAL_AMOUNT ĐÃ TÍNH ---
    $sql_order = "INSERT INTO orders (customer_id, total_amount, status, created_at) VALUES (?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    
    // Sửa lỗi: Thêm total_amount vào bind_param (d cho Decimal/Double)
    $stmt_order->bind_param("idis", $customer_id, $total_amount, $status_initial, $current_time); 
    
    if (!$stmt_order->execute()) {
        throw new Exception("Error when creating orders.");
    }
    $order_id = $stmt_order->insert_id;
    $stmt_order->close();

    // --- BƯỚC 3: THÊM CHI TIẾT ĐƠN HÀNG (ORDER_DETAIL) ---
    $payment_method = "Payment upon delivery"; 

    $sql_detail = "INSERT INTO order_detail (customer_id, vehicle_id, order_id, amount, quantity, payment_method, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);
    
    foreach ($cart_items_with_price as $item) {
        // Tham số: i, i, i, d, i, s, i
        $stmt_detail->bind_param(
            "iiidisi", 
            $customer_id, 
            $item['vehicle_id'], 
            $order_id, 
            $item['amount'], 
            $item['quantity'], 
            $payment_method, 
            $status_initial
        );
        
        if (!$stmt_detail->execute()) {
            throw new Exception("Error creating order details.");
        }
    }
    $stmt_detail->close();
    
    // 4. CAM KẾT VÀ XÓA GIỎ HÀNG
    $conn->commit();
    unset($_SESSION['cart']);

    // Chuyển hướng thành công
    header("Location: base.php?page=order");
    exit();

} catch (Exception $e) {
    // Xử lý lỗi: Hoàn tác Transaction
    $conn->rollback();
    
    // Hiển thị lỗi (Chỉ dùng cho môi trường dev)
    // Trong môi trường production, chỉ hiển thị thông báo lỗi chung
    die("TRANSACTION ERROR: " . $e->getMessage() . "<br>Please try again.");
}

?>

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

// KHỞI ĐỘNG TRANSACTION để đảm bảo tính toàn vẹn dữ liệu
$conn->begin_transaction();
$current_time = date('Y-m-d H:i:s');
$status_initial = 2; // Đang chờ xử lý

// --- KHỞI TẠO BIẾN TÍNH TOÁN ---
$sub_total = 0; // Tổng giá trị sản phẩm (Chưa bao gồm phí vận chuyển)
$cart_items_with_price = []; 

try {
    // --- BƯỚC 1: TÍNH TOÁN TỔNG GIÁ TRỊ TỪ GIỎ HÀNG (Sub Total) ---
    foreach ($cart as $vehicle_id => $item) {
        $qty = $item['qty'];
        
        // Lấy giá xe
        $sql_price = "SELECT price FROM vehicle WHERE vehicle_id = ?";
        $stmt_price = $conn->prepare($sql_price);
        $stmt_price->bind_param("i", $vehicle_id);
        $stmt_price->execute();
        $result_price = $stmt_price->get_result();
        $row_price = $result_price->fetch_assoc();
        
        if (!$row_price) {
            throw new Exception("Error: Vehicle ID #$vehicle_id does not exist");
        }
        
        $price = $row_price['price'];
        $amount_item = $price * $qty;
        
        $sub_total += $amount_item; 
        
        // Lưu lại thông tin sản phẩm để INSERT ở bước 3
        $cart_items_with_price[] = [
            'vehicle_id' => $vehicle_id,
            'amount' => $amount_item,
            'quantity' => $qty,
        ];
        $stmt_price->close(); 
    }
    
    // --- BƯỚC 2: TÍNH TOÁN PHÍ VẬN CHUYỂN VÀ TỔNG CỘNG CUỐI CÙNG ---
    
    // Đọc trực tiếp giá trị phí vận chuyển từ trường ẩn 'shipping_cost' trong form HTML
    // Nếu chọn Express ($15) thì $_POST['shipping_cost'] sẽ là '15'
    $shipping_fee = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 0.00;
    
    // Tính tổng tiền cuối cùng bao gồm phí vận chuyển
    $final_total = $sub_total + $shipping_fee; 

    // --- BƯỚC 3: TẠO ĐƠN HÀNG MỚI (ORDERS) VỚI TOTAL_AMOUNT & SHIPPING_FEE ---
    $sql_order = "INSERT INTO orders (customer_id, total_amount, shipping_fee, status, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    
    // Tham số: i (customer_id), d (final_total), d (shipping_fee), i (status), s (created_at)
    $stmt_order->bind_param("iddis", $customer_id, $final_total, $shipping_fee, $status_initial, $current_time); 
    
    if (!$stmt_order->execute()) {
        throw new Exception("Error when creating orders: " . $stmt_order->error);
    }
    $order_id = $stmt_order->insert_id;
    $stmt_order->close();

    // --- BƯỚC 4: THÊM CHI TIẾT ĐƠN HÀNG (ORDER_DETAIL) ---

    $payment_method = $_POST['payment_method'] ?? "Payment upon delivery"; 

    $sql_detail = "INSERT INTO order_detail (customer_id, vehicle_id, order_id, amount, quantity, payment_method, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);
    
    foreach ($cart_items_with_price as $item) {

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
    
    // 5. CAM KẾT VÀ XÓA GIỎ HÀNG
    $conn->commit();
    unset($_SESSION['cart']);

    // Chuyển hướng thành công
    header("Location: base.php?page=order");
    exit();

} catch (Exception $e) {
    // Xử lý lỗi: Hoàn tác Transaction
    $conn->rollback();
    
    // Hiển thị lỗi
    die("TRANSACTION ERROR: " . $e->getMessage() . "<br>Please try again.");
}

?>
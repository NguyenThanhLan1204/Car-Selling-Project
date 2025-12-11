<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php'; 

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// 2. Lấy Order ID từ URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    // Chuyển hướng nếu không có ID
    header("Location: base.php?page=order"); 
    exit;
}

// --- HÀM HỖ TRỢ HIỂN THỊ STATUS ---
function getStatusText($status) {
    // Dùng mảng cố định để dễ quản lý hơn switch/case dài
    $statuses = [
        2 => ["Booked", "badge bg-primary"],
        3 => ["Delivering", "badge bg-info"],
        4 => ["Success", "badge bg-success"],
    ];
    return $statuses[$status] ?? ["Not determined", "badge bg-dark"];
}

// --- 3. TRUY VẤN TỔNG QUAN ĐƠN HÀNG VÀ BẢO MẬT (ĐÃ THÊM shipping_fee) ---
$sql_order = "SELECT total_amount, status, created_at, shipping_fee 
              FROM orders 
              WHERE order_id = ? AND customer_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("ii", $order_id, $customer_id);
$stmt_order->execute();
$order_info = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

// Kiểm tra: Nếu đơn hàng không tồn tại HOẶC KHÔNG thuộc về khách hàng này
if (!$order_info) {
    echo "<p class='text-center text-danger py-5'>The order does not exist, or you do not have permission to access it.</p>";
    exit;
}

// Lấy phí vận chuyển từ DB. Nếu không có (NULL), mặc định là 0.
$shipping_fee = $order_info['shipping_fee'] ?? 0;


// --- 4. TRUY VẤN CHI TIẾT SẢN PHẨM TRONG ĐƠN HÀNG ---
$sql_details = "SELECT od.quantity, od.amount, od.payment_method, 
                        v.model, v.image_url, m.name AS manufacturer
                FROM order_detail od
                JOIN vehicle v ON od.vehicle_id = v.vehicle_id
                JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
                WHERE od.order_id = ?";
$stmt_details = $conn->prepare($sql_details);
$stmt_details->bind_param("i", $order_id);
$stmt_details->execute();
$details_result = $stmt_details->get_result();

// Lấy phương thức thanh toán từ dòng đầu tiên
$first_item = $details_result->fetch_assoc();
$payment_method = $first_item['payment_method'] ?? 'N/A';

// --- Bổ sung: Tính toán Tổng giá trị sản phẩm ---
$total_products_value = 0;
if ($details_result->num_rows > 0) {
    $details_result->data_seek(0); // Đưa con trỏ về đầu để tính toán
    while($item = $details_result->fetch_assoc()) {
        // $item['amount'] là tổng giá trị của một dòng chi tiết (price * quantity)
        $total_products_value += $item['amount']; 
    }
    // Đưa con trỏ về đầu lần nữa để lặp lại cho phần hiển thị HTML
    $details_result->data_seek(0); 
}

// Tính Tổng cộng cuối cùng
$grand_total = $total_products_value + $shipping_fee;

// Dùng Grand Total để hiển thị, nếu total_amount trong DB bằng 0 (lỗi) thì dùng Grand Total tự tính
$display_final_total = ($order_info['total_amount'] > 0) ? $order_info['total_amount'] : $grand_total;


?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="fw-bold mb-4">Order Details #<?= $order_id ?></h2>
            <a href="base.php?page=order" class="btn btn-sm btn-outline-secondary mb-4"><i class='bx bx-arrow-back'></i> Return to Order List</a>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <?php list($statusText, $badgeClass) = getStatusText($order_info['status']); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date order:</strong> <?= date("d/m/Y H:i:s", strtotime($order_info['created_at'])) ?></p>
                            <p><strong>Status:</strong> <span class="<?= $badgeClass ?>"><?= $statusText ?></span></p>
                            <p><strong>Total Value:</strong> <span class="text-danger fs-5">$<?= number_format($display_final_total, 0, ',', '.') ?></span></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            
                            <p><strong>Payment methods:</strong> <?= htmlspecialchars($payment_method) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-3">Purchased Products (<?= $details_result->num_rows ?> items)</h4>
            <?php 
            if ($details_result->num_rows > 0) {
                // Đưa con trỏ về đầu lần nữa trước khi hiển thị
                $details_result->data_seek(0); 
                while($item = $details_result->fetch_assoc()) {
            ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded object-fit-cover" alt="<?= htmlspecialchars($item['model']) ?>" style="height: 80px; width: 100%;">
                            </div>
                            <div class="col-md-5">
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($item['model']) ?> - <?= htmlspecialchars($item['manufacturer']) ?></h6>
                            </div>
                            <div class="col-md-5 text-end">
                                <p class="mb-0">Quantity: <strong><?= number_format($item['quantity']) ?></strong></p>
                                <p class="mb-0">Total Price: <strong class="text-primary">$<?= number_format($item['amount'], 0, ',', '.') ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                } 
            ?>

            <div class="card shadow-sm mt-5">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Products Value:</span>
                        <span class="fw-bold">$<?= number_format($total_products_value, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping/Other Fees:</span>
                        <span class="fw-bold text-success">$<?= number_format($shipping_fee, 0, ',', '.') ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-4">
                        <span class="fw-bold text-danger">GRAND TOTAL:</span>
                        <span class="fw-bold text-danger">$<?= number_format($display_final_total, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
            
            <?php
            } else {
                echo '<div class="alert alert-warning">There are no products in this order.</div>';
            }
            ?>
            
        </div>
    </div>
</div>

<?php
$stmt_details->close();
$conn->close();
?>
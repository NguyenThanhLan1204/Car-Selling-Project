<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php'; 
// 1. Check login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}
$customer_id = $_SESSION['customer_id'];

// 2. Get Order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    header("Location: base.php?page=order"); 
    exit;
}

// --- FUNCTION TO SUPPORT DISPLAYING STATUS ---
function getStatusText($status) {
    $statuses = [
        2 => ["Booked", "badge bg-primary"],
        3 => ["Delivering", "badge bg-info"],
        4 => ["Success", "badge bg-success"],
    ];
    return $statuses[$status] ?? ["Not determined", "badge bg-dark"];
}

// --- 3. ORDER OVERVIEW QUERY
$sql_order = "SELECT o.total_amount, o.status, o.created_at, o.shipping_fee, pm.name as payment_method_name
              FROM orders o
              LEFT JOIN payment_methods pm ON o.payment_method_id = pm.payment_method_id
              WHERE o.order_id = ? AND o.customer_id = ?";
              
$stmt_order = $conn->prepare($sql_order);

if ($stmt_order === false) {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}

$stmt_order->bind_param("ii", $order_id, $customer_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order_info = $result_order->fetch_assoc();
$stmt_order->close();

// Check: If the order does not exist OR does not belong to this customer
if (!$order_info) {
    echo "<p class='text-center text-danger py-5'>The order does not exist, or you do not have permission to access it.</p>";
    exit;
}

// Get shipping fee
$shipping_fee = $order_info['shipping_fee'] ?? 0;
$payment_method = $order_info['payment_method_name'] ?? 'N/A';


// --- 4. QUERY FOR PRODUCT DETAILS
$sql_details = "SELECT od.quantity, od.amount, 
                        v.model, v.image_url, m.name AS manufacturer
                FROM order_detail od
                JOIN vehicle v ON od.vehicle_id = v.vehicle_id
                JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
                WHERE od.order_id = ?";
                
$stmt_details = $conn->prepare($sql_details);

if ($stmt_details === false) {
    die("Detailed query preparation error: " . $conn->error);
}

$stmt_details->bind_param("i", $order_id);
$stmt_details->execute();
$details_result = $stmt_details->get_result();

$total_products_value = 0;
$details_data = [];

if ($details_result->num_rows > 0) {
    while($item = $details_result->fetch_assoc()) {
        $details_data[] = $item;
        $total_products_value += $item['amount']; 
    }
}

// Calculate the grand total
$grand_total = $order_info['total_amount'];

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
                            
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p><strong>Payment methods:</strong> <?= htmlspecialchars($payment_method) ?></p>
                            <p><strong>Total Value:</strong> <span class="text-danger fs-5">$<?= number_format($grand_total, 0, ',', '.') ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-3">Purchased Products (<?= count($details_data) ?> items)</h4>
            <?php 
            if (!empty($details_data)) {
                foreach($details_data as $item) {
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
                        <span>Total Products Value (Subtotal):</span>
                        <span class="fw-bold">$<?= number_format($total_products_value, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping/Other Fees:</span>
                        <span class="fw-bold text-success">$<?= number_format($shipping_fee, 0, ',', '.') ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-4">
                        <span class="fw-bold text-danger">GRAND TOTAL:</span>
                        <span class="fw-bold text-danger">$<?= number_format($grand_total, 0, ',', '.') ?></span>
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
if (isset($stmt_details) && $stmt_details) $stmt_details->close();
?>
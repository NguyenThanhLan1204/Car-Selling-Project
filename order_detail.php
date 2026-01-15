<?php
// --- KHỞI TẠO VÀ KIỂM TRA ĐĂNG NHẬP ---

// Kiểm tra session đã bật chưa, nếu chưa thì bật lên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Kết nối database
require_once 'db.php'; 

// 1. Kiểm tra quyền truy cập 
// Nếu chưa đăng nhập, đá về trang login ngay lập tức
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}
// Lấy ID khách hàng từ session
$customer_id = $_SESSION['customer_id'];

// 2: NHẬN VÀ KIỂM TRA ID ĐƠN HÀNG ---

// Lấy Order ID từ thanh địa chỉ (URL)
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Nếu ID = 0 (không hợp lệ), chuyển hướng về danh sách đơn hàng
if ($order_id === 0) {
    header("Location: base.php?page=order"); 
    exit;
}

// --- CHUYỂN MÃ SỐ TRẠNG THÁI THÀNH CHỮ VÀ MÀU SẮC ---
function getStatusText($status) {
    switch ($status) {
        case 1: return ["Cancel Pending", "bg-warning"]; 
        case 2: return ["Booked", "bg-primary"];         
        case 3: return ["Testing", "bg-info"];           
        case 4: return ["Success", "bg-success"];        
        case 5: return ["Cancelled", "bg-secondary"];    
        default: return ["Not Determine", "bg-dark"];    
    }
}

// --- 3: TRUY VẤN THÔNG TIN TỔNG QUAN CỦA ĐƠN HÀNG ---
// Lấy ngày đặt, tổng tiền, tiền cọc, lịch hẹn lái thử
$sql_order = "SELECT 
                o.total_amount, 
                o.status, 
                o.created_at, 
                o.deposit,
                o.test_drive_date,
                o.test_drive_time
              FROM orders o
              WHERE o.order_id = ? AND o.customer_id = ?"; 
              
            
$stmt_order = $conn->prepare($sql_order);

if ($stmt_order === false) {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}

// Gán tham số: i (integer) cho order_id, i (integer) cho customer_id
$stmt_order->bind_param("ii", $order_id, $customer_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order_info = $result_order->fetch_assoc();
$stmt_order->close();

// Kiểm tra: Nếu không tìm thấy đơn hàng (hoặc đơn hàng không thuộc về khách này)
if (!$order_info) {
    echo "<p class='text-center text-danger py-5'>The order does not exist, or you do not have permission to access it.</p>";
    exit; 
}

// Lấy các thông tin cần thiết ra biến để dễ sử dụng bên dưới
// Toán tử ?? 0 nghĩa là nếu không có giá trị (null) thì mặc định là 0
$deposit = $order_info['deposit'] ?? 0;
$test_drive_date = $order_info['test_drive_date'];
$test_drive_time = $order_info['test_drive_time'];

// --- 4: TRUY VẤN CHI TIẾT SẢN PHẨM TRONG ĐƠN HÀNG ---
// Lấy danh sách các xe đã đặt trong đơn này 
$sql_details = "SELECT od.quantity, od.amount, 
                       v.model, v.image_url, m.name AS manufacturer
                FROM order_detail od
                JOIN vehicle v ON od.vehicle_id = v.vehicle_id            
                JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id 
                WHERE od.order_id = ?"; // Chỉ lấy của đơn hàng hiện tại
                
$stmt_details = $conn->prepare($sql_details);

if ($stmt_details === false) {
    die("Detailed query preparation error: " . $conn->error);
}

$stmt_details->bind_param("i", $order_id);
$stmt_details->execute();
$details_result = $stmt_details->get_result();

// Biến tính tổng giá trị thực tế của các món hàng (để so sánh hoặc hiển thị)
$total_products_value = 0;
$details_data = []; // Mảng chứa dữ liệu để duyệt loop trong HTML

if ($details_result->num_rows > 0) {
    while($item = $details_result->fetch_assoc()) {
        $details_data[] = $item;
        $total_products_value += $item['amount']; // Cộng dồn thành tiền
    }
}

// Lấy tổng tiền đơn hàng từ bảng orders
$grand_total = $order_info['total_amount'];

?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="fw-bold mb-4">Booking Details #<?= $order_id ?></h2>
            <a href="base.php?page=order" class="btn btn-sm btn-outline-secondary mb-4"><i class='bx bx-arrow-back'></i> Return to Booking List</a>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Booking Information</h5>
                </div>
                <div class="card-body">
                    <?php list($statusText, $badgeClass) = getStatusText($order_info['status']); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date book:</strong> <?= date("d/m/Y H:i:s", strtotime($order_info['created_at'])) ?></p>
                            <p><strong>Status:</strong> <span class="badge <?= $badgeClass ?> fs-6"><?= $statusText ?></span> </p>
                            
                            <?php if (!empty($test_drive_date) && !empty($test_drive_time)): ?>
                                <p>
                                    <strong>Test drive schedule:</strong>
                                    <?= date("d/m/Y H:i:s", strtotime($test_drive_date . ' ' . $test_drive_time)) ?>
                                </p>
                            <?php endif; ?>
  
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-3">Purchased Products (<?= count($details_data) ?> items)</h4>
            <?php 
            if (!empty($details_data)) {
                // Duyệt qua từng sản phẩm trong mảng đã lấy từ database
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
            <?php   }  ?>

            <div class="card shadow-sm mt-5">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Products Value:</span>
                        <span class="fw-bold">$<?= number_format($total_products_value, 0, ',', '.') ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-4">
                        <span class="fw-bold text-danger">TOTAL DEPOSIT (10%):</span>
                        <span class="fw-bold text-danger">$<?= number_format($deposit, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
            
            <?php
            } else {
                // Có đơn hàng nhưng không có chi tiết sản phẩm
                echo '<div class="alert alert-warning">There are no products in this order.</div>';
            }
            ?>   
        </div>
    </div>
</div>

<?php
// Đóng statement nếu nó đã được khởi tạo
if (isset($stmt_details) && $stmt_details) $stmt_details->close();
?>
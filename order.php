<?php 
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}
?>

<?php
// Kết nối DB
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "car_selling";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['customer_id'])) {
    echo "<p class='text-center text-danger'>Bạn cần đăng nhập để xem đơn hàng.</p>";
    exit;
}

$customer_id = $_SESSION['customer_id'];

$statusFilter = isset($_GET['status']) ? intval($_GET['status']) : 0;

// Lấy dữ liệu đơn hàng chi tiết
$sql = "SELECT od.order_detail_id, od.order_id, od.amount, od.quantity, od.payment_method, od.status,
               o.created_at, v.model, v.image_url, m.name AS manufacturer
        FROM order_detail od
        JOIN orders o ON od.order_id = o.order_id
        JOIN vehicle v ON od.vehicle_id = v.vehicle_id
        JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
        WHERE od.customer_id = ?";

if ($statusFilter > 0) {
    $sql .= " AND od.status = ?";
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
if ($statusFilter > 0) {
    $stmt->bind_param("ii", $customer_id, $statusFilter);
} else {
    $stmt->bind_param("i", $customer_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Đơn hàng của bạn</h2>
    <div class="row g-4">
        <div class="col-3">
            <div class="p-4 shadow-sm bg-white rounded position-fixed">
                <h5 class="fw-bold mb-3">Bộ lọc Trạng thái</h5>        
                <div class="list-group">       
                    <?php $status = $_GET['status'] ?? ''; ?>       
                    <a href="base.php?page=order" 
                       class="list-group-item list-group-item-action <?= $status == '' ? 'active' : '' ?>">
                       Tất cả Đơn hàng
                    </a>       
                    <a href="base.php?page=order&status=2" 
                       class="list-group-item list-group-item-action <?= $status == 2 ? 'active' : '' ?>">
                       Đang chờ xử lý
                    </a>       
                    <a href="base.php?page=order&status=3" 
                       class="list-group-item list-group-item-action <?= $status == 3 ? 'active' : '' ?>">
                       Đang giao hàng
                    </a>      
                    <a href="base.php?page=order&status=4" 
                       class="list-group-item list-group-item-action <?= $status == 4 ? 'active' : '' ?>">
                       Đã hoàn thành
                    </a>       
                </div>
            </div>
        </div>  
        <!-- Danh sách đơn hàng -->
        <div class="col-9" style="min-height: 400px;">
            <!-- Đơn hàng 1 -->
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Xử lý trạng thái
                    $statusText = "";
                    $badgeClass = "";
                    switch ($row['status']) {
                        case 2: $statusText = "Đang chờ xử lý"; $badgeClass = "bg-secondary"; break;
                        case 3: $statusText = "Đang giao hàng"; $badgeClass = "bg-warning text-dark"; break;
                        case 4: $statusText = "Đã thanh toán"; $badgeClass = "bg-success"; break;
                        default: $statusText = "Không xác định"; $badgeClass = "bg-dark"; break;
                    }

                    echo '<div class="card mb-4 shadow-sm">';
                    echo '  <div class="row align-items-center m-3">';
                    echo '    <div class="col-md-3">';
                    echo '      <img src="'.$row['image_url'].'" class="img-fluid rounded-start" alt="'.$row['model'].'">';
                    echo '    </div>';
                    echo '    <div class="col-md-7">';
                    echo '      <h5 class="fw-bold mb-2">'.$row['model'].' - '.$row['manufacturer'].'</h5>';
                    echo '      <p class="mb-1">Mã Đơn hàng: <strong>#'.$row['order_id'].'</strong></p>';
                    echo '      <p class="mb-1">Ngày: '.date("d.m.Y", strtotime($row['created_at'])).'</p>';
                    echo '      <p class="mb-1">Giá: <strong>'.number_format($row['amount'],0,',','.').' VND</strong></p>';
                    echo '      <span class="badge '.$badgeClass.'">'.$statusText.'</span>';
                    echo '    </div>';
                    // echo '    <div class="col-md-2">';
                    // echo '      <a href="order_detail.php?id='.$row['order_detail_id'].'" class="btn btn-success text-nowrap d-flex justify-content-center align-items-center">';
                    // echo '        Xem chi tiết';
                    // echo '      </a>';
                    // echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="d-flex justify-content-center align-items-center">';
                echo '   <div class="text-center text-muted">';
                echo '       <i class="bi bi-box-seam" style="font-size: 3rem;"></i>';
                echo '        <p class="mt-3">Bạn chưa có đơn hàng nào.</p>';
                echo '    </div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

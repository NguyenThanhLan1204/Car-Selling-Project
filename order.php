<?php 
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}
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

$customer_id = $_SESSION['customer_id'];

// Lấy filter status từ URL (Sử dụng 0 cho "Tất cả")
$statusFilter = isset($_GET['status']) ? intval($_GET['status']) : 0;

// --- HÀM HỖ TRỢ HIỂN THỊ STATUS ---
function getStatusText($status) {
    switch ($status) {
        // Đã đồng bộ lại màu sắc
        case 2: return ["Booked", "bg-primary"]; 
        case 3: return ["Delivering", "bg-info"]; 
        case 4: return ["Success", "bg-success"]; 
        default: return ["Not Determine", "bg-dark"];
    }
}


// --- TRUY VẤN DỮ LIỆU ĐƠN HÀNG TỔNG HỢP (ĐÃ SỬA) ---
$sql = "SELECT o.order_id, o.total_amount, o.status, o.created_at, 
               od_rep.model, od_rep.image_url, od_rep.manufacturer
        FROM orders o 
        -- Subquery để chọn một sản phẩm đại diện (cho hình ảnh/tên) cho mỗi order_id

        JOIN (
             SELECT 
                 od1.order_id, 
                 MIN(v.model) AS model,         -- Sử dụng hàm tổng hợp MIN()
                 MIN(v.image_url) AS image_url, -- Sử dụng hàm tổng hợp MIN()
                 MIN(m.name) AS manufacturer    -- Sử dụng hàm tổng hợp MIN()
             FROM order_detail od1
             JOIN vehicle v ON od1.vehicle_id = v.vehicle_id
             JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
             GROUP BY od1.order_id
        ) od_rep ON o.order_id = od_rep.order_id
        WHERE o.customer_id = ?"; // Lọc theo customer_id trong bảng orders (Tối ưu)

// Khởi tạo tham số và loại dữ liệu
$params = [$customer_id];
$types = "i";

// Thêm điều kiện lọc trạng thái nếu có ($statusFilter > 0)
if ($statusFilter > 0) {
    // lọc theo status của bảng orders để đảm bảo tính nhất quán của đơn hàng
    $sql .= " AND o.status = ?"; 
    $params[] = $statusFilter;
    $types .= "i";
}

$sql .= " ORDER BY o.created_at DESC";

// Sử dụng Prepared Statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation error: " . $conn->error);
}

$stmt->bind_param($types, ...$params); 

$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Your order</h2>
    <div class="row g-4">

            <div class="col-lg-3 col-md-4">
            <div class="p-4 shadow-sm bg-white rounded position-fixed" style="width: 250px;">           <h5 class="fw-bold mb-3">Status Filter</h5>
             <div class="list-group">
                 <a href="base.php?page=order" class="list-group-item list-group-item-action <?= ($statusFilter === 0) ? 'active' : '' ?>">
                     All 
                 </a>
                 <a href="base.php?page=order&status=2" class="list-group-item list-group-item-action <?= ($statusFilter === 2) ? 'active' : '' ?>">
                     Booked
                 </a>
                 <a href="base.php?page=order&status=3" class="list-group-item list-group-item-action <?= ($statusFilter === 3) ? 'active' : '' ?>">
                     Delivering
                 </a>
                 <a href="base.php?page=order&status=4" class="list-group-item list-group-item-action <?= ($statusFilter === 4) ? 'active' : '' ?>">
                      Success
                 </a>
                 </div>
             </div>
          </div>

             <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
             <?php 
             if ($result->num_rows > 0) {
                 while($row = $result->fetch_assoc()) { 
                     // Lấy status và class đã được chuẩn hóa
                     list($statusText, $badgeClass) = getStatusText($row['status']);
                     ?>
             <div class="card mb-4 shadow-sm">
             <div class="row align-items-center m-3">

               <div class="col-md-3">
                 <img src="<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid rounded-start object-fit-cover" alt="<?= htmlspecialchars($row['model']) ?>" style="max-height: 150px; width: 100%;">
               </div>

             <div class="col-md-7">
                 <h5 class="fw-bold mb-2"><?= htmlspecialchars($row['model']) ?> - <?= htmlspecialchars($row['manufacturer']) ?></h5>
                     <p class="mb-1">Order Code: <strong>#<?= htmlspecialchars($row['order_id']) ?></strong></p>
                     <p class="mb-1">Date order: <?= date("d.m.Y H:i:s", strtotime($row['created_at'])) ?></p>
                     
                     <p class="mb-1">Total Amount: <strong class="text-danger">$<?= number_format($row['total_amount'], 0, ',', '.') ?></strong></p>
                     
                     <span class="badge <?= $badgeClass ?> fs-6"><?= $statusText ?></span>
                 </div>
                   <div class="col-md-2 text-end">
                     <a 
                         href="base.php?page=order_detail&order_id=<?= htmlspecialchars($row['order_id']) ?>" class="btn btn-success text-nowrap w-100"> 
                              See details 
                     </a>
                   </div>
                   </div>
             </div>
               <?php 
          }
             } else {
               echo '<div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">';
               echo '    <div class="text-center text-muted">';
               echo '        <p class="mt-3 fs-5">You don`t have any orders yet.</p>';
               echo '    </div>';
               echo '</div>';
             }
             ?>
             </div>
    </div>
</div>
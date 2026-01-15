<?php 
// --- 1: KIỂM TRA ĐĂNG NHẬP ---
// Nếu chưa có session customer_id (chưa đăng nhập), chuyển hướng về trang login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}
// Kết nối database
include("db.php");

// Lấy ID khách hàng từ session hiện tại
$customer_id = $_SESSION['customer_id'];

// --- 2: XỬ LÝ BỘ LỌC TRẠNG THÁI  ---
// Lấy tham số 'status' từ URL (ví dụ: ?status=2). Nếu không có thì mặc định là 0
// intval() giúp ép kiểu về số nguyên để bảo mật.
$statusFilter = isset($_GET['status']) ? intval($_GET['status']) : 0;

// --- 3 HIỂN THỊ TRẠNG THÁI ---
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

// ---  4: XÂY DỰNG CÂU TRUY VẤN SQL  ---
// Lấy thông tin đơn hàng và thông tin của 1 xe đại diện trong đơn đó để hiển thị ảnh.
$sql = "SELECT o.order_id, o.total_amount, o.status, o.created_at, 
               od_rep.model, od_rep.image_url, od_rep.manufacturer
        FROM orders o 
        JOIN (
             SELECT 
                 od1.order_id, 
                 MIN(v.model) AS model,         
                 MIN(v.image_url) AS image_url, 
                 MIN(m.name) AS manufacturer   
             FROM order_detail od1
             JOIN vehicle v ON od1.vehicle_id = v.vehicle_id
             JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
             GROUP BY od1.order_id
        ) od_rep ON o.order_id = od_rep.order_id
        WHERE o.customer_id = ?"; // Điều kiện 1: Phải là đơn của khách đang đăng nhập

// --- 5: XỬ LÝ THAM SỐ ĐỘNG CHO SQL ---
// Khởi tạo mảng tham số và chuỗi kiểu dữ liệu cho bind_param
$params = [$customer_id];
$types = "i"; 

// Nếu người dùng chọn lọc theo trạng thái (status > 0)
if ($statusFilter > 0) {
    // Nối thêm điều kiện vào câu SQL
    $sql .= " AND o.status = ?"; 
    // Thêm giá trị vào mảng tham số
    $params[] = $statusFilter;
    // Thêm kiểu dữ liệu vào chuỗi types
    $types .= "i";
}

// Sắp xếp đơn mới nhất lên đầu
$sql .= " ORDER BY o.created_at DESC";

// Sử dụng Prepared Statement để chống SQL Injection
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation error: " . $conn->error);
}


$stmt->bind_param($types, ...$params); 

$stmt->execute();
// Lấy kết quả trả về
$result = $stmt->get_result();

?>

<body class="bg-light">
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Your booking</h2>
    <div class="row g-4">

            <div class="col-lg-3 col-md-4">
                <div class="p-4 shadow-sm bg-white rounded" style="width: 250px;"> <h5 class="fw-bold mb-3">Status Filter</h5>
                <div class="list-group">
                    <a href="base.php?page=order" class="list-group-item list-group-item-action <?= ($statusFilter === 0) ? 'active' : '' ?>">
                        All 
                    </a>
                    <a href="base.php?page=order&status=1" class="list-group-item list-group-item-action <?= ($statusFilter === 1) ? 'active' : '' ?>">
                         Cancel Pending
                    </a>
                    <a href="base.php?page=order&status=2" class="list-group-item list-group-item-action <?= ($statusFilter === 2) ? 'active' : '' ?>">
                        Booked
                    </a>
                    <a href="base.php?page=order&status=3" class="list-group-item list-group-item-action <?= ($statusFilter === 3) ? 'active' : '' ?>">
                        Testing
                    </a>
                    <a href="base.php?page=order&status=4" class="list-group-item list-group-item-action <?= ($statusFilter === 4) ? 'active' : '' ?>">
                        Success
                    </a>
                     <a href="base.php?page=order&status=5" class="list-group-item list-group-item-action <?= ($statusFilter === 5) ? 'active' : '' ?>">
                         Cancelled
                     </a>
                </div>
                </div>
          </div>

            <div class="col-lg-9 col-md-8 ">
            <?php 
            // Nếu có đơn hàng trả về
            if ($result->num_rows > 0) {
                // Vòng lặp duyệt qua từng dòng dữ liệu
                while($row = $result->fetch_assoc()) { 
                    // Gọi hàm helper để lấy text và class màu sắc
                    list($statusText, $badgeClass) = getStatusText($row['status']);
                    ?>
                    
            <div class="card mb-4 shadow-sm">
            <div class="row align-items-center m-3">
              <div class="col-md-3">
                <img src="<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid rounded-start object-fit-cover" alt="<?= htmlspecialchars($row['model']) ?>" style="max-height: 150px; width: 100%;">
              </div>
            <div class="col-md-7">
                <h5 class="fw-bold mb-2"><?= htmlspecialchars($row['model']) ?> - <?= htmlspecialchars($row['manufacturer']) ?></h5>
                    <p class="mb-1">Book Code: <strong>#<?= htmlspecialchars($row['order_id']) ?></strong></p>
                    <p class="mb-1">Date book: <?= date("d.m.Y H:i:s", strtotime($row['created_at'])) ?></p>
                    
                    <p class="mb-1">Total Amount: <strong class="text-danger">$<?= number_format($row['total_amount'], 0, ',', '.') ?></strong></p>
                    
                    <span class="badge <?= $badgeClass ?> fs-6"><?= $statusText ?></span>
                </div>
                  
                  <div class="col-md-2 text-end">
                    <a 
                        href="base.php?page=order_detail&order_id=<?= htmlspecialchars($row['order_id']) ?>" class="btn btn-success text-nowrap w-100"> 
                             See details 
                    </a>
                  </div>
                  
                 <?php if ($row['status'] == 2): ?>

                    <div class="col-md-2 text-end">
                        <button class="btn btn-danger text-nowrap w-100 mt-2"
                                data-bs-toggle="modal"
                                data-bs-target="#cancelModal<?= $row['order_id'] ?>">
                            Cancel Booking
                        </button>
                    </div>

                    <div class="modal fade" id="cancelModal<?= $row['order_id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Cancel Booking</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to cancel booking #<?= htmlspecialchars($row['order_id']) ?>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                    <a href="cancel_order.php?order_id=<?= htmlspecialchars($row['order_id']) ?>"
                                    class="btn btn-danger">
                                        Yes, Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                  </div>
            </div>
              <?php 
          } // Kết thúc vòng lặp while
             } else {
               // Trường hợp không tìm thấy đơn hàng nào
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
</body>
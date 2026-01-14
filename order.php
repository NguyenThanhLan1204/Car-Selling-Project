<?php 
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?message=please_login");
    exit();
}
include("db.php");

$customer_id = $_SESSION['customer_id'];

// Get filter status from URL (Use 0 for "All")
$statusFilter = isset($_GET['status']) ? intval($_GET['status']) : 0;

// --- FUNCTION TO SUPPORT DISPLAYING STATUS ---
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
// --- QUERY OF GENERAL ORDER DATA ---
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
        WHERE o.customer_id = ?"; // Filter by customer_id in the orders table

// Initialize parameters and data types
$params = [$customer_id];
$types = "i";

// Add state filtering conditions
if ($statusFilter > 0) {
    // Filter by order table status
    $sql .= " AND o.status = ?"; 
    $params[] = $statusFilter;
    $types .= "i";
}

$sql .= " ORDER BY o.created_at DESC";

// Use Prepared Statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation error: " . $conn->error);
}

$stmt->bind_param($types, ...$params); 

$stmt->execute();
$result = $stmt->get_result();

?>
<body class="bg-light">
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-center">Your booking</h2>
    <div class="row g-4">

            <div class="col-lg-3 col-md-4">
            <div class="p-4 shadow-sm bg-white rounded position-fixed" style="width: 250px;">           <h5 class="fw-bold mb-3">Status Filter</h5>
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

            <div class="col-lg-9 col-md-8 offset-lg-3 offset-md-4">
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { 
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

                    <!-- Cancel Modal -->
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
</body>
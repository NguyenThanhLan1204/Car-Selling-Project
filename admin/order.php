<?php
include("dbconn.php");

// =========================================
// XỬ LÝ UPDATE STATUS ĐƠN HÀNG
// =========================================
if (isset($_GET['order']) && isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    $newStatus = (int)$_GET['order'];

    $res = mysqli_query($link, "SELECT status FROM orders WHERE order_id = $order_id"); 
    $row = mysqli_fetch_assoc($res); 
    $oldStatus = (int)$row['status']; 
    // Nếu đơn hàng đang Cancel Pending (1) 
    if ($oldStatus === 1) { 
        // Nếu admin bấm Approve (-> Cancelled = 5) 
        if ($newStatus === 5) { 
            // Lấy chi tiết sản phẩm trong đơn hàng 
            $sqlDetail = "SELECT vehicle_id, quantity FROM order_detail WHERE order_id = $order_id"; 
            $details = mysqli_query($link, $sqlDetail); 
            
            while ($item = mysqli_fetch_assoc($details)) { 
                $vid = (int)$item['vehicle_id']; 
                $qty = (int)$item['quantity']; 
                // Cộng lại số lượng xe vào stock 
                mysqli_query($link, "UPDATE vehicle SET stock = stock + $qty WHERE vehicle_id = $vid"); 
            } 
        } // Nếu admin bấm Reject (-> Booked = 2) thì không làm gì thêm 
    }

    mysqli_query($link, "UPDATE orders SET status = $newStatus WHERE order_id = $order_id");


    header("Location: order.php?msg=updated");
    exit();
}

}

// =========================================
// LẤY FILTER STATUS
// =========================================
$type = isset($_GET['type']) ? (int)$_GET['type'] : -1;

// =========================================
// LẤY DANH SÁCH ORDER (ưu tiên shipping_*)
// =========================================
$sql = "
    SELECT 
        o.order_id,
        o.status,
        o.test_drive_date,o.test_drive_time,
        o.deposit,
        c.email,
        COALESCE(o.shipping_name, c.name) AS display_name,
        COALESCE(o.shipping_phone, c.phone_number) AS display_phone,
        COALESCE(o.shipping_address, c.address) AS display_address,
        SUM(od.quantity) AS quantity,
        MIN(v.model) AS vehicle_model
    FROM orders o
    JOIN customer c ON o.customer_id = c.customer_id
    LEFT JOIN order_detail od ON o.order_id = od.order_id
    LEFT JOIN vehicle v ON od.vehicle_id = v.vehicle_id
";

if ($type != -1) {
    $sql .= " WHERE o.status = $type ";
}

$sql .= " GROUP BY o.order_id ORDER BY o.created_at DESC";

$orders = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Management</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/admin_order.css">
</head>
<body>
<div class="layout">
    <?php include("header.php"); ?>
<div class="container-fluid">
<div class="row">
<div class="col-12">

<div class="card my-4">
    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">Order table</h6>

            <!-- FILTER -->
            <a href="order.php" class="filter-btn all <?= $type == -1 ? 'active' : '' ?>">All</a>
            <a href="order.php?type=1" class="filter-btn warning <?= $type == 1 ? 'active' : '' ?>">Cancel Pending</a>
            <a href="order.php?type=2" class="filter-btn primary <?= $type == 2 ? 'active' : '' ?>">Booked</a>
            <a href="order.php?type=3" class="filter-btn info <?= $type == 3 ? 'active' : '' ?>">Testing</a>
            <a href="order.php?type=4" class="filter-btn success <?= $type == 4 ? 'active' : '' ?>">Success</a>
            <a href="order.php?type=5" class="filter-btn secondary <?= $type == 5 ? 'active' : '' ?>">Cancelled</a>

        </div>
    </div>

    <div class="card-body px-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0 mt-3">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Deposit</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Customer Appointments</th>
                </tr>
                </thead>
                <tbody>

                <?php while ($order = mysqli_fetch_assoc($orders)) { ?>
                <tr>
                    <td>#<?= $order['order_id'] ?></td>
                    <td>
                        <b><?= htmlspecialchars($order['display_name']) ?></b><br>
                        <small><?= htmlspecialchars($order['email']) ?></small>
                    </td>
                    <td>
                        <a href="order-detail.php?order_id=<?= $order['order_id'] ?>">View now</a><br>
                        Qty: <?= $order['quantity'] ?>
                    </td>
                    <td><?= nl2br(htmlspecialchars($order['display_address'])) ?></td>
                    <td><?= htmlspecialchars($order['display_phone']) ?></td>
                    <td class="text-danger fw-bold"> $<?= number_format($order['deposit'], 0, ',', '.') ?>
                    </td>

                    <td class="text-center" style="min-width: 150px;">
                    <?php
                    switch ($order['status']) {
                        case 1:
                            echo '<span class="badge bg-warning text-dark">Cancel Pending</span>';
                            break;
                        case 2:
                            echo '<span class="badge bg-primary">Booked</span>';
                            break;
                        case 3:
                            echo '<span class="badge bg-info">Testing</span>';
                            break;
                        case 4:
                            echo '<span class="badge bg-success">Success</span>';
                            break;
                        case 5:
                            echo '<span class="badge bg-danger">Cancelled</span>';
                            break;
                        default:
                            echo '<span class="badge bg-dark">Unknown</span>';
                    }
                    ?>
                    </td>

                    <td class="text-center">
                        <?php if (!empty($order['test_drive_date']) && !empty($order['test_drive_time'])): ?>
                            <?= date(
                                'd-m-Y H:i',
                                strtotime($order['test_drive_date'] . ' ' . $order['test_drive_time'])
                            ) ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>

                </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
</div>

</div>
</body>
</html>
<?php
include("dbconn.php");

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    die("Invalid order id!");
}

// Lấy thông tin đơn hàng + ưu tiên shipping_*
$sqlOrder = "
    SELECT 
        o.order_id, 
        o.status, 
        o.test_drive_date,o.test_drive_time,
        o.shipping_name,
        o.shipping_phone,
        o.shipping_address,
        COALESCE(o.shipping_name, c.name) AS display_name,
        COALESCE(o.shipping_phone, c.phone_number) AS display_phone,
        COALESCE(o.shipping_address, c.address) AS display_address,
        c.email
    FROM orders o
    JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_id = $order_id
";

$orderInfo = mysqli_query($link, $sqlOrder);
if (!$orderInfo || mysqli_num_rows($orderInfo) == 0) {
    die("Order not found!");
}
$first = mysqli_fetch_assoc($orderInfo);

// Lấy chi tiết sản phẩm
$sqlDetail = "
    SELECT od.quantity, od.amount, od.created_at,
           v.model, v.image_url
    FROM order_detail od
    JOIN vehicle v ON od.vehicle_id = v.vehicle_id
    WHERE od.order_id = $order_id
";
$details = mysqli_query($link, $sqlDetail);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Detail #<?= $order_id ?></title>
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
                            <h6 class="text-white text-capitalize ps-3">Order Detail #<?= $order_id ?></h6>
                        </div>
                    </div>

                    <div class="card-body px-4">
                        <h2>Buyer Information</h2>
                        Buyer Name: <strong><?= htmlspecialchars($first['display_name']) ?></strong><br>
                        Phone: <strong><?= htmlspecialchars($first['display_phone']) ?></strong><br>
                        Email: <strong><?= htmlspecialchars($first['email']) ?></strong><br>
                        Address: <strong><?= nl2br(htmlspecialchars($first['display_address'])) ?></strong><br>
                        Test Drive Date: <strong><?= htmlspecialchars($first['test_drive_date']) ?></strong><br>
                        Test Drive Time: <strong><?= htmlspecialchars($first['test_drive_time']) ?></strong><br>
                        <strong>Status:</strong>
                        <?php
                        switch ($first['status']) {
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
                                echo '<span class="badge bg-secondary">Cancelled</span>';
                                break;

                            default:
                                echo '<span class="badge bg-dark">Unknown</span>';
                        }
                        ?>

                        <?php if ($first['status'] == 2 || $first['status'] == 3): ?>
                            <br><br><strong>Update to:</strong>
                            <?php if ($first['status'] == 2): ?>
                                <a href="order.php?order=3&order_id=<?= $order_id ?>" class="badge bg-info">Testing</a>
                                <a href="order.php?order=5&order_id=<?= $order_id ?>" class="badge bg-danger"> Cancelled </a>
                            <?php elseif ($first['status'] == 3): ?>
                                 <br><br><strong></strong>
                                <a href="order.php?order=4&order_id=<?= $order_id ?>" class="badge bg-success">Success</a>
                                <a href="order.php?order=5&order_id=<?= $order_id ?>" class="badge bg-danger"> Cancelled </a>
                            <?php endif; ?>
                            
                            <?php elseif ($first['status'] == 1): ?>
                                <br><br><strong>Approve or Reject:</strong>
                                <a href="order.php?order=5&order_id=<?= $order_id ?>" class="badge bg-success">Approve</a>
                                <a href="order.php?order=2&order_id=<?= $order_id ?>" class="badge bg-danger">Reject</a>
                            <?php endif; ?>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Info</th>
                                    <th>Order time</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($item = mysqli_fetch_assoc($details)) { ?>
                                <tr>
                                    <td>
                                        <img src="../<?= htmlspecialchars($item['image_url']) ?>" width="60" alt="vehicle"><br>
                                        <?= htmlspecialchars($item['model']) ?>
                                    </td>
                                    <td>
                                        Price: $<?= number_format($item['amount']) ?><br>
                                        Qty: <?= $item['quantity'] ?><br>
                                        Total: 
                                        <?php
                                        $tmp = $item['quantity'] * $item['amount'];
                                        $total += $tmp;
                                        echo "$" . number_format($tmp);
                                        ?>
                                    </td>
                                    <td><?= date('d-m-Y', strtotime($item['created_at'])) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <h3>Total: $<?= number_format($total) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
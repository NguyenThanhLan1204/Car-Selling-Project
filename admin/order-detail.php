<?php
include("dbconn.php");

// Lấy order_id từ URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    die("Invalid order id!");
}

// ===============================
// Lấy thông tin đơn hàng + khách hàng
// ===============================
$sqlOrder = "
    SELECT o.order_id, o.customer_id, o.status, o.created_at,
           c.name, c.phone_number, c.email, c.address
    FROM orders o
    JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_id = $order_id
";

$orderInfo = mysqli_query($link, $sqlOrder);

if (!$orderInfo || mysqli_num_rows($orderInfo) == 0) {
    die("Order not found!");
}

$first = mysqli_fetch_assoc($orderInfo);

// ===============================
// Lấy danh sách sản phẩm trong order_detail
// ===============================
$sqlDetail = "
    SELECT od.order_detail_id, od.vehicle_id, od.quantity, od.amount,
           od.payment_method, od.status, od.created_at,
           v.model, v.price, v.image_url
    FROM order_detail od
    JOIN vehicle v ON od.vehicle_id = v.vehicle_id
    WHERE od.order_id = $order_id
";

$details = mysqli_query($link, $sqlDetail);

// Tính tổng
$total = 0;
?>

<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/or.css"> 
</head>

<body>
<div class="layout">

    <!-- SIDEBAR -->
    <?php include("header.php"); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Order Detail</h6>
                        </div>
                    </div>

                    <div class="card-body px-4">

                        <h5>Buyer Information</h5>
                        Buyer Name: <?= $first['name'] ?><br>
                        Phone: <?= $first['phone_number'] ?><br>
                        Email: <?= $first['email'] ?><br>
                        Address: <?= $first['address'] ?><br>

                        <br>
                        <strong>Status:</strong>
                        <?php
                            if ($first['status'] == 2)
                                echo '<span class="badge bg-primary">Booked</span>';
                            else if ($first['status'] == 3)
                                echo '<span class="badge bg-info">Delivering</span>';
                            else if ($first['status'] == 4)
                                echo '<span class="badge bg-success">Success</span>';
                        ?>

                        <?php if ($first['status'] == 2 || $first['status'] == 3): ?>
                            <br><br><strong>Update to:</strong>
                            <?php
                                if ($first['status'] == 2) {
                                    echo "<a href='order.php?order=3&order_id=$order_id' class='badge bg-info'>Delivering</a>";
                                } else if ($first['status'] == 3) {
                                    echo "<a href='order.php?order=4&order_id=$order_id' class='badge bg-success'>Delivered</a>";
                                }
                            ?>
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
                                        <img src="../<?= $item['image_url'] ?>" width="60"><br>
                                        <?= $item['model'] ?>
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

                                    <td>
                                        <?= date('d-m-Y', strtotime($item['created_at'])) ?>
                                    </td>
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
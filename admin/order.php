<?php
include("header.php");
include("dbconn.php");

// =========================================
// XỬ LÝ UPDATE STATUS
// =========================================
if (isset($_GET['order']) && isset($_GET['order_id'])) {

    $order_id = (int) $_GET['order_id'];
    $newStatus = (int) $_GET['order'];

    mysqli_query($link, "UPDATE orders SET status=$newStatus WHERE order_id=$order_id");
    mysqli_query($link, "UPDATE order_detail SET status=$newStatus WHERE order_id=$order_id");

    header("Location: order.php?msg=updated");
    exit();
}

// =========================================
// FILTER TYPE
// =========================================
$type = isset($_GET['type']) ? (int)$_GET['type'] : -1;

// =========================================
// QUERY ORDERS
// =========================================
$sql = "
    SELECT 
        o.order_id,
        o.status,
        o.created_at,
        c.name,
        c.email,
        c.phone_number,
        c.address,
        SUM(od.quantity) AS quantity
    FROM orders o
    JOIN customer c ON o.customer_id = c.customer_id
    LEFT JOIN order_detail od ON o.order_id = od.order_id
";

if ($type != -1) {
    $sql .= " WHERE o.status = $type ";
}

$sql .= "
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
";

$orders = mysqli_query($link, $sql);

?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/order.css"> 
</head>

<body>
<div class="layout">

<?php include("header.php"); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card my-4">

                <!-- HEADER + FILTER -->
<div class="card my-4">
    <!-- HEADER -->
    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">Order Table</h6>
        </div>
    </div>

    <!-- FILTER TÁCH RIÊNG -->
    <div class="filter-group ps-4 pt-2 pb-3">
        <a href="./order.php" 
           class="filter-btn all <?= $type == -1 ? 'active' : '' ?>">All</a>

        <a href="./order.php?type=2" 
           class="filter-btn booked <?= $type == 2 ? 'active' : '' ?>">Booked</a>

        <a href="./order.php?type=3" 
           class="filter-btn delivered <?= $type == 3 ? 'active' : '' ?>">Delivered</a>

        <a href="./order.php?type=4" 
           class="filter-btn success <?= $type == 4 ? 'active' : '' ?>">Success</a>
    </div>


                <!-- TABLE -->
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
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order time</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php while($order = mysqli_fetch_assoc($orders)) { ?>
                                <tr>
                                    <td>#<?= $order['order_id'] ?></td>

                                    <td>
                                        <b><?= $order['name'] ?></b><br>
                                        <small><?= $order['email'] ?></small>
                                    </td>

                                    <td>
                                        <a href="./order-detail.php?order_id=<?= $order['order_id'] ?>">
                                            View now
                                        </a>
                                        <br>
                                        Quantity: <?= $order['quantity'] ?>
                                    </td>

                                    <td><?= $order['address'] ?></td>

                                    <td><small><?= $order['phone_number'] ?></small></td>

                                    <td class="text-center">
                                        <?php
                                            if ($order['status'] == 2) echo '<span class="badge bg-primary">Booked</span>';
                                            else if ($order['status'] == 3) echo '<span class="badge bg-info">Delivering</span>';
                                            else if ($order['status'] == 4) echo '<span class="badge bg-success">Success</span>';
                                            else echo '<span class="badge bg-dark">Unknown</span>';
                                        ?>
                                    </td>

                                    <td class="text-center">
                                        <?= date('d-m-Y', strtotime($order['created_at'])) ?>
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

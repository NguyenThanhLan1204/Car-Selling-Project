<?php
include("header.php");
include("dbconn.php");

// =========================================
// XỬ LÝ UPDATE STATUS ĐƠN HÀNG
// =========================================
if (isset($_GET['order']) && isset($_GET['order_id'])) {

    $order_id = (int) $_GET['order_id'];
    $newStatus = (int) $_GET['order'];

    // update bảng orders
    $sql1 = "UPDATE orders SET status = $newStatus WHERE order_id = $order_id";
    mysqli_query($link, $sql1);

    // update bảng order_detail
    $sql2 = "UPDATE order_detail SET status = $newStatus WHERE order_id = $order_id";
    mysqli_query($link, $sql2);

    header("Location: order.php?msg=updated");
    exit();
}

// =========================================
// LẤY FILTER STATUS (type)
// =========================================
$type = isset($_GET['type']) ? (int)$_GET['type'] : -1;

// =========================================
// LẤY DANH SÁCH ORDER (KHÔNG DÙNG HÀM)
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

<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Order table</h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">

                        <!-- FILTER -->
                        <a href="./order.php" class="badge bg-secondary mx-2">All</a>
                        <a href="./order.php?type=2" class="badge bg-primary mx-2">Booked</a>
                        <a href="./order.php?type=3" class="badge bg-info mx-2">Delivering</a>
                        <a href="./order.php?type=4" class="badge bg-success mx-2">Success</a>

                        <table class="table align-items-center mb-0 mt-3">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Vehicle</th>
                                    <th>Address</th>
                                    <th>Phone</th>              <!-- CỘT MỚI -->
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

                                    <!-- Chỉ còn địa chỉ -->
                                    <td>
                                        <?= $order['address'] ?>
                                    </td>

                                    <!-- Cột số điện thoại mới -->
                                    <td>
                                        <small><?= $order['phone_number'] ?></small>
                                    </td>

                                    <td class="text-center">
                                        <?php
                                        if ($order['status'] == 2)
                                            echo '<span class="badge bg-primary">Booked</span>';
                                        else if ($order['status'] == 3)
                                            echo '<span class="badge bg-info">Delivering</span>';
                                        else if ($order['status'] == 4)
                                            echo '<span class="badge bg-success">Success</span>';
                                        else
                                            echo '<span class="badge bg-dark">Unknown</span>';
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
</body>
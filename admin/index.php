<?php 
include ("dbconn.php");

function totalValue($table) {
    global $link;
    $table = mysqli_real_escape_string($link, $table);
    $query = "SELECT COUNT(*) AS total FROM `$table`";
    $result = mysqli_query($link, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}
?>

<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/index.css"> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>

<body>

<div class="layout">
    <!-- SIDEBAR GỌI TỪ header.php -->
    <?php include ("header.php"); ?>

    <!-- BẮT ĐẦU VÙNG NỘI DUNG -->
    <div class="content-area">

        <div class="container mt-5">
            <div class="row g-4">

            <!-- TOTAL CARD TEMPLATE ↓ -->
            <?php 
                $cards = [
                    ["Users", "person", totalValue("customer"), "", "success"],
                    ["Product", "table_view", totalValue("vehicle"), "", "success"],
                    ["Manufacturer", "factory", totalValue("manufacturer"), "", "success"],
                    ["Booking", "receipt_long", totalValue("orders"), "", "danger"],
                ];

                foreach ($cards as $c) { 
            ?>

            <div class="col-xl-3 col-sm-6">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <i class="material-icons"><?= $c[1] ?></i>

                        <div class="text-end">
                            <p class="text-sm mb-0 text-capitalize">Total <?= $c[0] ?></p>
                            <h4 class="mb-0"><?= $c[2] ?></h4>
                        </div>
                    </div>

                    <div class="card-footer p-3">
                        <p class="mb-0">
                            <span class="text-<?= $c[4] ?> text-sm font-weight-bolder"><?= $c[3] ?></span>
                        </p>
                    </div>

                </div>
            </div>

            <?php } ?>
            <!-- END TEMPLATE -->
        </div>

    </div>
    </div>

</div>

</body>


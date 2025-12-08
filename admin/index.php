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

<!-- BẮT ĐẦU LAYOUT -->
<div class="layout">
    <!-- SIDEBAR GỌI TỪ header.php -->
    <?php include ("header.php"); ?>

    <!-- BẮT ĐẦU VÙNG NỘI DUNG -->
    <div class="content-area">

        <div class="container mt-5">
            <div class="row g-4">

                <!-- Total Users -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="icon ">
                                <i class="material-icons">person</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Users</p>
                                <h4 class="mb-0"><?= totalValue('customer') ?></h4>
                            </div>
                        </div>
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">+3%</span>
                                than last month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Product -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="icon ">
                                <i class="material-icons">table_view</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Product</p>
                                <h4 class="mb-0"><?= totalValue('vehicle') ?></h4>
                            </div>
                        </div>
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">+55%</span>
                                than last week
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Manufacturer -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="icon ">
                                <i class="material-icons">factory</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Manufacturer</p>
                                <h4 class="mb-0"><?= totalValue('manufacturer') ?></h4>
                            </div>
                        </div>
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">+12%</span>
                                than last week
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Order -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="icon ">
                                <i class="material-icons">receipt_long</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Order</p>
                                <h4 class="mb-0"><?= totalValue('orders') ?></h4>
                            </div>
                        </div>
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                <span class="text-danger text-sm font-weight-bolder">-2%</span>
                                than yesterday
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- KẾT THÚC CONTENT-AREA -->

</div>
<!-- KẾT THÚC LAYOUT -->

</body>

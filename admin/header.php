<?php 
$page = basename($_SERVER['PHP_SELF']);
?>

<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/header.css">

    <!-- ⭐ Import font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<aside class="sidenav" id="sidenav-main">

    <!-- Header -->
    <div class="sidenav-header text-center">
        <span class="navbar-brand text-white">CAR SELLING MANAGE</span>
    </div>

    <hr class="horizontal">

    <!-- Main menu -->
    <div class="collapse" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <li class="nav-item">
                <a class="nav-link <?= $page == "index.php" ? 'active' : '' ?>" href="../admin/index.php">
                    <div><i class="fas fa-chart-line"></i></div>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page == "customer.php" ? 'active' : '' ?>" href="customer.php">
                    <div><i class="fas fa-users"></i></div>
                    <span class="nav-link-text">Customer manage</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page == "order.php" ? 'active' : '' ?>" href="order.php">
                    <div><i class="fas fa-shopping-cart"></i></div>
                    <span class="nav-link-text">Order manage</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page == "vehicle.php" ? 'active' : '' ?>" href="list_vehicle.php">
                    <div><i class="fas fa-car"></i></div>
                    <span class="nav-link-text">Vehicle manage</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page == "manufacturer.php" ? 'active' : '' ?>" href="list_manufacturer.php">
                    <div><i class="fas fa-industry"></i></div>
                    <span class="nav-link-text">Manufacturers manage</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- Footer -->
    <div class="sidenav-footer text-center">
        <a class="btn bg-gradient-primary text-white w-75" href="../logout.php">Logout</a>
    </div>

</aside>

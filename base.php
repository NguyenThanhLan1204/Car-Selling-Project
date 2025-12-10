<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Megasix Showroom</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/base.css">
</head>

<body>
    <div class="app d-flex flex-column min-vh-100">
        <header class="header bg-main py-2 shadow-sm">        
            <div class="container-fluid d-flex align-items-center justify-content-between px-4">

                <a href="?page=home" class="text-decoration-none">
                    <h4 id="header_brand-slogan" class="m-0 text-dark fw-bold fs-3">
                        MEGASIX SHOWROOM
                    </h4>
                </a>
                
                <form action="base.php" method="GET" class="search_box position-relative d-flex mx-4" style="flex: 1; max-width: 600px;">
                    <input type="hidden" name="page" value="home">
                    
                    <input type="search" 
                           id="search" 
                           class="form-control h-100 w-100 px-4 rounded-5 border-0 shadow-sm" 
                           name="keyword" 
                           placeholder="Search for cars (e.g. Audi, BMW)..."
                           value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                    
                    <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent text-secondary">
                        <i class="bx bx-search-alt fs-3 custom_hover"></i>
                    </button>
                </form>                                     
    
                <ul class="user_menu list-unstyled d-flex m-0 gap-3 align-items-center">
                    <li class="user_menu-notice position-relative">                     
                        <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-bell fs-2 custom_hover"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><a class="dropdown-item" href="#">No new notifications</a></li>
                        </ul>
                    </li>

                    <li class="user_menu-cart">
                        <a href="<?= isset($_SESSION['username']) ? '?page=cart' : 'login.php' ?>" class="text-dark">
                            <i class="bx bx-cart fs-2 custom_hover"></i>
                        </a>
                    </li>

                    <li class="user_menu-log position-relative">
                        <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-user-circle fs-2 custom_hover"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <?php if (isset($_SESSION['username'])): ?>
                                <li><h6 class="dropdown-header">Hello, <?= htmlspecialchars($_SESSION['username']) ?></h6></li>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="./admin/index.php">Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="?page=order">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="login.php">Login</a></li>
                                <li><a class="dropdown-item" href="registration.php">Register</a></li>
                            <?php endif; ?>
                        </ul>                    
                    </li>                   
                </ul> 
            </div>           
        </header>

        <nav id="myNavbar" class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
            <div class="container justify-content-center">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item">
                        <a href="?page=home" class="nav-link fw-bold text-uppercase text-dark custom_hover <?= (!isset($_GET['page']) || $_GET['page']=='home') ? 'active text-danger' : '' ?>">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=about" class="nav-link fw-bold text-uppercase text-dark custom_hover <?= (isset($_GET['page']) && $_GET['page']=='about') ? 'active text-danger' : '' ?>">
                            About Us
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
            
        <main class="flex-grow-1 bg-light">
            <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'home';
          
                // Sử dụng Switch Case cho gọn và dễ quản lý
                switch ($page) {
                    case 'home':
                        include("./home.php");
                        break;
                    case 'information': // Trang chi tiết xe
                        include("./information.php");
                        break;
                    case 'cart':
                        include("./cart.php");
                        break;
                    case 'order':
                        include("./order.php");
                        break;
                    case 'about':
                        include("./about.php");
                        break;
                    case 'cart_add': // Xử lý thêm vào giỏ (nếu có file này)
                        if (file_exists("./cart_add.php")) include("./cart_add.php");
                        break;
                    default:
                        include("./home.php"); // Mặc định về home
                        break;
                }
            ?>
        </main>

        <footer class="footer bg-dark text-white py-4 mt-auto">
            <div class="container text-center">
                <h3 class="fw-bold mb-3">MEGASIX CARS</h3>
                <ul class="list-unstyled d-flex justify-content-center gap-4 fs-3 mb-0">
                    <li><a href="#" class="text-white custom_hover"><i class='bx bxl-facebook-circle'></i></a></li>
                    <li><a href="#" class="text-white custom_hover"><i class='bx bxl-instagram-alt'></i></a></li>
                    <li><a href="#" class="text-white custom_hover"><i class='bx bxl-youtube'></i></a></li>
                    <li><a href="#" class="text-white custom_hover"><i class='bx bxl-twitter'></i></a></li>
                </ul>
                <p class="mt-3 text-secondary small">&copy; 2025 Megasix Showroom. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>
</html>
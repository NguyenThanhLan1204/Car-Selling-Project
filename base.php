<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/base.css">
    <link rel="stylesheet" href="/test/Car-Selling-Project/assets/css/main.css">
    <link rel="stylesheet" href="/test/Car-Selling-Project/assets/css/base.css">

    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <title>Car</title>
</head>
<body>
    <div class="app">
        <header class="header bg-main py-2">        
            <div class="header_top container-fluid position-relative d-flex align-items-center justify-content-around">

                <h4 id="header_brand-slogan" class="text-decoration-none m-0 text-dark fw-bold fs-2">
                    MEGASIX SHOWROOM
                </h4>
                
                <div class="search_box position-relative d-flex">
                    <input type="search" id="search" class="form-control h-100 w-100 px-4 rounded-3 border-0" name="search" placeholder="Search...">
                    <i class="bx bx-search-alt custom_hover position-absolute top-50 end-0 translate-middle-y me-3"></i>
                </div>                                     
    
                <ul class="user_menu list-unstyled d-flex m-0 gap-3">
                    <li class="user_menu-notice menu-item position-relative">                     
                        <a href="#" class="dropdown_btn" data-target="#bellDropdown">
                            <i class="bx bx-bell fs-2 custom_hover"></i>
                        </a>
                    </li>

                    <li class="user_menu-cart">
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="?page=cart"><i class="bx bx-cart fs-2 custom_hover"></i></a>
                        <?php else: ?>
                            <a href="login.php"><i class="bx bx-cart fs-2 custom_hover"></i></a>
                        <?php endif; ?>
                    </li>

                    <li class="user_menu-log menu-item position-relative">
                        <a href="" class="dropdown_btn" data-target="#userDropdown">
                            <i class="bx bx-user-circle fs-2 custom_hover"></i>
                        </a>                    
                    </li>                   
                </ul> 
                <div class="user-dropdown position-absolute rounded-2 p-3 shadow-lg" id="mainDropdown">
                    <div class="dropdown-content" id="bellDropdown">
                        <h3 class="pt-2 pb-4 text-nowrap fw-bold">Notification</h3>
                        <ul class="list-unstyled">
                            <li class="py-2 text-nowrap text-decoration-none">Notification 1</li>
                            <li class="py-2 text-nowrap text-decoration-none">Notification 2</li>
                        </ul>
                    </div>
                    <div class="dropdown-content" id="userDropdown">
                        <?php if (isset($_SESSION['username'])): ?>
                            <h3 class="pt-2 pb-4 text-nowrap fw-bold">Hello <?= $_SESSION['username'] ?></h3>
                            <ul class="list-unstyled">
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <li class="py-2"><a href="./admin/index.php" class="text-nowrap text-decoration-none custom_hover">Admin Page</a></li>
                                <?php endif; ?>
                                <li class="py-2"><a href="?page=order" class="text-nowrap text-decoration-none custom_hover">My Orders</a></li>
                                <li class="py-2"><a href="logout.php" class="text-nowrap text-decoration-none custom_hover">Logout</a></li>
                            </ul>
                        <?php else: ?>
                            <ul class="list-unstyled">
                                <li class="py-2"><a href="login.php" class="text-nowrap text-decoration-none custom_hover">Login</a></li>
                                <li class="py-2"><a href="registration.php" class="text-nowrap text-decoration-none custom_hover">Register</a></li>
                            </ul>
                        <?php endif; ?>
                    </div>        
                </div>               
            </div>           
        </header>
        <div id="myNavbar" class="navbar w-100 p-0 sticky-top d-flex align-items-center" >
            <ul class="navbar__list list-unstyled align-items-center justify-content-center d-flex gap-5 m-0 py-2 w-100">
                
                <li class="navbar__item">
                    <a href="?page=home" class="custom_hover fw-bold text-uppercase text-decoration-none text-nowrap">Home</a>
                </li>

                <li class="navbar__item">
                    <a href="?page=about" class="custom_hover fw-bold text-uppercase text-decoration-none text-nowrap">About</a>
                </li>
                
            </ul>
        </div>
            
        <main>
            <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'home';
          
                if ($page == 'home') {
                  include("./home.php");
                } elseif ($page == 'cart') {
                  include("./cart.php");
                } elseif ($page == 'order') {
                  include("./order.php");
                } elseif ($page == 'about') {
                  include("./about.php");
                } elseif ($page == 'information') {
                  include("./information.php");
                } else {
                  echo "<h2>Trang không tồn tại</h2>";
                }
            ?>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="contact">
                        <h3 class="contact-header text-center">
                            CAR
                        </h3>
                        <ul class="contact-socials list-unstyled d-flex justify-content-center gap-3">
                            <li>
                                <a href="#">
                                    <i class='bx bxl-facebook-circle fs-1 custom_hover'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-instagram-alt fs-1 custom_hover'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-youtube  fs-1 custom_hover'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-twitter fs-1 custom_hover'></i>
                                </a>
                            </li>
                        </ul>
                    </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>
</html>
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
    <title>Car</title>
</head>
<body>
    <div class="app">
        <header class="header bg-main py-2">        
            <div class="header_top container-fluid d-flex align-items-center justify-content-around">

                <h4 id="header_brand-slogen" class="text-decoration-none m-0 text-dark fw-bold fs-2">
                    EM RỂ TK KHẢI
                </h4>
                
                <div class="search_box position-relative d-flex">
                    <input type="search" id="search" class="form-control" name="search" placeholder="Tìm kiếm">
                    <i class="bx bx-search-alt custom_hover position-absolute top-50 end-0 translate-middle-y me-3"></i>
                </div>                                     
    
                <ul class="user_menu list-unstyled d-flex m-0 gap-3">
                    <li class="user_menu-notice">                     
                        <i class="bx bx-bell fs-2 custom_hover"></i>
                    </li>

                    <li class="user_menu-log">
                        <link href="#" class="">
                            <i class="bx bx-user-circle fs-2 custom_hover"></i>
                        </link>
                    </li>
                    
                    <li class="user_menu-cart">
                        <link href="#" id="" class="">
                            <i class="bx bx-cart fs-2 custom_hover"></i>
                        </link>
                    </li>
                </ul>                
            </div>           
        </header>
        <div id="myNavbar" class="navbar w-100 p-0 sticky-top d-flex align-items-center" >
            <ul class="navbar__list list-unstyled align-items-center justify-content-center d-flex gap-5 m-0 py-2 w-100">
                
                <li class="navbar__item">
                    <a href="" class="custom_hover fw-bold text-uppercase text-decoration-none text-nowrap">Trang chủ</a>
                </li>

                <li class="navbar__item">
                    <a href="" class="custom_hover fw-bold text-uppercase text-decoration-none text-nowrap">Xe</a>
                </li>

                <li class="navbar__item">
                    <a href="" class="custom_hover fw-bold text-uppercase text-decoration-none text-nowrap">About</a>
                </li>
                </li>
            </ul>
        </div>
            
        <main>
            <?php include("./home.php") ?>
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
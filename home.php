<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$link = mysqli_connect("localhost", "root", "") or die(mysqli_error($link));
mysqli_select_db($link, "user_car_system") or die(mysqli_error($link));

$user_id = $_SESSION['user_id'] ?? 0;
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/base.css">
    <title>Car</title>
</head>

<body>
    <div class="app">
        <header class="header bg-main py-2">        
            <div class="header_top container-fluid d-flex align-items-center justify-content-between">

                <h4 id="header_brand-slogen text-decoration-none m-0 text-dark">
                    Car
                </h4>
                
                <div class="search_box position-relative d-flex">
                    <input type="search" id="search" class="form-control" name="search" placeholder="Tìm kiếm">
                    <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                </div>                                     
    
                <ul class="user_menu list-unstyled d-flex m-0 gap-3">
                    <li class="user_menu-notice">
                        <a href="#" id="" class="">
                            <i class="fa-regular fa-bell fs-5"></i>
                        </a>
                    </li>

                    <li class="user_menu-log">
                        <a href="#" id="loginButton" class="search_login">
                            <i class="fa-regular fa-circle-user fs-5"></i>
                        </a>
                    </li>
                    
                    <li class="user_menu-cart">
                        <a href="#" id="" class="">
                            <i class="fa-solid fa-cart-shopping fs-5"></i>
                        </a>
                    </li>
                </ul>                
            </div>           
        </header>
        <div class="top-title">WELCOME TO THE WORLD OF CARS!</div>
    
            <!-- FORM CREATE CAR (bên trái) -->
            <div class="sidebar">
                <div class=" panel-default" >
                    <div class="panel-heading"><h3 >Create New Car</h3></div>
    
                    <div class="panel-body">
                        <form action="" method="post" enctype="multipart/form-data">
    
                            <div class="form-group">
                                <label>Car Name:</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
    
                            <div class="form-group">
                                <label>Brand:</label>
                                <input type="text" class="form-control" name="brand" required>
                            </div>
    
                            <div class="form-group">
                                <label>Color:</label>
                                <input type="text" class="form-control" name="color">
                            </div>
    
                            <div class="form-group">
                                <label>Price:</label>
                                <input type="number" step="1" class="form-control" name="price">
                            </div>
    
                            <div class="form-group">
                                <label>Year:</label>
                                <input type="number" class="form-control" name="year">
                            </div>
                            <div class="form-group">
                                <label>Image:</label>
                                <input type="file" class="form-control" name="picture" accept="image/*" required>
                            </div>
                            <button type="submit" name="create" class="btn btn-primary btn-block">
                                Create
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- LIST CAR (bên phải) -->
            <div class="col-md-8">
                <h2 class="text-center" >WISHLIST CAR</h2>
                <div class="car-container">
                    <?php
                    $user_id = $_SESSION['user_id'];
                    $res = mysqli_query($link, "SELECT * FROM cars WHERE user_id = $user_id ORDER BY id DESC");
                    while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <div class="car-card" >
                        <img src="<?php echo $row['picture']; ?>"  >
    
                        <div class="car-info" >
                            <h4><?php echo $row['name']; ?></h4>
                            <p><b>Brand:</b> <?php echo $row['brand']; ?></p>
                            <p><b>Color:</b> <?php echo $row['color']; ?></p>
                            <p><b>Year:</b> <?php echo $row['year']; ?></p>
                            <p><b>Price:</b> <?php echo number_format($row['price']); ?> USD</p>
                        </div>
    
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </div>
                    <?php 
                } 
                ?>
                </div>
            </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
<?php
// INSERT CAR
if (isset($_POST["create"])) {

    $picture_name = $_FILES['picture']['name'];
    $tmp = $_FILES['picture']['tmp_name'];

    $path = "cars/" . $picture_name;

    move_uploaded_file($tmp, $path);

    // Thêm vào CSDL
    mysqli_query($link, "
        INSERT INTO cars(name, brand, color, price, year, picture, user_id)
        VALUES(
            '$_POST[name]',
            '$_POST[brand]',
            '$_POST[color]',
            '$_POST[price]',
            '$_POST[year]',
            '$path',
            '$user_id'
        )
    ") or die(mysqli_error($link));
    echo "<script>alert('New car created successfully!'); window.location='';</script>";
}
?>
</html> 
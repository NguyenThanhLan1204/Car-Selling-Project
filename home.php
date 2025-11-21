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
    <title>Car List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/home.css">
</head>

<body>
<div class="row">
    <div class="top-bar">
    <div class="logout-box">
    <a href="logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
        Log out
    </a>
    </div>
    <div class="top-title">WELCOME TO THE WORLD OF CARS!</div>
</div>
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
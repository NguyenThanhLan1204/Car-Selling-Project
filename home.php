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

<body>
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
<?php
$link = mysqli_connect("localhost", "root", "") or die(mysqli_error($link));
mysqli_select_db($link, "user_car_system") or die(mysqli_error($link));

$id = $_GET["id"]; // ID of the car to edit

// Get car info from database
$res = mysqli_query($link, "SELECT * FROM cars WHERE id=$id");
$row = mysqli_fetch_array($res);

$name = $row["name"];
$color = $row["color"];
$brand = $row["brand"];
$price = $row["price"];
$year = $row["year"];
$old_picture = $row["picture"]; 
?>

<html lang="en">
<head>
    <title>Update Car Information</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="col-lg-4">
        <h2>Car Information Form</h2>

        <form action="" name="form1" method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label for="name">Car Name:</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?php echo $name; ?>">
            </div>

            <div class="form-group">
                <label for="brand">Brand:</label>
                <input type="text" class="form-control" id="brand" name="brand"
                       value="<?php echo $brand; ?>">
            </div>

            <div class="form-group">
                <label for="color">Color:</label>
                <input type="text" class="form-control" id="color" name="color"
                       value="<?php echo $color; ?>">
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" class="form-control" id="price" name="price"
                       value="<?php echo $price; ?>">
            </div>

            <div class="form-group">
                <label for="year">Year:</label>
                <input type="number" class="form-control" id="year" name="year"
                       value="<?php echo $year; ?>">
            </div>

            <!-- Hiển thị ảnh cũ -->
            <div class="form-group">
                <label>Current Image:</label><br>
                <img src="<?php echo $old_picture; ?>" width="150">
            </div>
            <div class="form-group">
                <label>Upload New Image (optional):</label>
                <input type="file" class="form-control" name="picture" accept="image/*">
            </div>

            <button type="submit" name="update" class="btn btn-default">Update</button>
        </form>

    </div>
</div>
</body>

<?php
// UPDATE CAR
if (isset($_POST["update"])) {

    // Mặc định dùng ảnh cũ
    $picture_name = $old_picture;

    // Nếu user upload ảnh mới
    if (!empty($_FILES["picture"]["name"])) {

    $new_name = $_FILES['picture']['name'];
    $tmp = $_FILES['picture']['tmp_name'];

    // Lưu vào thư mục cars/
    $path = "cars/" . $new_name;

    move_uploaded_file($tmp, $path);

    $picture_name = $path;
}


    // Update vào database
    mysqli_query($link, "
        UPDATE cars SET 
            name='$_POST[name]',
            brand='$_POST[brand]',
            color='$_POST[color]',
            price='$_POST[price]',
            year='$_POST[year]',
            picture='$picture_name'
        WHERE id=$id
    ") or die(mysqli_error($link));

    ?>
    <script>
        alert("Car information updated successfully!");
        window.location = "home.php";
    </script>
    <?php
}
?>
</html>

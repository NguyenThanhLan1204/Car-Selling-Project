<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Kết nối DB
include("dbconn.php"); 

// Xử lý thêm xe
if (isset($_POST["submit"])) {

    $manufacturer_id = $_POST["manufacturer_id"];
    $model = $_POST["model"];
    $category = $_POST["category"];   // ⭐ THÊM CATEGORY
    $year = $_POST["year"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    // Upload image
    $image = $_FILES["image"]["name"];
    $upload_path = "../assets/img/" . $image;
    $image_url = "./assets/img/" . $image; // Đường lưu DB

    move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path);

    // Query INSERT
    $sql = "
        INSERT INTO vehicle 
        (manufacturer_id, model, category, year, price, image_url, stock, description)
        VALUES 
        ('$manufacturer_id', '$model', '$category', '$year', '$price', '$image_url', '$stock', '$description')
    ";

    if (mysqli_query($link, $sql)) {
        echo "<script>alert('Vehicle Added Successfully'); window.location='list_vehicle.php';</script>";
    } else {
        echo "SQL Error: " . mysqli_error($link);
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>

<?php include("header.php"); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Add Vehicle</h4>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <!-- Manufacturer -->
                <label>Manufacturer</label>
                <select name="manufacturer_id" class="form-control" required>
                    <option value="">Select Manufacturer</option>
                    <?php 
                        $manu = mysqli_query($link, "SELECT * FROM manufacturer ORDER BY name ASC");
                        while ($m = mysqli_fetch_assoc($manu)) {
                            echo "<option value='{$m['manufacturer_id']}'>{$m['name']}</option>";
                        }
                    ?>
                </select>

                <!-- Model -->
                <label>Model</label>
                <input type="text" name="model" class="form-control" required>

                <!-- ⭐ CATEGORY (Fix chính) -->
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="sedan">Sedan</option>
                    <option value="hatchback">Hatchback</option>
                    <option value="suv">SUV</option>
                    <option value="mpv">MPV</option>
                    <option value="pickup">Pickup</option>
                    <option value="supercar">Supercar</option>
                </select>

                <!-- Year -->
                <label>Year</label>
                <input type="number" name="year" class="form-control" required>

                <!-- Price -->
                <label>Price</label>
                <input type="number" name="price" class="form-control" required>

                <!-- Stock -->
                <label>Stock</label>
                <input type="number" name="stock" class="form-control">

                <!-- Description -->
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>

                <!-- Image -->
                <label>Image</label>
                <input type="file" name="image" class="form-control" required>

                <button type="submit" name="submit" class="btn btn-success mt-3">
                    Save Vehicle
                </button>

            </form>
        </div>
    </div>
</div>

</body>
</html>

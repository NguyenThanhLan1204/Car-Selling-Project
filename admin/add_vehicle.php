<?php
include("dbconn.php");

if (isset($_POST["submit"])) {
    $manufacturer_id = $_POST["manufacturer_id"];
    $category = $_POST["category"];   // ⭐ thêm dòng này
    $model = $_POST["model"];
    $year = $_POST["year"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    // UPLOAD IMAGE
    $image = $_FILES["image"]["name"];
    $image_url = "./assets/img/" . $image;
    $upload_path = "../assets/img/" . $image;
    move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path);

    // INSERT SQL (chỉ thêm category)
    $sql = "INSERT INTO vehicle (manufacturer_id, category, model, year, price, stock, description, image_url)
            VALUES ('$manufacturer_id', '$category', '$model', '$year', '$price', '$stock', '$description', '$image_url')";

    mysqli_query($link, $sql);

    echo "<script>alert('Vehicle Added'); window.location='list_vehicle.php';</script>";
}
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/add_vers.css"> 
</head>

<form method="POST" enctype="multipart/form-data">

    <label>Manufacturer</label>
    <input type="text" name="manufacturer_id" class="form-control" required>

    <!-- ⭐ THÊM CHỈ MỘT Ô CATEGORY -->
    <label>Category</label>
    <input type="text" name="category" class="form-control" required>

    <label>Model</label>
    <input type="text" name="model" class="form-control">

    <label>Year</label>
    <input type="number" name="year" class="form-control">

    <label>Price</label>
    <input type="number" name="price" class="form-control">

    <label>Stock</label>
    <input type="number" name="stock" class="form-control">

    <label>Description</label>
    <textarea name="description" class="form-control"></textarea>

    <label>Image</label>
    <input type="file" name="image" class="form-control">

    <button type="submit" name="submit" class="btn btn-primary mt-3">Add Vehicle</button>
</form>

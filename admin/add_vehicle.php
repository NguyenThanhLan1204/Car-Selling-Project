<?php
include("dbconn.php");

if (isset($_POST["submit"])) {

    $manufacturer_id = $_POST["manufacturer_id"];
    $model = $_POST["model"];
    $year = $_POST["year"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    /* ========================================
    UPLOAD IMAGE → assets/img/
    ========================================= */
    $image = $_FILES["image"]["name"];

    $target_dir = "../assets/img/";
    $target_file = $target_dir . basename($image);

    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $db_image_path = "assets/img/" . $image;

    /* ========================================
    UPLOAD VIDEO → assets/video/
    ========================================= */
    $video = $_FILES["video"]["name"];   // <-- thêm input name="video" trong form

    $video_target_dir = "../assets/video/";
    $video_target_file = $video_target_dir . basename($video);

    move_uploaded_file($_FILES["video"]["tmp_name"], $video_target_file);

    $db_video_path = "assets/video/" . $video;


    /* ========================================
    INSERT VÀO DATABASE
    ========================================= */
    $sql = "INSERT INTO vehicle 
            (manufacturer_id, model, year, price, stock, description, image_url, video_url)
            VALUES 
            ('$manufacturer_id', '$model', '$year', '$price', '$stock', '$description', '$db_image_path', '$db_video_path')";

    mysqli_query($link, $sql);

    echo "<script>alert('Vehicle Added'); window.location='list_vehicle.php';</script>";}
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/add_vers.css"> 
</head>

<div class="layout">

    <!-- SIDEBAR -->
    <?php include ("header.php"); ?>

    <div class="container mt-4">

        <div class="card">
            <div class="card-header">
                <h4>Add Vehicle</h4>
            </div>

            <div class="card-body">

                <form method="POST" enctype="multipart/form-data">

                    <label>Manufacturer</label>
                    <select name="manufacturer_id" class="form-control" required>
                        <option>Select Manufacturer</option>
                        <?php 
                            $manu = mysqli_query($link, "SELECT * FROM manufacturer");
                            foreach ($manu as $m) {
                                echo "<option value='{$m['manufacturer_id']}'>{$m['name']}</option>";
                            }
                        ?>
                    </select>

                    <label>Model</label>
                    <input type="text" name="model" class="form-control" required>

                    <label>Year</label>
                    <input type="number" name="year" class="form-control" required>

                    <label>Price</label>
                    <input type="number" name="price" class="form-control" required>

                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control">

                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>

                    <label>Image</label>
                    <input type="file" name="image" class="form-control" required>

                    <label class="mt-3">Video</label>
                    <input type="file" name="video" class="form-control" required>


                    <button type="submit" name="submit" class="btn btn-success mt-3">
                        Save Vehicle
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

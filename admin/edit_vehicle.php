<?php
include("dbconn.php");

$id = $_GET["id"];

$vehicle = mysqli_fetch_assoc(
    mysqli_query($link, "SELECT * FROM vehicle WHERE vehicle_id = $id")
);

if (isset($_POST["update"])) {
    $manufacturer_id = $_POST["manufacturer_id"];
    $model = $_POST["model"];
    $year = $_POST["year"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    if (!empty($_FILES["image"]["name"])) {
        $image = $_FILES["image"]["name"];
        $path = "../uploads/vehicles/" . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $path);
    } else {
        $path = $vehicle["image_url"];
    }

    $sql = "
        UPDATE vehicle SET
        manufacturer_id='$manufacturer_id',
        model='$model',
        year='$year',
        price='$price',
        stock='$stock',
        description='$description',
        image_url='$path'
        WHERE vehicle_id = $id
    ";

    mysqli_query($link, $sql);

    echo "<script>alert('Updated!'); window.location='list_vehicle.php';</script>";
}
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/edit_vers.css"> 
</head>

<div class="layout">

    <!-- SIDEBAR GỌI TỪ header.php -->
    <?php include ("header.php"); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Edit Vehicle</h4>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <label>Manufacturer</label>
                <select name="manufacturer_id" class="form-control">
                    <?php 
                        $manu = mysqli_query($link, "SELECT * FROM manufacturer");
                        foreach ($manu as $m) {
                            $sel = $m['manufacturer_id'] == $vehicle['manufacturer_id'] ? "selected" : "";
                            echo "<option value='{$m['manufacturer_id']}' $sel>{$m['name']}</option>";
                        }
                    ?>
                </select>

                <label>Model</label>
                <input type="text" name="model" class="form-control" value="<?= $vehicle['model']; ?>">

                <label>Year</label>
                <input type="number" name="year" class="form-control" value="<?= $vehicle['year']; ?>">

                <label>Price</label>
                <input type="number" name="price" class="form-control" value="<?= $vehicle['price']; ?>">

                <label>Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= $vehicle['stock']; ?>">

                <label>Description</label>
                <textarea name="description" class="form-control"><?= $vehicle['description']; ?></textarea>

                <label>Image</label>
                <input type="file" name="image" class="form-control">

                <img src="<?= $vehicle['image_url']; ?>" width="120" class="mt-2">

                <button type="submit" name="update" class="btn btn-primary mt-3">
                    Update Vehicle
                </button>

            </form>
        </div>
    </div>
</div>
</div>
    <!-- KẾT THÚC CONTENT-AREA -->

</div>
<!-- KẾT THÚC LAYOUT -->
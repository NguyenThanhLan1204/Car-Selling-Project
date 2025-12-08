<?php
include("../admin/includes/header.php");
include("../config/dbcon.php");

if (isset($_POST["submit"])) {
    $manufacturer_id = $_POST["manufacturer_id"];
    $model = $_POST["model"];
    $year = $_POST["year"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    // File upload (simple)
    $image = $_FILES["image"]["name"];
    $path = "../uploads/vehicles/" . $image;
    move_uploaded_file($_FILES["image"]["tmp_name"], $path);

    $sql = "INSERT INTO vehicle (manufacturer_id, model, year, price, stock, description, image_url)
            VALUES ('$manufacturer_id', '$model', '$year', '$price', '$stock', '$description', '$path')";

    mysqli_query($link, $sql);

    echo "<script>alert('Vehicle Added'); window.location='list_vehicle.php';</script>";
}
?>

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

                <button type="submit" name="submit" class="btn btn-success mt-3">
                    Save Vehicle
                </button>

            </form>

        </div>
    </div>
</div>

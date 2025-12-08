<?php
include("dbconn.php");

$id = $_GET["id"];

$m = mysqli_fetch_assoc(
    mysqli_query($link, "SELECT * FROM manufacturer WHERE manufacturer_id=$id")
);

if (isset($_POST["update"])) {
    $name = $_POST["name"];
    $country = $_POST["country"];
    $description = $_POST["description"];

    $sql = "
        UPDATE manufacturer 
        SET name='$name', country='$country', description='$description'
        WHERE manufacturer_id = $id
    ";
    
    mysqli_query($link, $sql);

    echo "<script>alert('Updated!'); window.location='list_manufacturer.php';</script>";
}
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/edit_manuf.css"> 
</head>

<div class="layout">

    <!-- SIDEBAR GỌI TỪ header.php -->
    <?php include ("header.php"); ?>
<div class="container mt-4">
    <div class="card">

        <div class="card-header">
            <h4>Edit Manufacturer</h4>
        </div>

        <div class="card-body">

            <form method="POST">

                <label>Name</label>
                <input type="text" name="name" value="<?= $m['name']; ?>" class="form-control" required>

                <label>Country</label>
                <input type="text" name="country" value="<?= $m['country']; ?>" class="form-control">

                <label>Description</label>
                <textarea name="description" class="form-control"><?= $m['description']; ?></textarea>

                <button type="submit" name="update" class="btn btn-primary mt-3">
                    Update
                </button>

            </form>

        </div>

    </div>
</div>
</div>
    <!-- KẾT THÚC CONTENT-AREA -->

</div>
<!-- KẾT THÚC LAYOUT -->
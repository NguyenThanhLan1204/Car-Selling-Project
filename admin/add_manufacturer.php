<?php
include("dbconn.php");

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $country = $_POST["country"];
    $description = $_POST["description"];

    $sql = "INSERT INTO manufacturer (name, country, description)
            VALUES ('$name', '$country', '$description')";
    mysqli_query($link, $sql);

    echo "<script>alert('Manufacturer Added'); window.location='list_manufacturer.php';</script>";
}
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/ver_manuf.css"> 
</head>

<div class="layout">

    <!-- SIDEBAR GỌI TỪ header.php -->
    <?php include ("header.php"); ?>
<div class="container mt-4">
    <div class="card">

        <div class="card-header">
            <h2>Add Manufacturer</h2>
        </div>

        <div class="card-body">

            <form method="POST">

                <label>Name</label>
                <input type="text" name="name" class="form-control" required>

                <label>Country</label>
                <input type="text" name="country" class="form-control">

                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>

                <button type="submit" name="submit" class="btn btn-success mt-3">
                    Save
                </button>

            </form>

        </div>

    </div>
</div>
</div>
    <!-- KẾT THÚC CONTENT-AREA -->

</div>
<!-- KẾT THÚC LAYOUT -->
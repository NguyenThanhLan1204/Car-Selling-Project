<?php
include("../admin/includes/header.php");
include("../config/dbcon.php");

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

<div class="container mt-4">
    <div class="card">

        <div class="card-header">
            <h4>Add Manufacturer</h4>
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

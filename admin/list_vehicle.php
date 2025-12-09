<?php
include("dbconn.php");
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/list_vers.css"> 
</head>

<div class="layout">

<?php include("header.php"); ?>

<div class="container mt-4">

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Vehicle List</h4>
            <button class="btn btn-success" onclick="window.location.href='add_vehicle.php'">+ Add Vehicle</button>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Model</th>
                        <th>Category</th>
                        <th>Manufacturer</th>
                        <th>Image</th>
                        <th>Year</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $sql = "
                    SELECT v.*, m.name AS manufacturer_name
                    FROM vehicle v
                    LEFT JOIN manufacturer m 
                        ON v.manufacturer_id = m.manufacturer_id
                ";
                $vehicles = mysqli_query($link, $sql);

                if (mysqli_num_rows($vehicles) > 0) {
                    foreach ($vehicles as $item) {
                ?>
                    <tr>
                        <td><?= $item["vehicle_id"]; ?></td>
                        <td><?= $item["model"]; ?></td>
                        <td><?= $item["category"]; ?></td>
                        <td><?= $item["manufacturer_name"]; ?></td>

                        <td>
                            <img src="../<?= $item["image_url"]; ?>" width="70" height="50">
                        </td>

                        <td><?= $item["year"]; ?></td>
                        <td><?= number_format($item["price"]); ?></td>
                        <td><?= $item["stock"]; ?></td>

                        <td>
                            <button class="btn btn-primary btn-sm" 
                                onclick="window.location.href='edit_vehicle.php?id=<?= $item['vehicle_id']; ?>'">
                                Edit
                            </button>
                        </td>

                        <td>
                            <button class="btn btn-danger btn-sm" 
                                onclick="if(confirm('Delete this vehicle?')) window.location.href='delete_vehicle.php?id=<?= $item['vehicle_id']; ?>'">
                                Delete
                            </button>
                        </td>
                    </tr>

                <?php } } else { ?>
                    <tr><td colspan="10">No Vehicles Found</td></tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>
</div>

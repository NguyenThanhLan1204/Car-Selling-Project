<?php
include("header.php");
include("dbconn.php");
?>

<div class="container mt-4">

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Vehicle List</h4>
            <a href="add_vehicle.php" class="btn btn-success">+ Add Vehicle</a>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Model</th>
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
                    LEFT JOIN manufacturer m ON v.manufacturer_id = m.manufacturer_id
                ";
                $vehicles = mysqli_query($link, $sql);

                if (mysqli_num_rows($vehicles) > 0) {
                    foreach ($vehicles as $item) {
                ?>
                    <tr>
                        <td><?= $item["vehicle_id"]; ?></td>
                        <td><?= $item["model"]; ?></td>
                        <td><?= $item["manufacturer_name"]; ?></td>

                        <td>
                            <img src="<?= $item["image_url"]; ?>" width="70" height="50">
                        </td>

                        <td><?= $item["year"]; ?></td>
                        <td><?= number_format($item["price"]); ?></td>
                        <td><?= $item["stock"]; ?></td>

                        <td>
                            <a href="edit_vehicle.php?id=<?= $item['vehicle_id']; ?>" 
                               class="btn btn-primary btn-sm">Edit</a>
                        </td>

                        <td>
                            <a href="delete_vehicle.php?id=<?= $item['vehicle_id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this vehicle?');">
                               Delete
                            </a>
                        </td>
                    </tr>

                <?php } } else { ?>
                    <tr><td colspan="9">No Vehicles Found</td></tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>

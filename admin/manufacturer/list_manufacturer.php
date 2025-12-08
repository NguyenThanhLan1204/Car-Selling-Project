<?php
include("../admin/includes/header.php");
include("../config/dbcon.php");
?>

<div class="container mt-4">

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Manufacturer List</h4>
            <a href="add_manufacturer.php" class="btn btn-success">+ Add Manufacturer</a>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Description</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $manu = mysqli_query($link, "SELECT * FROM manufacturer");

                    if (mysqli_num_rows($manu) > 0) {
                        foreach ($manu as $m) {
                    ?>
                        <tr>
                            <td><?= $m["manufacturer_id"]; ?></td>
                            <td><?= $m["name"]; ?></td>
                            <td><?= $m["country"]; ?></td>
                            <td><?= $m["description"]; ?></td>

                            <td>
                                <a href="edit_manufacturer.php?id=<?= $m['manufacturer_id']; ?>" 
                                   class="btn btn-primary btn-sm">Edit</a>
                            </td>

                            <td>
                                <a href="delete_manufacturer.php?id=<?= $m['manufacturer_id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this manufacturer?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                        <tr><td colspan="6">No Manufacturers Found</td></tr>
                    <?php } ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

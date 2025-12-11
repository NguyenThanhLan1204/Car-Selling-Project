<?php
include("dbconn.php");
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="manuf-title">Manufacturer List</h2>

                <button 
                    class="btn btn-success add-btn"
                    onclick="window.location.href='add_manufacturer.php'">
                    + Add Manufacturer
                </button>
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
                                <button 
                                    class="btn btn-primary btn-sm"
                                    onclick="window.location.href='edit_manufacturer.php?id=<?= $m['manufacturer_id']; ?>'">
                                    Edit
                                </button>
                                </td>

                                <td>
                                <button 
                                    class="btn btn-danger btn-sm"
                                    onclick="if(confirm('Delete this manufacturer?')) 
                                            window.location.href='delete_manufacturer.php?id=<?= $m['manufacturer_id']; ?>'">
                                    Delete
                                </button>
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
    <!-- KẾT THÚC CONTENT-AREA -->
</div>
<!-- KẾT THÚC LAYOUT -->
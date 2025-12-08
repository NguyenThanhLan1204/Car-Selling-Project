<?php 
include ("dbconn.php");
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/manufacturer.css"> 
</head>
<body>
<div class="row">
    <!-- CREATE NEW MANUFACTURER (LEFT) -->
    <div class="sidebar">
        <div class="panel panel-default">
            <div class="panel-heading"><h3>Create New Manufacturer</h3></div>

            <div class="panel-body">
                <form action="" method="post">

                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>Country:</label>
                        <input type="text" class="form-control" name="country">
                    </div>

                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <button type="submit" name="create_manufacturer" class="btn btn-primary btn-block">
                        Create Manufacturer
                    </button>

                </form>
            </div>
        </div>
    </div>

    <!-- LIST MANUFACTURER (RIGHT) -->
    <div class="col-md-8">
        <h2 class="text-center">MANUFACTURER LIST</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Description</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $res = mysqli_query($link, "SELECT * FROM manufacturer ORDER BY manufacturer_id DESC");
                while ($row = mysqli_fetch_assoc($res)) {
            ?>
                <tr>
                    <td><?= $row['manufacturer_id']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td><?= $row['country']; ?></td>
                    <td><?= $row['description']; ?></td>

                    <td>
                        <a href="edit.php?id=<?= $row['manufacturer_id']; ?>" class="btn btn-success btn-sm">Edit</a>
                        <a href="delete.php?id=<?= $row['manufacturer_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>
</div>
</body>
<?php
// CREATE MANUFACTURER
if (isset($_POST["create_manufacturer"])) {

    mysqli_query($link, "
        INSERT INTO manufacturer(name, country, description)
        VALUES(
            '{$_POST['name']}',
            '{$_POST['country']}',
            '{$_POST['description']}'
        )
    ") or die(mysqli_error($link));
    echo "<script>alert('Manufacturer created successfully!'); window.location='';</script>";
}
?>
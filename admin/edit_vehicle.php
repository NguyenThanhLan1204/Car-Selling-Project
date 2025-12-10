<?php
include("dbconn.php");

$id = $_GET["id"];

// Lấy dữ liệu xe
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

    /* ========================================
       XỬ LÝ ẢNH (assets/img/)
    ========================================= */
    if (!empty($_FILES["image"]["name"])) {

        // Tên file mới
        $image = $_FILES["image"]["name"];

        // Đường dẫn thư mục lưu file (TRONG project)
        $target_dir = "../assets/img/";
        $target_file = $target_dir . basename($image);

        // Upload file
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        // Đường dẫn lưu vào DB (không có ../)
        $db_image_path = "assets/img/" . $image;

        // Xóa ảnh cũ nếu tồn tại
        if (!empty($vehicle["image_url"]) && file_exists("../" . $vehicle["image_url"])) {
            unlink("../" . $vehicle["image_url"]);
        }

    } else {
        // Không upload ảnh mới → giữ ảnh cũ
        $db_image_path = $vehicle["image_url"];
    }

    /* ========================================
       XỬ LÝ VIDEO (assets/video/)
    ========================================= */
    if (!empty($_FILES["video"]["name"])) {

        $video = $_FILES["video"]["name"];

        // Thư mục lưu video
        $target_dir_video = "../assets/video/";
        $target_file_video = $target_dir_video . basename($video);

        // Upload video
        move_uploaded_file($_FILES["video"]["tmp_name"], $target_file_video);

        // Đường dẫn lưu DB
        $db_video_path = "assets/video/" . $video;

        // Xóa video cũ
        if (!empty($vehicle["video_url"]) && file_exists("../" . $vehicle["video_url"])) {
            unlink("../" . $vehicle["video_url"]);
        }

    } else {
        // Không upload → giữ video cũ
        $db_video_path = $vehicle["video_url"];
    }


    /* ========================================
       UPDATE DATABASE
    ========================================= */
    $sql = "
        UPDATE vehicle SET
        manufacturer_id='$manufacturer_id',
        model='$model',
        category='$category',
        year='$year',
        price='$price',
        stock='$stock',
        description='$description',
        image_url='$db_image_path',
        video_url='$db_video_path'
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

    <?php include("header.php"); ?>

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
                    <img src="../<?= $vehicle['image_url']; ?>" width="150" class="mt-2">

                    <label class="mt-3">Video</label>
                    <input type="file" name="video" class="form-control">
                    <video width="150" class="mt-2" controls>
                        <source src="../<?= !empty($vehicle['video_url']) ? $vehicle['video_url'] : 'uploads/no-video.mp4'; ?>" 
                                type="video/mp4">
                    </video>
                    
                    <button type="submit" name="update" class="btn btn-primary mt-3">
                        Update Vehicle
                    </button>

                </form>

            </div>
        </div>

    </div>

</div>

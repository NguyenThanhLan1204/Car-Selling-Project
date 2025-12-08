<?php
include("dbconn.php");

if (isset($_POST["submit"])) {
    $manufacturer_id = $_POST["manufacturer_id"];
    $category = $_POST["category"]; // ⭐ THÊM DÒNG NÀY
    $model = $_POST["model"];
    $year = $_POST["year"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    // ----------------------------
    // UPLOAD IMAGE
    // ----------------------------
    $image = $_FILES["image"]["name"];

    // đường dẫn lưu vào DB (dùng cho <img src="">)
    $image_url = "./assets/img/" . $image;

    // đường dẫn hệ thống thật để move file (admin nằm trong folder con)
    $upload_path = "../assets/img/" . $image;

    // di chuyển file upload vào thư mục assets/img/
    move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path);

    // ----------------------------
    // INSERT SQL — chỉ thêm category
    // ----------------------------
    $sql = "INSERT INTO vehicle (manufacturer_id, category, model, year, price, stock, description, image_url)
            VALUES ('$manufacturer_id', '$category', '$model', '$year', '$price', '$stock', '$description', '$image_url')";

    mysqli_query($link, $sql);

    echo "<script>alert('Vehicle Added'); window.location='list_vehicle.php';</script>";
}
?>
<head>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="./css/add_vers.css"> 
</head>

<?php
include("dbconn.php");

$id = $_GET["id"];
$vehicle = mysqli_fetch_assoc(
    mysqli_query($link, "SELECT * FROM vehicle WHERE vehicle_id = $id")
);

if (isset($_POST["update"])) {

    $manufacturer_id = $_POST["manufacturer_id"];
    $model = $_POST["model"];
    $category = $_POST["category"];
    $year = $_POST["year"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    // Nếu có chọn ảnh mới
    if (!empty($_FILES["image"]["name"])) {
        $image = $_FILES["image"]["name"];
        $image_url = "assets/img/" . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], "../assets/img/" . $image);
    } else {
        $image_url = $vehicle["image_url"];
    }

    $sql = "
        UPDATE vehicle SET
            manufacturer_id='$manufacturer_id',
            model='$model',
            category='$category',
            year='$year',
            price='$price',
            stock='$stock',
            description='$description',
            image_url='$image_url'
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
            <h4>Edit

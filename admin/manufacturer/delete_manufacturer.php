<?php
include("dbconn.php");

$id = $_GET["id"];

mysqli_query($link, "DELETE FROM manufacturer WHERE manufacturer_id = $id");

echo "<script>alert('Manufacturer deleted!'); window.location='list_manufacturer.php';</script>";
?>

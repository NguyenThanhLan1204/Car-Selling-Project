<?php
include("../config/dbcon.php");

$id = $_GET["id"];

mysqli_query($link, "DELETE FROM vehicle WHERE vehicle_id = $id");

echo "<script>alert('Vehicle deleted!'); window.location='list_vehicle.php';</script>";
?>

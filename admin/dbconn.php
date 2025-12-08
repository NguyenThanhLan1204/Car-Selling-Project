<?php
$link = mysqli_connect("localhost", "root", "") or die(mysqli_error($link));
mysqli_select_db($link, "car_selling") or die(mysqli_error($link));
function getAllCustomers() {
    global $link;
    $sql = "SELECT * FROM customer";
    $query = mysqli_query($link, $sql);
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}
?>

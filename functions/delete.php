<?php
$link = mysqli_connect("localhost", "root", "") or die(mysqli_error($link));
mysqli_select_db($link, "user_car_system") or die(mysqli_error($link));

$id = $_GET["id"]; 

$res = mysqli_query($link, "SELECT * FROM cars WHERE id=$id");
$car = mysqli_fetch_array($res);


if (isset($_GET["confirm"]) && $_GET["confirm"] == "yes") {
    $delete_query = "DELETE FROM cars WHERE id=$id";
    mysqli_query($link, $delete_query) or die(mysqli_error($link));
    echo "<script>
            alert('Car removed successfully!');
            window.location.href = 'home.php';
          </script>";
    exit;
}

echo "<script>
        if (confirm('Are you sure you want to remove this car from your wishlist?')) {
            window.location.href = 'delete.php?id=$id&confirm=yes';
        } else {
            window.location.href = 'home.php';
        }
      </script>";

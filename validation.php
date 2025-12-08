<?php
session_start();

$con = mysqli_connect('localhost', 'root', '', 'user_car_system');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$name = mysqli_real_escape_string($con, $_POST['user']);
$pass = mysqli_real_escape_string($con, $_POST['password']);

// Kiểm tra username và password
$s = "SELECT * FROM users WHERE username='$name' AND password='$pass'";
$result = mysqli_query($con, $s);

if ($result) {
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // ✅ Lưu thêm user_id để dùng ở home.php
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_id'] = $row['id'];

        header('Location: home.php');
        exit();
    } else {
        echo "<script>
                alert('Invalid username or password! Please try again.');
                window.location.href = 'login.php';
              </script>";
        exit();
    }
} else {
    echo "SQL Error: " . mysqli_error($con);
}

mysqli_close($con);
?>

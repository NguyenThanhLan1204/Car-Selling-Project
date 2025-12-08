<?php
session_start();
if (isset($_SESSION['username'])) {
    // SỬA: Chuyển hướng về base.php
    header("Location: base.php?page=home");
    exit();
}
?>
<html lang="en" xmlns="">
    <head>
        <title>Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <link rel="stylesheet" href="./css/login.css"> 
    </head>

    <body>
        <div class="container">
            <div class="login d-flex justify-content-center align-items-center">
                <div class="login__form">
                    <h2>Login</h2>
                    <form action="validation.php" method="post">
                        <div class="form-group login__form--username">
                            <label for="username" class="form-label">Username</label>                        
                            <input type="text" id="username" name="user" class="form-control" required>                            
                        </div>
                        <div class="form-group login__form--password">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <div class="registration">
                            <p class="mt-3 mb-0">Don't have an account? <a href="registration.php">Registration</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>   
    </body>

</html>
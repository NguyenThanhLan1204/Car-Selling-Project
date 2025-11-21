<!DOCTYPE html>
<html lang="en" xmlns="">
    <head>
        <title>Registration</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">       
        <link rel="stylesheet" href="./css/registration.css"> 
    </head>

    <body>
        <div class="container">
            <div class="registration d-flex justify-content-center align-items-center">
                <div class="registration__form">
                    <h2>Registration</h2>
                    <form action="connect_registration.php" method="post">
                        
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>                                
                            <input type="text" id="username" name="user" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>                                
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="dob" class="form-label">Date of Birth</label>                                
                            <input type="date" id="dob" name="dob" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="nationality" class="form-label">Nationality</label>                                
                            <input type="text" id="nationality" name="nationality" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="phonenumber" class="form-label">Phone Number</label>                                
                            <input type="tel" id="phonenumber" name="phonenumber" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Register</button>
                        
                        <div class="registration">
                            <p class="mt-3 mb-0">Already have an account? <a href="login.php">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </body>
</html>
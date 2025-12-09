<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/registration.css"> 
</head>

<body>
    <div class="registration-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="registration-card">
                        <h2 class="text-center mb-4">Registration</h2>

                        <form action="connect_registration.php" method="post">

                            <!-- FULL NAME -->
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" id="fullname" name="name" class="form-control custom-input" required>
                            </div>

                            <!-- AGE -->
                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" id="age" name="age" class="form-control custom-input" required>
                            </div>

                            <!-- USERNAME -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>                                
                                <input type="text" id="username" name="user" class="form-control custom-input" required>
                            </div>

                            <!-- EMAIL -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>                                
                                <input type="email" id="email" name="email" class="form-control custom-input" required>
                            </div>

                            <!-- DOB + NATIONALITY -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>                                
                                    <input type="date" id="dob" name="dob" class="form-control custom-input">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nationality" class="form-label">Nationality</label>                                
                                    <input type="text" id="nationality" name="nationality" class="form-control custom-input">
                                </div>
                            </div>

                            <!-- PHONE -->
                            <div class="mb-3">
                                <label for="phonenumber" class="form-label">Phone Number</label>                                
                                <input type="tel" id="phonenumber" name="phonenumber" class="form-control custom-input">
                            </div>

                            <!-- ADDRESS -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>                                
                                <input type="text" id="address" name="address" class="form-control custom-input" required>
                            </div>
                            
                            <!-- PASSWORD -->
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control custom-input" required>
                            </div>

                            <!-- SUBMIT -->
                            <button type="submit" class="btn btn-primary w-100 custom-btn">Register</button>
                            
                            <div class="text-center mt-4 registration-link">
                                <p>Already have an account? <a href="login.php">Login</a></p>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div> 
    </div>
</body>
</html>

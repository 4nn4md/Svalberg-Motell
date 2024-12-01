<?php 
ob_start();
session_start(); // Start the session to access $_SESSION variables
?>
<!DOCTYPE html>
<html lang="no-nb">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svalberg Motell</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="../../assets/css/styles1.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand me-auto" href="#">Svalberg Motell</a>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Svalberg Motell</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/Svalberg-Motell/www/index1.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="/Svalberg-Motell/www/about.php">About the Motel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="/Svalberg-Motell/www/rooms.php">Our Rooms</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="#">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="#">Contact</a>
                        </li>
                        <?php if (isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
                            <li class="nav-item">
                                <a class="nav-link mx-lg-2" href="/Svalberg-Motell/www/page/user/user_profile_two.php">My Profile</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Check if user is logged in and show Login or Logout accordingly -->
            <?php if (isset($_SESSION['email'])): ?>
                <!-- Show logout button if the user is logged in -->
                <a href="/Svalberg-Motell/www/logout.php" class="login-button">Logout</a>
            <?php else: ?>
                <!-- Show login button if the user is not logged in -->
                <a href="/Svalberg-Motell/www/login.php" class="login-button">Login</a>
            <?php endif; ?>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <!-- End Navbar -->

    <!-- Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>

<?php 
//session_start(); // Start sessionen
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/ValidateController.php");
print_r($_SESSION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store all relevant data in the session
    $_SESSION['location'] = $_POST['location'];  // Save the location selected by the user
    $_SESSION['checkin'] = $_POST['checkin'];    // Save the check-in date chosen by the user
    $_SESSION['checkout'] = $_POST['checkout'];  // Save the check-out date chosen by the user
    $_SESSION['adults'] = $_POST['adults'];      // Save the number of adults selected by the user
    $_SESSION['children'] = $_POST['children'];  // Save the number of children selected by the user

    // Create a new instance of the validation class
    $validation = new Validering();

    // Validate the check-in and check-out dates
    $validation->validereDato($_POST['checkin'], $_POST['checkout']);  // Ensure that the check-out date is after the check-in date

    // Validate that no fields are empty
    $validation->emptyInput($_POST['location'], $_POST['checkin'], $_POST['checkout'], $_POST['adults']);  // Make sure essential fields are not empty

    // Ensure that at least one adult is selected
    $validation->hasToHaveAdult($_POST['adults']);  // Check that the number of adults is greater than zero

    // Retrieve any validation error messages
    $errorMessages = $validation->getValidateError();  // Get the error messages from the validation process

    // If no errors, redirect to the booking page
    if (empty($errorMessages)) {
        header("Location: page/user/booking_1.php");  
        exit();  
    }
}
?>


<!DOCTYPE html>
<html lang="no-nb">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svalberg Motell</title>
    <?php //include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="http://localhost/Svalberg-Motell/www/assets/css/styles1.css" rel="stylesheet">
</head>
<!-- action="page/user/booking_1.php" -->
<body>
<!--First Section-->
<section class=firstSection>
    <div class="mainImage">
        <img src="assets/image/index1/Hjem1.jpg" class="d-block w-100" alt="fjordenTilSvalbergMotel">
        <div class="booking-form">
            <?php if (!empty($errorMessages)): ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong>
                    <ul>
                        <?php foreach ($errorMessages as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="" method="POST" class="d-flex justify-content-center">
                <div class="row w-100 no-gutters"> <!-- Remove gutters for spacing -->
                    <div class="col-md-2">
                        <label for="location" class="bold-label">Location</label>
                        <select class="form-control" id="location" name="location">
                            <option value="" disabled selected>Choose location..</option>
                            <option value="Kristiansand">Kristiansand</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="checkin" class="bold-label">Arrival</label>
                        <input type="date" id="checkin" name="checkin" class="form-control" value="<?php echo htmlspecialchars($checkin ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="checkout" class="bold-label">Departure</label>
                        <input type="date" id="checkout" name="checkout" class="form-control" value="<?php echo htmlspecialchars($checkout ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="adults" class="bold-label">Adults</label>
                        <input type="number" id="adults" name="adults" class="form-control" value="<?php echo htmlspecialchars($adults ?? '1'); ?>" min="1">
                    </div>
                    <div class="col-md-2">
                        <label for="children" class="bold-label">Children</label>
                        <input type="number" id="children" name="children" class="form-control" value="<?php echo htmlspecialchars($children ?? '0'); ?>" min="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end"> <!-- Align button vertically -->
                        <button type="submit" id="submit" class="btn msearch-btn w-100" id="#MBtn">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- End of First Section-->

<!-- Second Section-->
<section class="secondSection">
    <div class="container text-center">
        <h2>Welcome to Svalberg Motell!</h2>
        <p>- your home by the sea and the mountains.</p>
    </div>

    <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/image/index1/Hjem4.jpg" class="d-block w-100" alt="oceanAndRocksInYellowLight">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Experience the peace</h5>
                    <p>Unique nature experiences are waiting for you.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/image/index1/Hjem3.jpg" class="d-block w-100" alt="blueOceanAndRocks">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Family vacation</h5>
                    <p>Create memories with those you love.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/image/index1/Hjem5.jpg" class="d-block w-100" alt="forestWithLight">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Nature experiences</h5>
                    <p>Discover the beautiful nature around us.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<!-- End of Second Section -->

<!-- Third Section -->
<section class="thirdSection">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <img src="assets/image/index1/Hjem7.jpg" class="img-fluid" alt="peacefulWomanHoldingMugLookingOutsideFromBed">
            </div>
            <div class="col-md-6">
                <h3>Discover our facilities</h3>
                <p>We offer comfortable rooms, outdoor activities, and amazing experiences. Our motel is the perfect place for relaxation and adventure</p>
                <a href="error.php">
                    <button class="btn custom-btn mt-3">Read more</button>
                </a>
            </div>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3>An unforgettable experience</h3>
                <p>We have a wide range of activities for the whole family. Whatever you're looking for, you'll find it here.</p>
                <a href="error.php">
                    <button class="btn custom-btn mt-3">Read more</button>
                </a>

            </div>
            <div class="col-md-6">
                <img src="assets/image/index1/Hjem5.jpg" class="img-fluid" alt="forestWithLight">
            </div>
        </div>
    </div>
</section>
<!-- End of Third Section-->
</body>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>

</html>
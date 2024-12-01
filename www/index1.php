<?php 
// session_start(); // Start session
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/ValidateController.php");
print_r($_SESSION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $location = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);
    $checkin = filter_var($_POST['checkin'], FILTER_SANITIZE_STRING);
    $checkout = filter_var($_POST['checkout'], FILTER_SANITIZE_STRING);
    $adults = filter_var($_POST['adults'], FILTER_SANITIZE_NUMBER_INT);
    $children = filter_var($_POST['children'], FILTER_SANITIZE_NUMBER_INT);

    // Store sanitized data in session
    $_SESSION['location'] = $location;
    $_SESSION['checkin'] = $checkin;
    $_SESSION['checkout'] = $checkout;
    $_SESSION['adults'] = $adults;
    $_SESSION['children'] = $children;

    $validation = new Validering();

    // Validate the sanitized data
    $validation->validereDato($checkin, $checkout);
    $validation->emptyInput($location, $checkin, $checkout, $adults);
    $validation->hasToHaveAdult($adults);

    $errorMessages = $validation->getValidateError();
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
    <title>Svalberg Motel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="http://localhost/Svalberg-Motell/www/assets/css/styles1.css" rel="stylesheet">
</head>

<body>
<!-- First Section -->
<section class="firstSection">
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
                        <label for="location" class="bold-label">Lokasjon</label>
                        <select class="form-control" id="location" name="location">
                            <option value="" disabled selected>Velg lokasjon..</option>
                            <option value="Kristiansand">Kristiansand</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="checkin" class="bold-label">Ankomst</label>
                        <input type="date" id="checkin" name="checkin" class="form-control" value="<?php echo htmlspecialchars($checkin ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="checkout" class="bold-label">Avreise</label>
                        <input type="date" id="checkout" name="checkout" class="form-control" value="<?php echo htmlspecialchars($checkout ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="adults" class="bold-label">Antall voksne</label>
                        <input type="number" id="adults" name="adults" class="form-control" value="<?php echo htmlspecialchars($adults ?? '1'); ?>" min="1">
                    </div>
                    <div class="col-md-2">
                        <label for="children" class="bold-label">Antall barn</label>
                        <input type="number" id="children" name="children" class="form-control" value="<?php echo htmlspecialchars($children ?? '0'); ?>" min="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end"> <!-- Align button vertically -->
                        <button type="submit" id="submit" class="btn msearch-btn w-100" id="#MBtn">Søk</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- End of First Section -->

<!-- Second Section -->
<section class="secondSection">
    <div class="container text-center">
        <h2>Welcome to Svalberg Motel!</h2>
        <p>- your home between the sea and mountains.</p>
    </div>

    <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/image/index1/Hjem4.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Experience the peace</h5>
                    <p>Unique nature experiences are waiting for you.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/image/index1/Hjem3.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Family vacation</h5>
                    <p>Create memories with those you love.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/image/index1/Hjem5.jpg" class="d-block w-100" alt="...">
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
                <img src="assets/image/index1/Hjem7.jpg" class="img-fluid" alt="Image description">
            </div>
            <div class="col-md-6">
                <h3> Discover our facilities</h3>
                <p>We offer comfortable rooms, outdoor activities, and amazing experiences. Our motel is the perfect place for relaxation and adventure.</p>
                <button class="btn custom-btn mt-3">Les mer</button>
            </div>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3>An unforgettable experience</h3>
                <p>We have a wide range of activities for the whole family. Whatever you're looking for, you'll find it here.</p>
                <button class="btn custom-btn mt-3">Les mer</button>
            </div>
            <div class="col-md-6">
                <img src="assets/image/index1/Hjem5.jpg" class="img-fluid" alt="Image description">
            </div>
        </div>
    </div>
</section>
<!-- End of Third Section -->

<?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>

</body>

</html>

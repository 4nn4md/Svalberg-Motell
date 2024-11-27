<?php 
//session_start(); // Start sessionen


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    session_destroy(); // Ødelegger sessionen og fjerner alle session-variabler

    // Start sessionen på nytt etter session_destroy, hvis du trenger å bruke session etterpå
    session_start();

    // Lagre alle relevante data i session
    $_SESSION['location'] = $_POST['location'];
    $_SESSION['checkin'] = $_POST['checkin'];
    $_SESSION['checkout'] = $_POST['checkout'];
    $_SESSION['adults'] = $_POST['adults'];
    $_SESSION['children'] = $_POST['children'];

    // Debugging: Vis session-innholdet etter at dataene er lagret
    // Dette vil vise innholdet i session etter at skjemaet er sendt

    // Hvis du ønsker å omdirigere til booking_1.php etter skjemaet er sendt, kan du bruke:
    // header('Location: booking_1.php');
    // exit();
}
?>


<!DOCTYPE html>
<html lang="no-nb">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svalberg Motell</title>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?>
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
            <form action="page/user/booking_1.php" method="POST" class="d-flex justify-content-center">
                <div class="row w-100 no-gutters"> <!-- Remove gutters for spacing -->
                    <div class="col-md-2">
                        <label for="location" class="bold-label">Lokasjon</label>
                        <select class="form-control" id="location" name="location" required>
                            <option value="" disabled selected>Velg lokasjon..</option>
                            <option value="Kristiansand">Kristiansand</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="checkin" class="bold-label">Ankomst</label>
                        <input type="date" id="checkin" name="checkin" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="checkout" class="bold-label">Avreise</label>
                        <input type="date" id="checkout" name="checkout" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="adults" class="bold-label">Antall voksne</label>
                        <input type="number" id="adults" name="adults" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-2">
                        <label for="children" class="bold-label">Antall barn</label>
                        <input type="number" id="children" name="children" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end"> <!-- Align button vertically -->
                        <button type="submit" id="submit" class="btn msearch-btn w-100" id="#MBtn">Søk</button>
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
        <h2>Velkommen til Svalberg Motell!</h2>
        <p>- ditt hjem ved havet og fjellene.</p>
    </div>

    <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/image/index1/Hjem4.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Opplev roen</h5>
                    <p>Unike naturopplevelser venter på deg.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/image/index1/Hjem3.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Familieferie</h5>
                    <p>Lag minner med de du elsker.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/image/index1/Hjem5.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Naturopplevelser</h5>
                    <p>Oppdag den vakre naturen rundt oss.</p>
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
                <h3>Oppdag våre fasiliteter</h3>
                <p>Vi tilbyr komfortable rom, utendørs aktiviteter og fantastiske opplevelser. Vårt motell er det perfekte stedet for avslapning og eventyr.</p>
                <button class="btn custom-btn mt-3">Les mer</button>
            </div>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3>En uforglemmelig opplevelse</h3>
                <p>Vi har et bredt utvalg av aktiviteter for hele familien. Uansett hva du leter etter, vil du finne det her.</p>
                <button class="btn custom-btn mt-3">Les mer</button>
            </div>
            <div class="col-md-6">
                <img src="assets/image/index1/Hjem5.jpg" class="img-fluid" alt="Image description">
            </div>
        </div>
    </div>
</section>
<!-- End of Third Section-->
</body>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>

</html>
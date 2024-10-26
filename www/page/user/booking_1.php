<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");
$sql = "SELECT * FROM oversikt_rom WHERE ledig = 'ja'"; // Juster SQL-spørringen basert på din databasetabell
$result = $conn->query($sql);

?>

<html>
    <head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?>    </head>
    </head>
    <body>
        <div class="card w-75" style="margin: 100px auto 20px auto;">
        <div class="row g-0">
            <div class="col-md-4">
                <img
                    src="http://localhost/Svalberg-Motell/www/assets/image/standardrom.avif"
                    class="img-fluid rounded-start"
                    alt="Standardrom"
                />
            </div>
            <div class="col-md-8">
                <div class="card-body d-flex flex-column justify-content-between" style="height: 100%;">
                    <div>
                        <h5 class="card-title">Standardrom</h5>
                        <p class="card-text">
                            Vårt standardrom er perfekt for en komfortabel overnatting. Rommet har moderne møbler og en koselig atmosfære.
                        </p>
                    </div>
                    <div class="row align-items-end mt-auto">
                        <div class="col-7">
                            <p class="card-text mb-0">
                                <strong>Etasje:</strong> 2.<br>
                                <strong>Nærhet til heis:</strong> Ja.<br>
                            </p>
                        </div>
                        <div class="col text-end"> <!-- Justerer pris til høyre -->
                            <p class="card-text mb-0">
                                <strong>Pris:</strong> 950 NOK per natt.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
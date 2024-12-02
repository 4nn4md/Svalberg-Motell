<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); 
//error_log("SESSION at start of booking_2.php: " . print_r($_SESSION, true));

redirectAction('login', 'login.php');
redirectAction('registrer', 'register.php');

?>

<html>
    <head>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?>
    </head>
    <body>
        <div class="container w-75" style="margin-top: 100px;">
            <div class="position-relative m-4">
                <div class="progress" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button" class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">1</button>
                <button type="button" class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-secondary rounded-pill" style="width: 2rem; height:2rem;">2</button>
    </body>
                <button type="button" class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-secondary rounded-pill" style="width: 2rem; height:2rem;">3</button>
            </div>
        <!-- <div class="progress">
                <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div> -->
            <div class="card w-75 bg-white" style="margin: 50px auto 25px auto;">
                <h1 class="text-center" style="margin: 50px auto 25px auto;">How do you want to proceed?</h1>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <a href="booking_2.php?action=registrer" class="btn msearch-btn w-50 mb-2">Become a member</a>
                    <a href="booking_2.php?action=login" class="btn msearch-btn w-50 mb-2">Log in</a>
                    <a href="booking_3.php" class="btn msearch-btn w-50 mb-5">Continue without logging in</a>
                </div>
            </div>
        </div>
    </body>
    <footer>
        <div style="margin-top: 50px;">
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>
        </div>
    </footer>
</html>

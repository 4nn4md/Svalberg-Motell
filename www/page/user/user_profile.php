<html>
    <head>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header.php"); ?>    </head>

    </head>
    <body>
        <div class="container-fluid w-100 m-0" style="background-color: #FFFFFF;">
            <div class="row d-flex justify-content-end">
                <div class="col-3 d-flex justify-content-end align-items-center">
                    <img class="img-thumbnail rounded-circle" src="http://localhost/Svalberg-Motell/assets/image/icons8-cat-64.png">
                </div>
                <div class="col-3 align-self-center">
                    <p><?php echo "navn"?></p>
                    <p><?php echo "telefon"?></p>
                    <p><?php echo "epost"?></p>
                </div>
                <div class="col-6 d-flex flex-column ms-auto position-relative">
                    <div class="sylinder" style="background-color: #B3D8F2;">
                        <div class="d-flex" id="tracker">
                            <div class="border  bg-white" id="point" style="border-radius: 50px 0 0 50px;">
                                <img src="http://localhost/Svalberg-Motell/assets/image/icons8-heart-64.png" style="width: 30px;">
                            </div>
                            <div class="border  bg-white" id="point" style="border-radius: 0 50px 50px 0;">
                                <p class="align-items-center"><?php echo 3?></p>
                            </div>
                        </div>
                    </div>
                    <div class="sylinder" style="background-color: #60A2D1;">
                        <div class="d-flex" id="tracker">
                            <div class="border  bg-white" id="point" style="border-radius: 50px 0 0 50px;">
                                <img src="http://localhost/Svalberg-Motell/assets/image/icons8-coin-96.png">
                            </div>
                            <div class="border  bg-white" id="point" style="border-radius: 0 50px 50px 0;">
                                <p class="align-items-center"><?php echo 3?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<!-- Denne html koden er hentet fra denne nettsiden: https://getbootstrap.com/docs/4.0/components/navs/ -->
<!-- Har gjort endringer for å tilpasse prosjektets behov-->
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link active" id="reservasjon-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Mine reservasjoner</a>
            <a class="nav-link" id="historikk-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Historikk</a>
        </div>
        <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="reservasjon-tab">hei</div>
            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="historikk-tab">hade</div>
        </div>



<!-- bootstrap javascript for å få koden over fungerende -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>




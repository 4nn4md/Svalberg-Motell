<html>
    <head>
    <?php include("../../assets/include/header.php");?>
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
                            <div class="border  bg-white" id="point" style="border-radius: 10px 0 0 10px;">
                                <img src="http://localhost/Svalberg-Motell/assets/image/icons8-cat-64.png">
                            </div>
                            <div class="border  bg-white" id="point" style="border-radius: 0 10px 10px 0;">
                                <p class="align-items-center"><?php echo 3?></p>
                            </div>
                        </div>
                    </div>
                    <div class="sylinder" style="background-color: #60A2D1;">
                        <div class="d-flex" id="tracker">
                            <div class="border  bg-white" id="point" style="border-radius: 10px 0 0 10px;">
                                <img src="http://localhost/Svalberg-Motell/assets/image/icons8-user-profile-64.png">
                            </div>
                            <div class="border  bg-white" id="point" style="border-radius: 0 10px 10px 0;">
                                <p class="align-items-center"><?php echo 3?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>




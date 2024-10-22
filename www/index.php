<html>
    <head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header.php"); ?>    </head>
    <body>
        <div class="container-fluid p-0 mt-0 mb-0 search">
            <form class="d-flex justify-content-center mb-0">
                <div class="row mb-0 pb-4 pt-4">
                    <div class="col-3">
                        <label for="location">Hvor ønsker du å bo?</label><br>
                        <select class="form-control w-100" id="location">
                            <option>Velg lokasjon..</option>
                            <option>Kristiansand</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="date-ankomst">Ankomst</label><br>
                        <input type="date" id="date-ankomst" class="form-control">
                    </div>
                    <div class="col">
                        <label for="date-avreise">Avreise</label><br>
                        <input type="date" id="date-avreise" class="form-control">
                    </div>
                    <div class="col">
                        <label for="voksne">Antall voksne</label><br>
                        <input type="numbers" id="voksne" class="form-control">
                    </div>
                    <div class="col">
                        <label for="barn">Antall barn</label><br>
                        <input type="numbers" id="barn" class="form-control">
                    </div>
                    <div class="col">
                        <label for="submit" class="invisible-label">Søk</label><br>
                        <button type="submit" id="submit" class="btn btn-primary w-100">Søk</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="container-fluid p-0 mt-0">
        <div class="row">
            <img class="img-fluid w-100 p-0 m-0 cropped-img" src="assets/image/front-image2.jpeg">
        </div>
</div>
    </body>
</html>






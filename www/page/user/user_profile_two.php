<html>
    <head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?>    </head>
    </head>
    <body>
    <section style="margin-bottom: 0;">
      <div class="container py-3">
        <div class="row d-flex justify-content-center">
          <div class="col-12">
            <div class="card">
              <div class="rounded-top text-white d-flex flex-row" style="background-color: #0A3D62; height:200px;">
                <div class="ms-4 mt-5 d-flex flex-column" style="width: 150px;">
                  <img src="http://localhost/Svalberg-Motell/www/assets/image/profile-pic.webp"
                    alt="Generic placeholder image" class="img-fluid img-thumbnail mt-4 mb-2"
                    style="width: 150px; z-index: 1">
                  <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-dark text-body edit-profile" data-mdb-ripple-color="dark" style="z-index: 1;">
                    Edit profile
                  </button>
                </div>
                <div class="ms-3" style="margin-top: 130px;">
                  <h5 style="color: #B3D8F2;"><?php echo "Fornavn" . " " . "Etternavn";?></h5>
                </div>
              </div>
              <div class="p-2 text-black bg-body-tertiary mt-0">
                <div class="d-flex justify-content-end text-center py-1 text-body">
                  <div>
                    <p class="mb-1 h5"><?php echo 500;?></p>
                    <p class="small text-muted mb-0">Poeng</p>
                  </div>
                  
                </div>
              </div>
              <div class="card-body p-4 text-black mb-0">
                <div class="mb-5  text-body">
                  <p class="lead fw-normal mb-1">Kontakt informasjon</p>
                  <div class="p-0 bg-body-tertiary">
                    <p class="font-italic mb-1"><?php echo "Telefon nummer";?></p>
                    <p class="font-italic mb-0"><?php echo "Epost";?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<!-- Denne html koden er hentet fra denne nettsiden: https://getbootstrap.com/docs/4.0/components/navs/ -->
<!-- Har gjort endringer for å tilpasse prosjektets behov-->
<div class="container " style="margin-bottom: 200px;">
  <div class="row">
    <div class="col-12">
      <div class="nav col-4 nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          <a class="nav-link active" id="reservasjon-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true" >Mine reservasjoner</a>
          <a class="nav-link" id="historikk-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false" >Historikk</a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="tab-content" id="v-pills-tabContent">
          <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="reservasjon-tab">
            
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Ankomst</th>
                  <th scope="col">Avreise</th>
                  <th scope="col">Totalpris</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                <tr class="clickable-row" data-toggle="collapse" data-target=".details-row1">
                  <td scope="row"><?php echo "20-5-24";?></td>
                  <td><?php echo "20-5-24";?></td>
                  <td><?php echo 1200;?></td>
                  <td>
                    <button class="btn btn-primary">Vis romdetaljer</button>
                  </td>
                </tr>
                <tr class="collapse details-row details-row1">
                  <td colspan="4">
                    <div class="card card-body">
                      <?php echo "test test tets";?>
                    </div>
                  </td>
                </tr>
                
              </tbody>
            </table>

          </div>
          <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="historikk-tab">
            
<!-- Hentet table elementet fra https://getbootstrap.com/docs/5.0/content/tables/ -->
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Rom type</th>
                  <th scope="col">Navn</th>
                  <th scope="col">Ankomst</th>
                  <th scope="col">Avreise</th>
                  <th scope="col">Antall personer</th>
                  <th scope="col">Totalpris</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th scope="row">1</th>
                  <td>Enkel rom</td>
                  <td><?php echo "Navn Etternavn";?></td>
                  <td><?php echo "10-10-24";?></td>
                  <td><?php echo "13-10-24";?></td>
                  <td><?php echo 2;?></td>
                  <td><?php echo 1200;?></td>
                </tr>
                
              </tbody>
            </table>

          </div>
      </div>
    </div>
  </div>

</div>

<!-- bootstrap javascript for å få koden over fungerende -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
  </body>
</html>
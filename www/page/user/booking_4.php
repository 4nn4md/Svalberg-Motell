<?php 
session_start();
// Include function and database file
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php"); 

//find information from the session and give them a new variable name
/*if (isset($_SESSION['room'])) {
    $room_info = $_SESSION['room'];
    $total_price = $room_info['total_price'] ?? '';
    $adults = (int) $room_info['adults'] ?? 0;
    $children = (int) $room_info['children'] ?? 0;
    $base_price = $room_info['base-price'] ?? '';
}

if (isset($_SESSION['username'])) {
    // Hent user_id fra databasen basert på username fra session
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = :username");
    $stmt->execute([':username' => $_SESSION['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Sett user_id hvis brukeren er funnet, ellers sett til NULL
    $user_id = $user['user_id'];
} else {
    // Hvis brukeren ikke er logget inn, sett user_id til NULL
    $user_id = NULL;
}

$sql = "INSERT IGNORE INTO booking 
        (booking_id, user_id, room_id, 'name', email, tlf, check_in_date, check_out_date, number_of_quests, payment_id) 
        VALUES 
        (:booking_id, :user_id, :room_id, :name, :email, :tlf, :check_in_date, :check_out_date, :number_of_quests, :payment_id )"; 

$sql = "INSERT IGNORE INTO payment 
        (payment_id ,booking_id, amount, payment_date, payment_method, 'status') 
        VALUES 
        (:payment_id, :booking_id, :amount, :payment_date, :payment_method, :status)"; 


$q = $pdo->prepare($sql);

$q->bindParam(':user_id', $user_id, PDO::PARAM_STR);
$q->bindParam(':room_id', $room_id, PDO::PARAM_STR);
$q->bindParam(':name', $name, PDO::PARAM_STR);
$q->bindParam(':tlf', $tlf, PDO::PARAM_STR);
$q->bindParam(':check_in_date', $check_in_date, PDO::PARAM_INT);
$q->bindParam(':check_out_date', $check_out_date, PDO::PARAM_STR);
$q->bindParam(':number_of_quests', $number_of_quests, PDO::PARAM_STR);
$q->bindParam(':payment_id', $payment_id, PDO::PARAM_STR);


$firstname = "Luke";
$lastname = "Skywalker";
$email = "luke@uia.no";
$cell = 99887766;
$zip = 4516;
$city = "Mandal";

try {
    $q->execute();
} catch (PDOException $e) {
    echo "Feil ved tilkobling: " . $e->getMessage() . "<br>"; // Kun for læring, bør logges!
}
//$q->debugDumpParams(); 

if($pdo->lastInsertId() > 0) {
    echo "Data er lagt til, identifisert ved UID " . $pdo->lastInsertId() . ".";
} else {
    echo "Data ble ikke lagt til databasen. Vennligst forsøk igjen senere.";
}
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['epost'];
    $country_code = $_POST['country_code'];
    $mobile = $_POST['mobile'];
    $message = $_POST['message'];
    $choosePayment = $_POST['choosePayment'];
    $total_price = $_SESSION['room']['total_price']; // Bruk total_price fra session

    // Kombiner landskode og mobilnummer til ett telefonnummer
    $phone = $country_code . $mobile;

    try {
        // Start transaksjon
        $pdo->beginTransaction();

        // Sett inn i booking-tabellen
        $stmt = $pdo->prepare("INSERT INTO booking (user_id, room_id, name, email, tlf, check_in_date, check_out_date, number_of_guests) 
                               VALUES (:user_id, :room_id, :name, :email, :tlf, :check_in_date, :check_out_date, :number_of_guests)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':room_id' => $_SESSION['room']['room_id'],
            ':name' => $fname . " " . $lname,
            ':email' => $email,
            ':tlf' => $phone, // Lagre kombinert telefonnummer her
            ':check_in_date' => $_SESSION['room']['check_in_date'],
            ':check_out_date' => $_SESSION['room']['check_out_date'],
            ':number_of_guests' => $_SESSION['room']['adults'] + $_SESSION['room']['children']
        ]);

        // Hent booking_id for den nye bookingen
        $booking_id = $pdo->lastInsertId();

        // Sett inn i payment-tabellen
        $stmt = $pdo->prepare("INSERT INTO payment (booking_id, amount, payment_date, payment_method, status) 
                               VALUES (:booking_id, :amount, NOW(), :payment_method, :status)");
        $stmt->execute([
            ':booking_id' => $booking_id,
            ':amount' => $total_price,
            ':payment_method' => $choosePayment,
            ':status' => 'Paid' // eller 'Pending' avhengig av betaling
        ]);

        // Fullfør transaksjon
        $pdo->commit();

        if (isset($_SESSION['username'])) {
            header('Location: /profil.php'); // Endre til riktig URL for profilsiden
            exit(); // Sørg for at ingen ytterligere kode blir kjørt
        } else {
            header('Location: /index.php'); // Endre til riktig URL for hovedsiden
            exit(); // Sørg for at ingen ytterligere kode blir kjørt
        }
       
    } catch (Exception $e) {
        // Rull tilbake ved feil
        $pdo->rollBack();
        echo "Feil: " . $e->getMessage();
    }
}



?>






<html>
    <head>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?> <!-- include header -->
    </head>
    <body>
        <div class="container w-75" style="margin-top: 100px; height: auto;">
            <div class="position-relative m-4">
                <div class="progress" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button" class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">1</button>
                <button type="button" class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">2</button>
                <button type="button" class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">3</button>
            </div>
            
            <div class="card bg-white" style="margin: 50px auto 25px auto;">
                <h1 class="text-center" style="margin: 50px auto 25px auto;">Kontakt og betalingsdetaljer</h1>
                <form method="POST" action="" class="row g-3 w-75" style="margin: 0 auto 25px auto;">    
                    <div class="col-md-6">
                        <label for="fname" class="form-label">Fornavn</label>
                        <input type="text" class="form-control" id="fname" name="fname">
                    </div>
                    <div class="col-md-6">
                        <label for="lname" class="form-label">Etternavn</label>
                        <input type="text" class="form-control" id="lname" name="lname">
                    </div>
                    <div class="col-md-6">
                        <label for="epost" class="form-label">Epost</label>
                        <input type="text" class="form-control" id="epost" name="epost">
                    </div>
                    <div class="col-md-2">
                        <label for="country_code" class="form-label">Landskode</label>
                        <div class="input-group">
                            <span class="input-group-text">+</span>
                            <input type="tel" class="form-control" id="country_code" name="country_code" placeholder="47">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="mobile" class="form-label">Mobil</label>
                        <input type="number" class="form-control" id="mobile" name="mobile">
                    </div>
                    <div class="col-12">
                        <label for="message" class="form-label">Beskjed</label>
                        <textarea class="form-control" id="message" name="message" placeholder="..." rows="4"></textarea>
                    </div>
                    <div class="col-12">
                        <h3>Velg betalingsmetode</h3>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="" id="flexCheckDefault" name="choosePayment" >
                        <label class="form-check-label" for="flexCheckDefault">Vips</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="" id="flexCheckDefault" name="choosePayment">
                        <label class="form-check-label" for="flexCheckDefault">Kort</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="" id="flexCheckChecked" name="choosePayment">
                        <label class="form-check-label" for="flexCheckChecked">Faktura</label>
                    </div>
                    <div class="col-12">
                        <div >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Pris for rom</th>
                                        <td class="text-end" scope="col"><?php echo htmlspecialchars($base_price) . " NOK";?></td>              
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="row"><?php echo "MVA 12%" . " Nok";?></td>
                                        <td class="text-end"><?php echo calculateMVA($base_price) . " NOK";?>
                                            <p>MVA er inkludert i prisen</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Total</th>
                                        <td class="text-end"><?php echo $total_price . " Nok";?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col d-flex justify-content-end">
                                <button type="submit" class="btn msearch-btn w-50 mb-2">Godkjenn & betal nå</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>
    <footer>
        <div style="margin-top: 50px;">
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>
        </div>
    </footer>
</html>
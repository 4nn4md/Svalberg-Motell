<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/ValidateController.php");

$user_points = 0;
$discount_data = ['price' => $_SESSION['selected_room']['total_price'], 'pointsLeft' => 0];

if (isset($_SESSION['email'])) {
    $username = sanitize($_SESSION['email']);
    $stmt = $pdo->prepare("SELECT point FROM swx_users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $user_points = $user['point'];
        $discount_data = calculatePriceWithPoints($_SESSION['selected_room']['total_price'], $user_points);
    }
}

$discounted_price = $discount_data['price']; // Ny pris etter rabatt
$discount_amount = $_SESSION['selected_room']['total_price'] - $discounted_price; // Beregn rabatten
$points_left = $discount_data['pointsLeft'];

$error_message = "";

// Hovedlogikk for POST-forespørsel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Sjekk om betalingsmetode er valgt
        if (!isset($_POST['choosePayment']) || empty($_POST['choosePayment'])) {
            throw new Exception("Du må velge en betalingsmetode.");
        }

        // Hent data fra skjemaet
        $fname = $_POST['fname'] !== null ? sanitize($_POST['fname']) : null;
        $lname = $_POST['lname'] !== null ? sanitize($_POST['lname']) : null;
        $email = $_POST['epost'] !== null ? sanitize($_POST['epost']) : null;
        $country_code = $_POST['country_code'] !== null ? sanitize($_POST['country_code']) : null;
        $mobile = $_POST['mobile'] !== null ? sanitize($_POST['mobile']) : null;
        $message = $_POST['message'] !== null ? sanitize($_POST['message']) : null;
        $choosePayment = $_POST['choosePayment'] !== null ? sanitize($_POST['choosePayment']) : null;

        $total_price = $_SESSION['selected_room']['total_price'];
        $room_id = $_SESSION['selected_room']['room_id'];
        $phone = $country_code !== null && $mobile !== null ? $country_code . $mobile : null;

        $final_price = isset($_POST['final_price']) ? sanitize($_POST['final_price']) : $total_price;

        // Valider inputs
        $validation = new Validering();
        $validation->validereFornavn($fname);
        $validation->validereEtternavn($lname);
        $validation->validereEpost($email);
        $validation->validereMobilnummer($country_code, $mobile);
        $validation->validereMessage($message);

        if (!empty($validation->getValidateError())) {
            throw new Exception("Validation failed: " . implode("<br>", $validation->getValidateError()));
        }

        // Oppdater poeng basert på bruk av poeng eller ikke
        $user_id = null;
        if (isset($_SESSION['email'])) {
            $username = $_SESSION['email'] !== null ? sanitize($_SESSION['email']) : null;
            $stmt = $pdo->prepare("SELECT user_id, point FROM swx_users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $user_id = $user['user_id'];

                if (isset($_POST['usePoints']) && $_POST['usePoints'] === 'on') {
                    // Trekk fra poeng brukt
                    $current_points = $user['point'];
                    $points_used = ceil($discount_amount / 0.25); // Forutsetter at 1 poeng = 0.25 NOK
                    $new_points = max($current_points - $points_used, 0);
                    $stmt = $pdo->prepare("UPDATE swx_users SET point = :point WHERE user_id = :user_id");
                    $stmt->execute([
                        ':point' => $new_points,
                        ':user_id' => $user_id
                    ]);
                } else {
                    // Oppdater poeng hvis poeng ikke brukes
                    $current_points = $user['point'];
                    $new_points = $current_points + $total_price;
                    $stmt = $pdo->prepare("UPDATE swx_users SET point = :point WHERE user_id = :user_id");
                    $stmt->execute([
                        ':point' => $new_points,
                        ':user_id' => $user_id
                    ]);
                }
            }
        }

        // Sett inn betaling
        $stmt = $pdo->prepare("INSERT INTO swx_payment (amount, payment_method, status) 
                               VALUES (:amount, :payment_method, :status)");
        $stmt->execute([
            ':amount' => $final_price,
            ':payment_method' => $choosePayment,
            ':status' => 'Completed'
        ]);
        $payment_id = $pdo->lastInsertId();

        // Sett inn booking
        $stmt = $pdo->prepare("INSERT INTO swx_booking (user_id, room_id, payment_id, name, email, tlf, comments, check_in_date, check_out_date, number_of_guests) 
                               VALUES (:user_id, :room_id, :payment_id, :name, :email, :tlf, :comments, :check_in_date, :check_out_date, :number_of_guests)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':room_id' => $room_id,
            ':payment_id' => $payment_id,
            ':name' => $fname . " " . $lname,
            ':email' => $email,
            ':tlf' => $phone,
            ':comments' => $message,
            ':check_in_date' => $_SESSION['checkin'],
            ':check_out_date' => $_SESSION['checkout'],
            ':number_of_guests' => $_SESSION['adults'] + $_SESSION['children']
        ]);

        // Generer faktura om nødvendig
        if ($choosePayment === 'Invoice') {
            require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/generate_invoice.php");
            $invoiceFileName = generateInvoice(
                $fname,
                $lname,
                $_SESSION['selected_room']['type_name'],
                $final_price,
                $payment_id
            );

            $stmt = $pdo->prepare("UPDATE swx_payment SET invoice_path = :invoice_path WHERE payment_id = :payment_id");
            $stmt->execute([
                ':invoice_path' => $invoiceFileName,
                ':payment_id' => $payment_id
            ]);
        }

        $pdo->commit();
        header("Location: confirmation.php?payment_id=$payment_id");
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_error($e);
        echo "<br>Sorry, something went wrong. Please try again later.<br>";
        echo "<pre>" . $e->getMessage() . "</pre>"; // Midlertidig debugging
        exit();
    }
}
?>


<html>
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
                        <input type="tel" class="form-control" id="mobile" name="mobile">
                    </div>
                    <div class="col-12">
                        <label for="message" class="form-label">Beskjed</label>
                        <textarea class="form-control" id="message" name="message" placeholder="..." rows="4"></textarea>
                    </div>
                    <div class="col-12">
                        <h3>Brukerpoeng</h3>
                        <p>Du har <strong id="userPoints"><?php echo $user_points; ?></strong> poeng.</p>
                        <p>Dette gir deg en maksimal rabatt på <strong id="discountAmount"><?php echo number_format($discount_amount, 2); ?></strong> NOK.</p>
                    </div>
                    <div class="col-12">
                        <h3>Velg betalingsmetode</h3>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="usePointsCheckbox" name="usePoints">
                        <label class="form-check-label" for="usePointsCheckbox">Bruk poengene mine</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="Vips" id="flexCheckDefault" name="choosePayment" >
                        <label class="form-check-label" for="flexCheckDefault">Vipps</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="Credit Card" id="flexCheckDefault" name="choosePayment">
                        <label class="form-check-label" for="flexCheckDefault">Kort</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="Invoice" id="flexCheckChecked" name="choosePayment">
                        <label class="form-check-label" for="flexCheckChecked">Faktura</label>
                    </div>
                    <?php if($error_message) { echo "<p style='color: red;'>$error_message</p>";}?>
                    <div class="col-12">
                        <div >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Pris for rom</th>
                                        <td class="text-end" scope="col"><?php echo htmlspecialchars($_SESSION['selected_room']['base_price']) . " NOK";?></td>              
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <!-- uses calculateMVA function -->
                                        <td scope="row"><?php echo "MVA 12%" . " NOK";?></td>
                                        <td class="text-end"><?php echo calculateMVA($_SESSION['selected_room']['base_price']) . " NOK";?>
                                            <p>MVA er inkludert i prisen</p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <th scope="row">Total</th>
                                        <td id="totalPrice" class="text-end"><?php echo htmlspecialchars($_SESSION['selected_room']['total_price']) . " NOK";?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <input type="hidden" id="finalPriceInput" name="final_price" value="<?php echo htmlspecialchars($_SESSION['selected_room']['total_price']); ?>">
                            <div class="col d-flex justify-content-end">
                                <button type="submit" class="btn msearch-btn w-50 mb-2">Godkjenn & betal nå</button>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
        <script>
            document.getElementById('usePointsCheckbox').addEventListener('change', function () {
                const usePoints = this.checked; // Sjekker om "Bruk poengene mine" er valgt
                const discountAmount = parseFloat(<?php echo json_encode($discount_amount); ?>); // Rabatten beregnet i PHP
                const totalPriceElement = document.getElementById("totalPrice"); // Hent totalpris-elementet
                const originalPrice = parseFloat(<?php echo json_encode($_SESSION['selected_room']['total_price']); ?>); // Original totalpris fra PHP

                // Oppdater kun totalprisen basert på om poeng brukes
                if (usePoints) {
                    const newPrice = Math.max(originalPrice - discountAmount, 0).toFixed(2); // Trekk rabatten fra totalprisen
                    totalPriceElement.innerText = `${newPrice} NOK`; // Oppdater totalprisen
                    document.getElementById('finalPriceInput').value = newPrice; // Oppdater skjult input for server
                } else {
                    totalPriceElement.innerText = `${originalPrice.toFixed(2)} NOK`; // Tilbakestill til originalpris
                    document.getElementById('finalPriceInput').value = originalPrice.toFixed(2); // Oppdater skjult input for server
                }
            });
        </script>
    </body>
    <footer>
        <div style="margin-top: 50px;">
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>
        </div>
    </footer>
</html>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include function and database file
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/ValidateController.php");
print_r($_SESSION);
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['epost'];
    $country_code = $_POST['country_code'];
    $mobile = $_POST['mobile'];
    $message = $_POST['message'];

    if (!isset($_POST['choosePayment']) || empty($_POST['choosePayment'])){
        $error_message = "Please choose a payment method.";
    }else {
        $choosePayment = $_POST['choosePayment'];
    }
    
    $total_price = $_SESSION['selected_room']['total_price']; // Use total_price from session

    // Combine country code and mobile number
    $phone = $country_code . $mobile;

    $validation = new Validering();
    
    $validation->validereFornavn($fname);
    $validation->validereEtternavn($lname);
    $validation->validereEpost($email);
    $validation->validereMobilnummer($country_code, $mobile);
    $validation->validereMessage($message);

    if (!empty($validation->getValidateError())) {
        echo "Error: <br>" . implode("<br>", $validation->getValidateError()) . "<br>";
        exit();
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();
    
            // Insert to payment table
            $stmt = $pdo->prepare("INSERT INTO swx_payment (amount, payment_method, status) 
                                   VALUES (:amount, :payment_method, :status)");
    
            // Insert payment record
            $stmt->execute([
                ':amount' => $total_price,
                ':payment_method' => $choosePayment,
                ':status' => 'Completed' // Default status, can be changed later
            ]);
    
            // Get the payment_id for the inserted record
            $payment_id = $pdo->lastInsertId();
    
            // Initialize user_id as NULL
            $user_id = NULL;
            if (isset($_SESSION['email'])) {
                // Get user_id based on the username in session
                $username = $_SESSION['email'];
                $stmt = $pdo->prepare("SELECT user_id, point FROM swx_users WHERE username = :username");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                // This update the point colum in user table, to be used as a loyalty program later. 
                if ($user) {
                    $user_id = $user['user_id'];

                    $current_points = $user['point'];
                    $new_points = $current_points + $total_price;

                    $stmt = $pdo->prepare("UPDATE swx_users SET point = :point WHERE username = :username");
                    $stmt->execute([
                        ':point' => $new_points,
                        ':username' => $username
                    ]);
                }
            }
    
            // Insert to booking table
            $stmt = $pdo->prepare("INSERT INTO swx_booking (user_id, room_id, payment_id, name, email, tlf, comments, check_in_date, check_out_date, number_of_guests) 
                       VALUES (:user_id, :room_id, :payment_id, :name, :email, :tlf, :comments, :check_in_date, :check_out_date, :number_of_guests)");

            // Bruk room_id direkte fra session
            $stmt->execute([
                ':user_id' => $user_id,
                ':room_id' => $_SESSION['selected_room']['room_id'], // Ingen ekstra konvertering nødvendig
                ':payment_id' => $payment_id,
                ':name' => $fname . " " . $lname,
                ':email' => $email,
                ':tlf' => $phone,
                ':comments' => $message,
                ':check_in_date' => $_SESSION['checkin'],
                ':check_out_date' => $_SESSION['checkout'],
                ':number_of_guests' => $_SESSION['adults'] + $_SESSION['children']
            ]);
    
            if ($choosePayment === 'Invoice') {
                require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/generate_invoice.php");
    
                $invoiceFileName = generateInvoice(
                    $fname,                              
                    $lname,                              
                    $_SESSION['selected_room']['type_name'], 
                    $_SESSION['selected_room']['total_price'], 
                    $payment_id                          
                );
    
                // Oppdater fakturabanen i databasen
                $stmt = $pdo->prepare("UPDATE swx_payment SET invoice_path = :invoice_path WHERE payment_id = :payment_id");
                $stmt->execute([
                    ':invoice_path' => $invoiceFileName,
                    ':payment_id' => $payment_id
                ]);
            }


            // Commit the transaction
            $pdo->commit();
            header("Location: confirmation.php?payment_id=$payment_id");
            exit();
    
        } catch (Exception $e) {
            // Rollback in case of error
            if ($pdo->inTransaction()) { // Check if a transaction is active
                $pdo->rollBack();
            }
            log_error($e);
        }
    }
  
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Get and sanitize form data
        $fname = sanitize($_POST['fname']);
        $lname = sanitize($_POST['lname']);
        $email = sanitize($_POST['epost']);
        $country_code = sanitize($_POST['country_code']);
        $mobile = sanitize($_POST['mobile']);
        $message = sanitize($_POST['message']);

        // Combine country code and mobile number
        $phone = $country_code . $mobile;

        // Validate form inputs
        $validation = new Validering();
        $validation->validereFornavn($fname);
        $validation->validereEtternavn($lname);
        $validation->validereEpost($email);
        $validation->validereMobilnummer($country_code, $mobile);
        $validation->validereMessage($message);

        if (!empty($validation->getValidateError())) {
            throw new Exception("Validation failed: " . implode(", ", $validation->getValidateError()));
        }

        // Validate payment method
        if (!isset($_POST['choosePayment']) || empty($_POST['choosePayment'])) {
            throw new Exception("Please choose a payment method.");
        }
        $choosePayment = sanitize($_POST['choosePayment']);

        // Validate session data
        if (!isset($_SESSION['selected_room']['room_id']) || empty($_SESSION['selected_room']['room_id'])) {
            throw new Exception("Room ID is missing or invalid.");
        }

        $room_id = $_SESSION['selected_room']['room_id']; // Use room_id from session

        // Validate room_id against the database
        $stmt = $pdo->prepare("SELECT room_id FROM swx_room WHERE room_id = :room_id");
        $stmt->execute([':room_id' => $room_id]);
        if ($stmt->rowCount() === 0) {
            throw new Exception("The selected room ID does not exist in the database.");
        }

        // Insert into payment table
        $stmt = $pdo->prepare("INSERT INTO swx_payment (amount, payment_method, status) 
                               VALUES (:amount, :payment_method, :status)");
        $stmt->execute([
            ':amount' => $_SESSION['selected_room']['total_price'],
            ':payment_method' => $choosePayment,
            ':status' => 'Completed' // Default status
        ]);

        // Get payment_id for the inserted record
        $payment_id = $pdo->lastInsertId();

        // Initialize user_id as NULL
        $user_id = NULL;
        if (isset($_SESSION['email'])) {
            // Get user_id based on the username in session
            $username = sanitize($_SESSION['email']);
            $stmt = $pdo->prepare("SELECT user_id, point FROM swx_users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Update points for loyalty program
            if ($user) {
                $user_id = $user['user_id'];
                $current_points = $user['point'];
                $new_points = $current_points + $_SESSION['selected_room']['total_price'];

                $stmt = $pdo->prepare("UPDATE swx_users SET point = :point WHERE username = :username");
                $stmt->execute([
                    ':point' => $new_points,
                    ':username' => $username
                ]);
            }
        }

        // Insert into booking table
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

        // If payment method is Invoice, generate invoice
        if ($choosePayment === 'Invoice') {
            require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/generate_invoice.php");

            $invoiceFileName = generateInvoice(
                $fname,
                $lname,
                $_SESSION['selected_room']['type_name'],
                $_SESSION['selected_room']['total_price'],
                $payment_id
            );

            // Update invoice path in payment table
            $stmt = $pdo->prepare("UPDATE swx_payment SET invoice_path = :invoice_path WHERE payment_id = :payment_id");
            $stmt->execute([
                ':invoice_path' => $invoiceFileName,
                ':payment_id' => $payment_id
            ]);
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect to confirmation page
        header("Location: confirmation.php?payment_id=$payment_id");
        exit();

    } catch (Exception $e) {
        // Rollback in case of error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_error($e);
        echo "<br>Sorry, something went wrong. Please try again later.<br>";
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
                        <h3>Velg betalingsmetode</h3>
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
                                    <tr>
                                        <th scope="row">Total</th>
                                        <td class="text-end"><?php echo htmlspecialchars($_SESSION['selected_room']['total_price']) . " NOK";?></td>
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
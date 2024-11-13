<?php

// Start session
session_start();

// Include function and database file
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['epost'];
    $country_code = $_POST['country_code'];
    $mobile = $_POST['mobile'];
    $message = $_POST['message'];
    $choosePayment = $_POST['choosePayment'];
    $total_price = $_SESSION['selected_room']['total_price']; // Use total_price from session

    // Combine country code and mobile number
    $phone = $country_code . $mobile;

    // Validate form fields
    if (empty($fname) || empty($lname) || empty($email) || empty($choosePayment)) {
        echo "Please fill in all required fields.";
        exit();
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert to payment table
        $stmt = $pdo->prepare("INSERT INTO payment (amount, payment_method, status) 
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
        if (isset($_SESSION['username'])) {
            // Get user_id based on the username in session
            $username = $_SESSION['username'];
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $user_id = $user['user_id'];
            }
        }

        // Insert to booking table
        $stmt = $pdo->prepare("INSERT INTO booking (user_id, room_id, payment_id, name, email, tlf, comments, check_in_date, check_out_date, number_of_guests) 
                               VALUES (:user_id, :room_id, :payment_id, :name, :email, :tlf, :comments, :check_in_date, :check_out_date, :number_of_guests)");

        // Insert booking record
        $stmt->execute([
            ':user_id' => $user_id,
            ':room_id' => $_SESSION['selected_room']['room_id'],
            ':payment_id' => $payment_id,
            ':name' => $fname . " " . $lname,
            ':email' => $email,
            ':tlf' => $phone,
            ':comments' => $message,
            ':check_in_date' => $_SESSION['checkin'],
            ':check_out_date' => $_SESSION['checkout'],
            ':number_of_guests' => $_SESSION['adults'] + $_SESSION['children']
        ]);

        // Commit the transaction
        $pdo->commit();

        // Redirect user to appropriate page
        if (isset($_SESSION['username'])) {
            header('Location: user_profile_two.php');
            exit();
        } else {
            session_destroy();
            header('Location: ../../index1.php');
            exit();
        }

    } catch (Exception $e) {
        // Rollback in case of error
        $pdo->rollBack();

        // Log and display error message for debugging
        error_log("Error: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
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
                        <input class="form-check-input" type="radio" value="Vips" id="flexCheckDefault" name="choosePayment" >
                        <label class="form-check-label" for="flexCheckDefault">Vips</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="Credit Card" id="flexCheckDefault" name="choosePayment">
                        <label class="form-check-label" for="flexCheckDefault">Kort</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="Invoice" id="flexCheckChecked" name="choosePayment">
                        <label class="form-check-label" for="flexCheckChecked">Faktura</label>
                    </div>
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
        <?php 
        // Debug purpose
        var_dump($_POST);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        ?>
    </body>
    <footer>
        <div style="margin-top: 50px;">
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>
        </div>
    </footer>
</html>
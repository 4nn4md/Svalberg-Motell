<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); 

// Path to where pdf files are stored
$pdfDir = __DIR__ . '/../../tmp/';

// check if payment_id is provided via the get request
if (!isset($_GET['payment_id'])) {
    log_error(new Exception("No payment ID specified"));
    echo "No payment specified.";
    exit();
}
//Place the element in an integer
$payment_id = (int)$_GET['payment_id'];

// Feth payment information from the database 
try{
    $stmt = $pdo->prepare("SELECT payment_method, invoice_path FROM swx_payment WHERE payment_id = :payment_id");
    $stmt->execute([':payment_id' => $payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        log_error(new Exception("Payment information not found for payment_id: $payment_id"));
        echo "Payment information not found.";
        exit();
    }
} catch (PDOException $e){
    log_error($e);
    echo "Database error occurred.";
    exit;
}

// Set the full file ptah fot the invoice
$invoicePath = !empty($payment['invoice_path']) ? $pdfDir . sanitize($payment['invoice_path']) : null;

// Check if a file download request is made using the "filnavn" parameter
if (isset($_GET['filnavn']) && isset($_GET['payment_id'])) {
    $filename = sanitize($_GET['filnavn']); 
    $filepath = $pdfDir . $filename; 

    // Check if the file exists at the given path
    if (file_exists($filepath)) {
        // Clear output buffer to prevent any output that could corrupt the file download
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Set the HTTP headers to initiate the file download
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Length: " . filesize($filepath));
        header("Cache-Control: private");
        header("Pragma: public");

        // read and output the file content to the user
        readfile($filepath);
        exit();
    } else {
        log_error(new Exception("File \"$filename\" does not exist at path: $filepath"));
        echo "Error: File \"$filename\" does not exist.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h1>Congratulations on your order!</h1>
            </div>
            <div class="card-body text-center">
                <p>Thank you for choosing Svalberg Motel. Your order is now confirmed.</p>
                <?php if ($invoicePath && file_exists($invoicePath)): ?>
                    <p>You can download the invoice by clicking on the link below:</p>
                    <a href="?filnavn=<?php echo urlencode(basename($invoicePath)); ?>&payment_id=<?php echo $payment_id; ?>" class="btn btn-success">Download invoice</a>
                <?php else: ?>
                    <p>Your payment was processed with <?php echo htmlspecialchars($payment['payment_method']); ?>.</p>
                <?php endif; ?>
                <form method="POST" class="mt-3">
                    <button type="submit" name="done" class="btn btn-primary">Ready</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Håndter "Ferdig"-knappen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['done'])) {
    
    $mottakerTlf = "+" . $_SESSION['phone'];
    $checkin = $_SESSION['selected_room']['checkin'];
    $checkout = $_SESSION['selected_room']['checkout'];
    include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/controller/genereateSMS.php");


    if (isset($_SESSION['email'])) {
        unset($_SESSION['selected_room']);
        header('Location: user_profile_two.php');
    } else {
        session_destroy();
        header('Location: ../../index1.php');
    }
    exit();
}
?>

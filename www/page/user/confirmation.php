<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");

// Funksjon for å rense brukerinput og sikre sikkerhet
function sanitize($var) {
    $var = strip_tags($var); // Fjern HTML-tagger
    $var = htmlentities($var, ENT_QUOTES, 'UTF-8'); // Konverter spesialtegn til HTML-enheter
    return basename($var); // Sørg for at bare filnavnet returneres, forhindrer directory traversal
}

// Sti til katalogen der PDF-filene lagres
$pdfDir = __DIR__ . '/../../tmp/';

// Sjekk om `payment_id` er sendt via GET
if (!isset($_GET['payment_id'])) {
    die("Ingen betaling spesifisert.");
}

$payment_id = (int)$_GET['payment_id'];

// Hent betalingsinformasjon fra databasen
$stmt = $pdo->prepare("SELECT payment_method, invoice_path FROM swx_payment WHERE payment_id = :payment_id");
$stmt->execute([':payment_id' => $payment_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("Betalingsinformasjon ikke funnet.");
}

// Fakturafilens sti
$invoicePath = !empty($payment['invoice_path']) ? $pdfDir . sanitize($payment['invoice_path']) : null;

// Feilsøk hvis lenken ikke vises
/*var_dump($payment['invoice_path'], $invoicePath);
if ($invoicePath && !file_exists($invoicePath)) {
    die("Filen eksisterer ikke på stien: " . $invoicePath);
}*/

// Sjekk om en nedlastingsforespørsel er sendt via `filnavn`
if (isset($_GET['filnavn']) && isset($_GET['payment_id'])) {
    $filename = sanitize($_GET['filnavn']); // Rens brukerinput
    $filepath = $pdfDir . $filename; // Full sti til filen

    // Sjekk om filen eksisterer
    if (file_exists($filepath)) {
        // Rydd opp utdata-buffer for å unngå korrupt fil
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Send passende HTTP-headere for nedlasting
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Length: " . filesize($filepath));
        header("Cache-Control: private");
        header("Pragma: public");

        // Les og send filen til brukeren
        readfile($filepath);
        exit();
    } else {
        die("<h1>Feil: Filen \"$filename\" eksisterer ikke.</h1>");
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h1>Gratulerer med bestillingen!</h1>
            </div>
            <div class="card-body text-center">
                <p>Takk for at du valgte Svalberg Motell. Din bestilling er nå bekreftet.</p>
                <?php if ($invoicePath && file_exists($invoicePath)): ?>
                    <p>Du kan laste ned fakturaen ved å klikke på lenken nedenfor:</p>
                    <a href="?filnavn=<?php echo urlencode(basename($invoicePath)); ?>&payment_id=<?php echo $payment_id; ?>" class="btn btn-success">Last ned faktura</a>
                <?php else: ?>
                    <p>Din betaling ble behandlet med <?php echo htmlspecialchars($payment['payment_method']); ?>.</p>
                <?php endif; ?>
                <form method="POST" class="mt-3">
                    <button type="submit" name="done" class="btn btn-primary">Ferdig</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Håndter "Ferdig"-knappen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['done'])) {
    if (isset($_SESSION['username'])) {
        header('Location: user_profile_two.php');
    } else {
        session_destroy();
        header('Location: ../../index1.php');
    }
    exit();
}
?>

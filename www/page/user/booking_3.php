<?php 
// Enable error display for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session 
session_start(); 

// Include functions 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); 

// This checks if information about the room is saved in the session
if (isset($_SESSION['selected_room'])) {
     // Retrieve individual values from rom information
    $type_name = $_SESSION['selected_room']['type_name'] ?? '';
    $description = $_SESSION['selected_room']['description'] ?? '';
    $floor = $_SESSION['selected_room']['floor'] ?? '';
    $nearElevator = $_SESSION['selected_room']['nearElevator'] ?? '';
    $total_price = $_SESSION['selected_room']['total_price'] ?? '';
    $picture = $_SESSION['selected_room']['picture'] ?? '';
    $adults = (int) ($_SESSION['selected_room']['adults'] ?? 0);
    $children = (int) ($_SESSION['selected_room']['children'] ?? 0);
    $checkin = $_SESSION['selected_room']['checkin'] ?? '';
    $checkout = $_SESSION['selected_room']['checkout'] ?? '';
} else {
    // Direct user to booking_1.php if no room information is available 
    header('Location: booking_1.php'); 
    exit;
}

//convert date from y-m-d to d.m.y for both checkin and checkout
$checkin = strtotime($checkin);
$checkin = date('d.m.Y', $checkin);

$checkout = strtotime($checkout);
$checkout = date('d.m.Y', $checkout);


?>

<html>
    <head>
        <!-- Include header-->
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?>
    </head>
    <body>
        <div class="container w-75" style="margin-top: 100px;">
            <div class="position-relative m-4">
                <div class="progress" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button" class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">1</button>
                <button type="button" class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary rounded-pill" style="width: 2rem; height:2rem;">2</button>
                <button type="button" class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-secondary rounded-pill" style="width: 2rem; height:2rem;">3</button>
            </div>
            
            <div class="card bg-white" style="margin: 50px auto 25px auto;">
                <h1 class="text-center" style="margin: 50px auto 25px auto;">Sammendrag</h1>
                <div class="row g-0 p-2">
                    <div class="col-md-4">    
                        <img
                            src="http://localhost/Svalberg-Motell/www/assets/image/<?php echo htmlspecialchars($picture); ?>" 
                            class="img-fluid rounded" style="height: 200px; object-fit: cover; margin-left: 25px;" 
                        />
                    </div>
                    <div class="col-md-8">
                        <div class="card-body d-flex flex-column justify-content-between" style="height: 100%;">
                            <div>
                                <h5 class="card-title"><?php echo htmlspecialchars($type_name); ?></h5>
                                <p class="card-text m-0 p-0">
                                    <?php echo "Dato: " . htmlspecialchars($checkin) . " - " . htmlspecialchars($checkout); ?>
                                </p>
                                <p class="card-text m-0 p-0">
                                    <?php echo "Antall voksen: " . htmlspecialchars($adults); ?>
                                </p>
                                <p class="card-text m-0 p-0">
                                <?php echo "Antall barn: " . htmlspecialchars($children); ?>
                                </p>
                            </div>
                            <div class="row align-items-end mt-auto">
                                <div class="col-5">
                                    <p class="card-text mb-0">
                                        <strong>Etasje:</strong> <?php echo htmlspecialchars($floor); ?><br>
                                        <strong>Nærhet til heis:</strong> <?php echo htmlspecialchars($nearElevator); ?><br>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin: 0 25px 0 25px;">
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
                                <td scope="row"><?php echo "MVA 12%" . " Nok";?></td>
                                <td class="text-end"><?php echo calculateMVA($_SESSION['selected_room']['base_price']) . " NOK";?>
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
                        <a href="booking_4.php" class="btn msearch-btn w-50 mb-2">Neste</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <footer>
        <div style="margin-top: 50px;">
            <!-- Include footer-->
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>
        </div>
    </footer>
</html>
<?php
ob_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php");
// Enable error display for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to store data across different pages
//session_start();

//including database and function file. 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php"); 
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); 

// Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['type_name'])) {
        // Check if the 'type_name' POST data is set (the form for selecting a room was submitted)
        $_SESSION['location'] = $_POST['location'] ?? $_SESSION['location'];
        $_SESSION['checkin'] = $_POST['checkin'] ?? $_SESSION['checkin'];
        $_SESSION['checkout'] = $_POST['checkout'] ?? $_SESSION['checkout'];
        $_SESSION['adults'] = $_POST['adults'] ?? $_SESSION['adults'];
        $_SESSION['children'] = $_POST['children'] ?? $_SESSION['children'];

        $_SESSION['etasje'] = $_POST['etasje'] ?? $_SESSION['etasje'];
        $_SESSION['heis'] = $_POST['heis'] ?? $_SESSION['heis'];
        $_SESSION['room_type'] = $_POST['room_type'] ?? $_SESSION['room_type'];

        $_SESSION['selected_room'] = [
            'room_id' => $_POST['room_id'],
            'type_name' => $_POST['type_name'],
            'description' => $_POST['description'],
            'floor' => $_POST['floor'] ?? '',
            'nearElevator' => $_POST['nearElevator'],
            'total_price' => $_POST['total_price'],
            'picture' => $_POST['picture'],
            'adults' => $_POST['adults'],
            'children' => $_POST['children'],
            'checkin' => $_POST['checkin'],
            'checkout' => $_POST['checkout'],
            'base_price' => $_POST['base_price']
        ];

        // Redirect to booking_2.php after selecting a room
        header('Location: booking_2.php');
        exit();
    } else {
        // This is for the filter form, staying on the same page (booking_1.php)
        $_SESSION['location'] = $_POST['location'] ?? $_SESSION['location'];
        $_SESSION['checkin'] = $_POST['checkin'] ?? $_SESSION['checkin'];
        $_SESSION['checkout'] = $_POST['checkout'] ?? $_SESSION['checkout'];
        $_SESSION['adults'] = $_POST['adults'] ?? $_SESSION['adults'];
        $_SESSION['children'] = $_POST['children'] ?? $_SESSION['children'];

        $_SESSION['etasje'] = $_POST['etasje'] ?? $_SESSION['etasje'] ?? null;
        $_SESSION['heis'] = $_POST['heis'] ?? $_SESSION['heis'] ?? null;
        $_SESSION['room_type'] = $_POST['room_type'] ?? $_SESSION['room_type'] ?? null;
    }
}
// Query the database for available room types (to be used in the room selection filter dropdown)
$roomTypes = $pdo->query("SELECT type_id, type_name FROM swx_room_type")->fetchAll(PDO::FETCH_ASSOC);

// Update the variable after user have made changes in the search bar
$location = $_SESSION['location'];
$checkin = $_SESSION['checkin'];
$checkout = $_SESSION['checkout'];
$adults = $_SESSION['adults'];
$children = $_SESSION['children'];
$etasje = $_SESSION['etasje'] ?? '';
$heis = $_SESSION['heis'] ?? '';
$room_type = $_SESSION['room_type'] ?? '';

//variable for total guests
$total_guests = $adults + $children;

$sql = 
    "SELECT swx_room.*, swx_room_type.type_name, swx_room_type.description, swx_room_type.price
    FROM swx_room
    INNER JOIN swx_room_type ON swx_room.room_type = swx_room_type.type_id
    WHERE swx_room.room_id NOT IN (
        SELECT room_id 
        FROM swx_booking 
        WHERE 
            swx_booking.check_in_date < :checkout 
            AND swx_booking.check_out_date > :checkin
        )
    AND swx_room.under_construction = 'nei'
    AND swx_room_type.max_capacity >= :total_guests";

$params = [
    ':checkin' => $_SESSION['checkin'],
    ':checkout' => $_SESSION['checkout'],
    ':total_guests' => $_SESSION['adults'] + $_SESSION['children']
];

// Legg til filtrene dynamisk
if (!empty($_SESSION['room_type'])) {
    $sql .= " AND swx_room.room_type = :room_type";
    $params[':room_type'] = $_SESSION['room_type'];
}
if (!empty($etasje)) {
    $sql .= " AND swx_room.floor = :floor";
    $params[':floor'] = $etasje;
}
if (!empty($heis)) {
    $sql .= " AND swx_room.nearElevator = :near_elevator";
    $params[':near_elevator'] = ($heis === 'ja') ? 'ja' : 'nei';
}

// Prepare the SQL query
$q = $pdo->prepare($sql); 

// Execute the query with the parameters
try {
    foreach ($params as $key => $value){
        $q->bindValue($key, $value);
    }
    $q->execute(); // Execute query
} catch (PDOException $e) {
    log_error($e);
    echo "<br>Sorry, something went wrong. Please try again later.<br>";
}
?>

<html>
    <body>
        <!-- Form to search for rooms based on location, check-in/out dates, and guest numbers -->
        <div class="container w-75" style="margin: 100px auto 24px auto;">
            <form method="POST" class="d-flex justify-content-center">
                <div class="row w-100 no-gutters"> 
                    <div class="col-md-2">
                        <label for="location" class="bold-label">Lokasjon</label>
                        <select class="form-control" id="location" name="location" required>
                            <option value="" disabled <?php echo empty($location) ? 'selected' : ''; ?>>Velg lokasjon..</option>
                            <option value="Kristiansand" <?php echo ($location === "Kristiansand") ? 'selected' : ''; ?>>Kristiansand</option>

                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="checkin" class="bold-label">Ankomst</label>
                        <input type="date" value="<?php echo htmlspecialchars($checkin);?>" id="checkin" name="checkin" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="checkout" class="bold-label">Avreise</label>
                        <input type="date" value="<?php echo htmlspecialchars($checkout);?>" id="checkout" name="checkout" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="adults" class="bold-label">Antall voksne</label>
                        <input type="number" min="0" value="<?php echo htmlspecialchars($adults);?>" id="adults" name="adults" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-2">
                        <label for="children" class="bold-label">Antall barn</label>
                        <input type="number" min="0" value="<?php echo htmlspecialchars($children);?>"  id="children" name="children" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end"> 
                        <button type="submit" id="submit" class="btn msearch-btn w-100" id="#MBtn">Søk</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter options: allows users to filter search results based on room floor, elevator proximity, and room type -->
        <div class="container w-75">
            <form method="POST" action="" id="filter-form" style="margin: 0 auto 0 auto;" class="d-flex justify-content-center">
            <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
                <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                <input type="hidden" name="adults" value="<?php echo htmlspecialchars($adults); ?>">
                <input type="hidden" name="children" value="<?php echo htmlspecialchars($children); ?>">

                <div class="row w-100 no-gutters">
                    <div class="col">
                        <label for="etasje" class="form-label">Etasje</label>
                        <select id="etasje" class="form-select" name="etasje">
                            <option value="">Alle</option>
                            <option value="1" <?php if ($etasje == "1") echo 'selected'; ?>>1. Etasje</option>
                            <option value="2" <?php if ($etasje == "2") echo 'selected'; ?>>2. Etasje</option>  
                        </select>
                    </div>

                    <div class="col">
                        <label for="heis" class="form-label">Nærhet til Heis</label>
                        <select id="heis" class="form-select" name="heis">
                            <option value="">Alle</option>
                            <option value="ja" <?php if ($heis == "ja") echo 'selected'; ?>>Ja</option>
                            <option value="nei" <?php if ($heis == "nei") echo 'selected'; ?>>Nei</option>
                        </select>
                    </div>

                    <!-- Dropdown to filter rooms by type; populated with values from the database -->
                    <div class="col">
                    <label for="room_type" class="form-label">Type rom</label>
                        <select id="room_type" class="form-select" name="room_type">
                            <option value="">Alle</option> 
                            <?php foreach ($roomTypes as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['type_id']); ?>" 
                                    <?php if (isset($_SESSION['room_type']) && $_SESSION['room_type'] == $type['type_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($type['type_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col d-flex align-items-end">
                        <button type="submit" class="btn msearch-btn">Filtrer</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="container">
            <!-- Check if $q is bigger than 0, and will check if there are any available rows -->
            <?php if($q->rowCount() > 0): ?> 
                <!-- Loop through each row from the query result -->
                <?php while($row = $q->fetch(PDO::FETCH_ASSOC)): ?> 
                    <?php 
                    // Get the base price from the database and calculate the total price based on the number of guests
                    $base_price = $row['price']; 
                    $total_price = price_guests($base_price, $adults, $children); 
                    ?>
                    <div class="card w-75" style="margin: 24px auto 10px auto; height: auto;">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <?php
                                //using function that is already made in function.php to find the picture that fits the name
                                $picture = getRoomImage($row['type_name']);
                                ?>
                                <img
                                    src="http://localhost/Svalberg-Motell/www/assets/image/<?php echo $picture; ?>" 
                                    class="img-fluid rounded-start" style="height: 200px; width: 500px; object-fit: cover;" 
                                />
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column justify-content-between" style="height: 100%;">
                                    <div>
                                        <h5 class="card-title"><?php echo htmlspecialchars($row['type_name']); ?></h5> <!-- Display the dynamic room name -->
                                        <p class="card-text">
                                            <?php echo htmlspecialchars($row['description']); ?> <!-- Display the dynamic room description -->
                                        </p>
                                    </div>
                                    <div class="row align-items-end mt-auto">
                                        <div class="col-5">
                                            <p class="card-text mb-0">
                                                <strong>Etasje:</strong> <?php echo htmlspecialchars($row['floor']); ?><br> 
                                                <strong>Nærhet til heis:</strong> <?php echo htmlspecialchars($row['nearElevator']); ?><br> 
                                            </p>
                                        </div>
                                        <div class="col text-end"> 
                                            <p class="card-text mb-0">
                                                <strong>Pris:</strong> <?php echo "Fra " . htmlspecialchars($total_price); ?> NOK per natt.
                                            </p>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end"> 
                                        <form method="post" action=""> <!-- Form to submit the selected room data -->
                                            <!-- Include hidden fields to pass room and booking data to the server when submitted -->
                                            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($row['room_id']); ?>">
                                            <input type="hidden" name="type_name" value="<?php echo htmlspecialchars($row['type_name']); ?>">
                                            <input type="hidden" name="description" value="<?php echo htmlspecialchars($row['description']); ?>">
                                            <input type="hidden" name="floor" value="<?php echo htmlspecialchars($row['floor']); ?>">
                                            <input type="hidden" name="nearElevator" value="<?php echo htmlspecialchars($row['nearElevator']); ?>">
                                            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
                                            <input type="hidden" name="picture" value="<?php echo htmlspecialchars($picture); ?>">
                                            <input type="hidden" name="adults" value="<?php echo htmlspecialchars($adults); ?>">
                                            <input type="hidden" name="children" value="<?php echo htmlspecialchars($children); ?>">
                                            <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                                            <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                                            <input type="hidden" name="base_price" value="<?php echo htmlspecialchars($row['price']); ?>">

                                            <button type="submit" id="submit" class="btn msearch-btn w-100" id="#MBtn">Velg</button>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Message if no rooms are avaible -->
                <p>Ingen ledige rom tilgjengelig.</p>
            <?php endif; ?>
        </div>
        <?php 
        //var_dump($_SESSION['selected_room']['base_price']);
        ?>
    </body>
    <footer>
        <div style="margin-top: 50px;">
            <!-- Include footer -->
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>
        </div>
    </footer>
</html>
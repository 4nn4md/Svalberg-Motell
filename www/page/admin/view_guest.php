<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $guest_id = $_GET['id'];

    // Fetch guest details
    $guestQuery = "SELECT user_id, firstName, lastName, username, tlf FROM swx_users WHERE user_id = ?";
    $stmt = $conn->prepare($guestQuery);
    $stmt->bind_param("i", $guest_id);
    $stmt->execute();
    $guestResult = $stmt->get_result();

    if ($guestResult->num_rows > 0) {
        $guest = $guestResult->fetch_assoc();
    } else {
        die("Guest not found.");
    }

    // Fetch the bookings for this guest
    $bookingQuery = "SELECT b.check_in_date, b.check_out_date, r.room_id, rt.type_name AS room_type
                     FROM swx_booking b
                     JOIN room r ON b.room_id = r.room_id
                     JOIN room_type rt ON r.room_type = rt.type_id
                     WHERE b.user_id = ?";
    $stmt = $conn->prepare($bookingQuery);
    $stmt->bind_param("i", $guest_id);
    $stmt->execute();
    $bookingResult = $stmt->get_result();
} else {
    die("No guest ID provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px; padding: 20px; }
        .logout-button { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .back-button { position: absolute; top: 10px; left: 10px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="javascript:history.back()" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Guest Information</h1>

    <div class="card">
        <h3>Guest Details</h3>
        <p><strong>Name:</strong> <?php echo $guest['firstName'] . ' ' . $guest['lastName']; ?></p>
        <p><strong>Username:</strong> <?php echo $guest['username']; ?></p>
        <p><strong>Phone:</strong> <?php echo $guest['tlf']; ?></p>
    </div>

    <div class="card">
        <h3>Rooms Booked</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Check-In Date</th>
                    <th>Check-Out Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($bookingResult->num_rows > 0) {
                    while ($booking = $bookingResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $booking['room_number'] . "</td>";
                        echo "<td>" . $booking['room_type'] . "</td>";
                        echo "<td>" . $booking['check_in_date'] . "</td>";
                        echo "<td>" . $booking['check_out_date'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No bookings found for this guest.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

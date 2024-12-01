<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Get today's date in Y-m-d format
$today = date('Y-m-d');

// Fetch room details based on room_id passed from URL (e.g., view_room.php?id=1)
$roomId = $_GET['id'] ?? null;
if (!$roomId) {
    echo "Room ID is required.";
    exit();
}

// Get the room details from the database
$roomQuery = "
    SELECT r.room_id, rt.type_name as room_type, r.nearElevator, r.floor, r.availability, r.under_construction, 
           rt.max_capacity, r.created_at, r.updated_at
    FROM swx_room r
    JOIN swx_room_type rt ON r.room_type = rt.type_id
    WHERE r.room_id = ?";
$stmt = $pdo->prepare($roomQuery);
$stmt->execute([$roomId]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the room exists
if (!$room) {
    echo "Room not found.";
    exit();
}

// Check for bookings that overlap with today's date
$bookingQuery = "
    SELECT b.booking_id, b.check_in_date, b.check_out_date, b.name AS guest_name
    FROM swx_booking b
    WHERE b.room_id = ? AND (b.check_in_date <= ? AND b.check_out_date >= ?)";
$stmt = $pdo->prepare($bookingQuery);
$stmt->execute([$roomId, $today, $today]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine room availability
$roomAvailability = 'Available';
if (!empty($bookings)) {
    $roomAvailability = 'Occupied';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Room Details</h1>

    <div class="card">
        <div class="card-header">
            <h3><?php echo htmlspecialchars($room['room_type']); ?> (Room ID: <?php echo $room['room_id']; ?>)</h3>
        </div>
        <div class="card-body">
            <p><strong>Floor:</strong> <?php echo htmlspecialchars($room['floor']); ?></p>
            <p><strong>Near Elevator:</strong> <?php echo htmlspecialchars($room['nearElevator']); ?></p>
            <p><strong>Max Capacity:</strong> <?php echo htmlspecialchars($room['max_capacity']); ?></p>
            <p><strong>Availability:</strong> <?php echo $roomAvailability; ?></p>

            <h4>Upcoming Bookings:</h4>
            <?php if (empty($bookings)) { ?>
                <p>No bookings for this room today or in the future.</p>
            <?php } else { ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Guest Name</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking) { ?>
                            <tr>
                                <td><?php echo $booking['booking_id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                                <td><?php echo $booking['check_in_date']; ?></td>
                                <td><?php echo $booking['check_out_date']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>

    <a href="manage_rooms.php" class="btn btn-primary mt-3">Back to Manage Rooms</a>
</div>

</body>
</html>

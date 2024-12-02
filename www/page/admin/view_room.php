<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php");

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Get today's date, first day of the week (Monday), and first day of the month
$today = date('Y-m-d');
$firstDayOfMonth = date('Y-m-01');
$firstDayOfThisWeek = date('Y-m-d', strtotime('monday this week')); // Start of this week (Monday)
$lastDayOfThisWeek = date('Y-m-d', strtotime('sunday this week')); // End of this week (Sunday)
$firstDayOfNextWeek = date('Y-m-d', strtotime('next monday')); // Start of next week (Monday)
$lastDayOfNextWeek = date('Y-m-d', strtotime('next sunday')); // End of next week (Sunday)

// Initialize the filter parameters
$startOfRange = $today;
$endOfRange = $today;

// Validate the filter parameter using PHP
if (isset($_GET['filter'])) {
    $filter = sanitize($_GET['filter']); // Sanitize filter
    switch ($filter) {
        case 'this_week':
            // Get the start and end date of this week (Monday to Sunday)
            $startOfRange = $firstDayOfThisWeek; // Start of this week (Monday)
            $endOfRange = $lastDayOfThisWeek; // End of this week (Sunday)
            break;
        case 'this_month':
            // Filter for this month's bookings
            $startOfRange = $firstDayOfMonth; // First day of the current month
            $endOfRange = date('Y-m-t'); // Last day of the current month
            break;
        default:
            // If the filter is invalid, default to today's date range
            $startOfRange = $today;
            $endOfRange = $today;
            break;
    }
} else {
    // Default to today's date range if no filter is set
    $startOfRange = $today;
    $endOfRange = $today;
}

// Fetch room details based on room_id passed from URL (e.g., view_room.php?id=1)
$roomId = isset($_GET['id']) ? sanitize($_GET['id']) : null; // Sanitize the room ID from the URL
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

// Modify the booking query to filter bookings based on the selected range
$bookingQuery = "
    SELECT b.booking_id, b.check_in_date, b.check_out_date, b.name AS guest_name
    FROM swx_booking b
    WHERE b.room_id = ? AND b.check_in_date >= ? AND b.check_out_date <= ?";
$stmt = $pdo->prepare($bookingQuery);
$stmt->execute([$roomId, $startOfRange, $endOfRange]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style to move the "Back" button to the top-left corner */
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Back button placed in the top-left corner -->
    <a href="manage_rooms.php" class="btn btn-primary back-button">Back to Manage Rooms</a>

    <h1>Room Details</h1>

    <div class="card">
        <div class="card-header">
            <h3><?php echo htmlspecialchars(sanitize($room['room_type'])); ?> (Room ID: <?php echo htmlspecialchars(sanitize($room['room_id'])); ?>)</h3>
        </div>
        <div class="card-body">
            <p><strong>Floor:</strong> <?php echo htmlspecialchars(sanitize($room['floor'])); ?></p>
            <p><strong>Near Elevator:</strong> <?php echo htmlspecialchars(sanitize($room['nearElevator'])); ?></p>
            <p><strong>Max Capacity:</strong> <?php echo htmlspecialchars(sanitize($room['max_capacity'])); ?></p>
            <p><strong>Availability:</strong> <?php echo htmlspecialchars(sanitize($room['availability'])); ?></p>

            <h4>Upcoming Bookings:</h4>

            <!-- Filter Form -->
            <form method="GET" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($roomId); ?>" />
                <div class="form-group">
                    <label for="filter">Filter Bookings:</label>
                    <select class="form-control" name="filter" id="filter" onchange="this.form.submit()">
                        <option value="" disabled <?php echo !isset($_GET['filter']) ? 'selected' : ''; ?>>Select a filter...</option> <!-- Placeholder option -->
                        <option value="this_week" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'this_week') ? 'selected' : ''; ?>>This Week</option>
                        <option value="this_month" <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    </select>
                </div>
            </form>

            <!-- Bookings Table -->
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest Name</th>
                        <th>Check-in Date</th>
                        <th>Check-out Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(sanitize($booking['booking_id'])); ?></td>
                                <td><?php echo htmlspecialchars(sanitize($booking['guest_name'])); ?></td>
                                <td><?php echo htmlspecialchars(sanitize($booking['check_in_date'])); ?></td>
                                <td><?php echo htmlspecialchars(sanitize($booking['check_out_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No bookings found for this date range.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>

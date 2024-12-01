<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); // Ensure sanitize function is included

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Sanitize session data
$role = sanitize($_SESSION['role']); // Example of sanitizing session data

// Fetch room information
$roomQuery = "SELECT COUNT(*) AS total_rooms, SUM(CASE WHEN availability = 'ledig' THEN 1 ELSE 0 END) AS available_rooms FROM swx_room";  
$roomResult = $pdo->query($roomQuery); // Use PDO query method
if (!$roomResult) {
    die("Error executing room query: " . $pdo->errorInfo()[2]);
}
$roomData = $roomResult->fetch(PDO::FETCH_ASSOC);

// Sanitize room data (although it's already safe from SQL injection)
$total_rooms = sanitize($roomData['total_rooms']);
$available_rooms = sanitize($roomData['available_rooms']);

// Fetch staff information (assuming 'staff' table)
$staffQuery = "SELECT staff_id, email FROM swx_staff";  // Staff are stored in the 'staff' table
$staffResult = $pdo->query($staffQuery); // Use PDO query method
if (!$staffResult) {
    die("Error executing staff query: " . $pdo->errorInfo()[2]);
}

// Fetch guest information (users are guests in this case)
$guestQuery = "SELECT user_id, username FROM swx_users";  // Guests are stored in the 'users' table
$guestResult = $pdo->query($guestQuery); // Use PDO query method
if (!$guestResult) {
    die("Error executing guest query: " . $pdo->errorInfo()[2]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px; padding: 20px; text-align: center; }
        .logout-button { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .logout-button:hover { background: #c82333; }
        .card a { display: inline-block; margin-top: 10px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .card a:hover { background: #0056b3; }
    </style>
</head>
<body>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Svalberg Admin Dashboard</h1>

    <div class="databox">
        <div class="card">
            <i class="fas fa-bed"></i>
            <h3>Room Overview</h3>
            <p>Total Rooms: <?php echo htmlspecialchars($total_rooms); ?></p>
            <p>Available Rooms: <?php echo htmlspecialchars($available_rooms); ?></p>
            <a href="manage_rooms.php">Manage Rooms</a>
        </div>

        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Staff Overview</h3>
            <p>Total Staff: <?php echo $staffResult->rowCount(); ?></p> <!-- Use PDO rowCount() -->
            <a href="manage_staff.php">Manage Staff</a>
        </div>

        <div class="card">
            <i class="fas fa-user"></i>
            <h3>Guest Overview</h3>
            <p>Total Guests: <?php echo $guestResult->rowCount(); ?></p> <!-- Use PDO rowCount() -->
            <a href="manage_guests.php">Manage Guests</a>
        </div>

        <div class="card">
            <i class="fas fa-calendar-check"></i>
            <h3>Bookings Overview</h3>
            <a href="manage_bookings.php">Manage Bookings</a>
        </div>

        <div class="card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Financial Overview</h3>
            <a href="financial_reports.php">View Reports</a>
        </div>
    </div>

</div>

</body>
</html>

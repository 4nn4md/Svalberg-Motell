<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/config.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch room information
$roomQuery = "SELECT COUNT(*) AS total_rooms, SUM(CASE WHEN availability = 'ledig' THEN 1 ELSE 0 END) AS available_rooms FROM room";  
$roomResult = mysqli_query($conn, $roomQuery);
if (!$roomResult) {
    die("Error executing room query: " . mysqli_error($conn));
}
$roomData = mysqli_fetch_assoc($roomResult);

// Fetch staff information (assuming 'staff' table)
$staffQuery = "SELECT staff_id, email FROM staff";  // Staff are stored in the 'staff' table
$staffResult = mysqli_query($conn, $staffQuery);
if (!$staffResult) {
    die("Error executing staff query: " . mysqli_error($conn));
}

// Fetch guest information (users are guests in this case)
$guestQuery = "SELECT user_id, username FROM users";  // Guests are stored in the 'users' table
$guestResult = mysqli_query($conn, $guestQuery);
if (!$guestResult) {
    die("Error executing guest query: " . mysqli_error($conn));
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
            <p>Total Rooms: <?php echo $roomData['total_rooms']; ?></p>
            <p>Available Rooms: <?php echo $roomData['available_rooms']; ?></p>
            <a href="manage_rooms.php">Manage Rooms</a>
        </div>

        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Staff Overview</h3>
            <p>Total Staff: <?php echo mysqli_num_rows($staffResult); ?></p>
            <a href="manage_staff.php">Manage Staff</a>
        </div>

        <div class="card">
            <i class="fas fa-user"></i>
            <h3>Guest Overview</h3>
            <p>Total Guests: <?php echo mysqli_num_rows($guestResult); ?></p>
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

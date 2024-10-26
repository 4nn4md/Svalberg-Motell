<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/config.php';

// Check if connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch room information
$roomQuery = "SELECT COUNT(*) AS total_rooms, SUM(CASE WHEN availability = 1 THEN 1 ELSE 0 END) AS available_rooms FROM room";
$roomResult = mysqli_query($conn, $roomQuery);
if (!$roomResult) {
    die("Error executing room query: " . mysqli_error($conn));
}
$roomData = mysqli_fetch_assoc($roomResult);

// Fetch staff information
$staffQuery = "SELECT COUNT(*) AS total_staff FROM staff";
$staffResult = mysqli_query($conn, $staffQuery);
if (!$staffResult) {
    die("Error executing staff query: " . mysqli_error($conn));
}
$staffData = mysqli_fetch_assoc($staffResult);

// Optionally fetch bookings and financial data here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Motel Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 20px;
            text-align: center;
        }
        .card h3 {
            margin-bottom: 20px;
        }
        .card i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .card a:hover {
            background: #0056b3;
        }
        .databox {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        /* Logout button styles */
        .logout-button {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px; /* Space below the button */
        }
        .logout-button:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Svalberg Admin Dashboard</h1>

    <!-- Logout Button -->
    <a href="logout.php" class="logout-button">Logout</a>

    <div class="databox">
        <!-- Room Overview Card -->
        <div class="card">
            <i class="fas fa-bed"></i>
            <h3>Room Overview</h3>
            <p>Total Rooms: <?php echo $roomData['total_rooms']; ?></p>
            <p>Available Rooms: <?php echo $roomData['available_rooms']; ?></p>
            <a href="manage_rooms.php">Manage Rooms</a>
        </div>

        <!-- Staff Overview Card -->
        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Staff Overview</h3>
            <p>Total Staff: <?php echo $staffData['total_staff']; ?></p>
            <a href="manage_staff.php">Manage Staff</a>
        </div>

        <!-- Bookings Overview Card -->
        <div class="card">
            <i class="fas fa-calendar-check"></i>
            <h3>Bookings Overview</h3>
            <p>Pending Bookings: <!-- Add pending bookings count query here --></p>
            <p>Completed Bookings: <!-- Add completed bookings count query here --></p>
            <a href="manage_bookings.php">Manage Bookings</a>
        </div>

        <!-- Financial Overview Card -->
        <div class="card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Financial Overview</h3>
            <p>Total Revenue: <!-- Add total revenue query here --></p>
            <p>Total Expenses: <!-- Add total expenses query here --></p>
            <a href="financial_reports.php">View Reports</a>
        </div>
    </div>
</div>

</body>
</html>

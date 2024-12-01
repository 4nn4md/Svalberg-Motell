<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Initialize error messages
$errors = [];

// Get filter parameters from GET request
$roomFilter = $_GET['room_filter'] ?? '';
$userFilter = $_GET['user_filter'] ?? '';
$checkInDateFilter = $_GET['check_in_date_filter'] ?? '';
$checkOutDateFilter = $_GET['check_out_date_filter'] ?? '';
$sortOrder = $_GET['sort_order'] ?? 'ASC'; // Default to ascending order

// Build the WHERE clause based on the filters
$whereClauses = [];
if ($roomFilter) {
    $whereClauses[] = "b.room_id = ?";
}
if ($userFilter) {
    $whereClauses[] = "b.user_id = ?";
}
if ($checkInDateFilter) {
    $whereClauses[] = "b.check_in_date >= ?";
}
if ($checkOutDateFilter) {
    $whereClauses[] = "b.check_out_date <= ?";
}

// Build the WHERE SQL part
$whereSql = "";
if (!empty($whereClauses)) {
    $whereSql = "WHERE " . implode(' AND ', $whereClauses);
}

// Build the ORDER BY clause based on the sort order
$orderSql = "ORDER BY b.check_in_date $sortOrder"; // Default is ascending order

// Fetch booking information with filters applied
$bookingQuery = "
    SELECT b.booking_id, b.room_id, rt.type_name AS room_name, u.firstName, u.lastName, u.username AS user_username, 
           b.check_in_date, b.check_out_date, b.number_of_guests, b.name AS guest_name, b.email AS guest_email, 
           b.tlf AS guest_phone, b.comments
    FROM swx_booking b
    LEFT JOIN swx_room r ON b.room_id = r.room_id
    LEFT JOIN swx_room_type rt ON r.room_type = rt.type_id
    LEFT JOIN swx_users u ON b.user_id = u.user_id
    $whereSql $orderSql";  // Apply the WHERE and ORDER BY clause

$stmt = $pdo->prepare($bookingQuery);

// Bind parameters if filters are applied
$paramIndex = 1;
if ($roomFilter) {
    $stmt->bindParam($paramIndex++, $roomFilter, PDO::PARAM_INT);
}
if ($userFilter) {
    $stmt->bindParam($paramIndex++, $userFilter, PDO::PARAM_INT);
}
if ($checkInDateFilter) {
    $stmt->bindParam($paramIndex++, $checkInDateFilter, PDO::PARAM_STR);
}
if ($checkOutDateFilter) {
    $stmt->bindParam($paramIndex++, $checkOutDateFilter, PDO::PARAM_STR);
}

$stmt->execute();
$bookingResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if no bookings are found
if (empty($bookingResult)) {
    $message = "No bookings found with the selected filters.";
    $message_type = "info";
}

// Handle deleting booking
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM swx_booking WHERE booking_id = ?";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(1, $delete_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $message = "Booking deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting booking: " . $pdo->errorInfo()[2];
        $message_type = "error";
    }

    // Redirect to the same page after deletion to avoid re-triggering the deletion on back
    header("Location: manage_bookings.php");
    exit();
}

// Handle adding new booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_booking'])) {
    // Get form data
    $room_id = $_POST['room_id'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $check_in_date = $_POST['check_in_date'] ?? '';
    $check_out_date = $_POST['check_out_date'] ?? '';
    $number_of_guests = $_POST['number_of_guests'] ?? '';
    $guest_name = $_POST['guest_name'] ?? '';
    $guest_email = $_POST['guest_email'] ?? '';
    $guest_phone = $_POST['guest_phone'] ?? '';
    $comments = $_POST['comments'] ?? '';

    // Basic validation
    if (empty($room_id)) {
        $errors[] = "Room ID is required.";
    }
    if (empty($user_id)) {
        $errors[] = "User ID is required.";
    }
    if (empty($check_in_date) || !strtotime($check_in_date)) {
        $errors[] = "Valid check-in date is required.";
    }
    if (empty($check_out_date) || !strtotime($check_out_date)) {
        $errors[] = "Valid check-out date is required.";
    }
    if (empty($number_of_guests) || !is_numeric($number_of_guests)) {
        $errors[] = "Number of guests is required and should be a number.";
    }
    if (empty($guest_name)) {
        $errors[] = "Guest name is required.";
    }
    if (empty($guest_email) || !filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (empty($guest_phone) || !preg_match("/^[0-9]{8,15}$/", $guest_phone)) {
        $errors[] = "A valid phone number is required.";
    }

    // If validation fails, show errors
    if (!empty($errors)) {
        $message = "Error: <br>" . implode("<br>", $errors);
        $message_type = "error";
    } else {
        // Insert the new booking data into the database
        $insertQuery = "INSERT INTO swx_booking (room_id, user_id, check_in_date, check_out_date, number_of_guests, name, email, tlf, comments) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(1, $room_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $user_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $check_in_date, PDO::PARAM_STR);
        $stmt->bindParam(4, $check_out_date, PDO::PARAM_STR);
        $stmt->bindParam(5, $number_of_guests, PDO::PARAM_INT);
        $stmt->bindParam(6, $guest_name, PDO::PARAM_STR);
        $stmt->bindParam(7, $guest_email, PDO::PARAM_STR);
        $stmt->bindParam(8, $guest_phone, PDO::PARAM_STR);
        $stmt->bindParam(9, $comments, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // After adding a booking, redirect to refresh the page
            header("Location: manage_bookings.php");
            exit();
        } else {
            $message = "Error adding booking: " . $pdo->errorInfo()[2];
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
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
<a href="adminIndex.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Manage Bookings</h1>

    <!-- Message (if any) -->
    <?php if (isset($message)) { ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- Booking List Table -->
    <div class="card">
        <h3>Booking List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Room ID</th> <!-- Added room_id to display -->
                    <th>Room</th>
                    <th>User</th>
                    <th>Guest Name</th>
                    <th>Guest Email</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Number of Guests</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookingResult)) { ?>
                    <tr>
                        <td colspan="10" class="text-center">
                            No bookings found.
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($bookingResult as $booking) { ?>
                        <tr>
                            <td><?php echo $booking['booking_id']; ?></td>
                            <td><?php echo $booking['room_id']; ?></td> <!-- Display Room ID -->
                            <td><?php echo $booking['room_name']; ?></td>
                            <td><?php echo $booking['firstName'] . ' ' . $booking['lastName']; ?></td>
                            <td><?php echo $booking['guest_name']; ?></td>
                            <td><?php echo $booking['guest_email']; ?></td>
                            <td><?php echo $booking['check_in_date']; ?></td>
                            <td><?php echo $booking['check_out_date']; ?></td>
                            <td><?php echo $booking['number_of_guests']; ?></td>
                            <td>
                                <a href="manage_bookings.php?delete_id=<?php echo $booking['booking_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>
</html>

<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch room information along with all necessary details, including max_capacity
$roomQuery = "SELECT r.room_id, rt.type_name as room_type, r.nearElevator, r.floor, r.availability, r.under_construction, 
                     rt.max_capacity, r.created_at, r.updated_at
              FROM swx_room r
              JOIN swx_room_type rt ON r.room_type = rt.type_id";
$roomResult = $pdo->query($roomQuery); // Use PDO query method
if (!$roomResult) {
    die("Error executing room query: " . $pdo->errorInfo()[2]);
}

// Check the current number of rooms to ensure there are no more than 25
$roomCountQuery = "SELECT COUNT(*) AS room_count FROM swx_room";
$roomCountResult = $pdo->query($roomCountQuery);
$roomCount = $roomCountResult->fetch(PDO::FETCH_ASSOC)['room_count'];

if ($roomCount >= 25) {
    $message = "Maximum room limit reached (25 rooms). Cannot add more rooms.";
    $message_type = "error";
}

// Handle adding new room
if (isset($_POST['add_room'])) {
    // Get the form data
    $room_type = $_POST['room_type'] ?? '';
    $nearElevator = $_POST['nearElevator'] ?? '';
    $floor = $_POST['floor'] ?? '';
    $availability = $_POST['availability'] ?? '';
    $under_construction = $_POST['under_construction'] ?? '';

    // PHP Validation
    if ($roomCount >= 25) {
        $message = "You cannot add more rooms as the maximum limit (25 rooms) has been reached.";
        $message_type = "error";
    } elseif (!in_array($floor, [1, 2])) {
        $message = "Floor must be either 1 or 2!";
        $message_type = "error";
    } elseif (empty($room_type) || empty($nearElevator) || empty($availability) || empty($under_construction)) {
        $message = "All fields are required!";
        $message_type = "error";
    } else {
        // If "Under Construction" is "Yes", set "Availability" to "Occupied"
        if ($under_construction == 'Ja') {
            $availability = 'opptatt'; // Automatically set Availability to "Occupied"
        }

        // Insert the new room data into the database
        $insertQuery = "INSERT INTO swx_room (room_type, nearElevator, floor, availability, under_construction) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(1, $room_type, PDO::PARAM_STR);
        $stmt->bindParam(2, $nearElevator, PDO::PARAM_STR);
        $stmt->bindParam(3, $floor, PDO::PARAM_INT);
        $stmt->bindParam(4, $availability, PDO::PARAM_STR);
        $stmt->bindParam(5, $under_construction, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $message = "Room added successfully!";
            $message_type = "success";
        } else {
            $message = "Error adding room: " . $pdo->errorInfo()[2];
            $message_type = "error";
        }
    }
}

// Handle deleting room
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM swx_room WHERE room_id = ?";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(1, $delete_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $message = "Room deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting room: " . $pdo->errorInfo()[2];
        $message_type = "error";
    }

    // Redirect to the same page after deletion to avoid re-triggering the deletion on back
    header("Location: manage_rooms.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px; padding: 20px; }
        .logout-button { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .back-button { position: absolute; top: 10px; left: 10px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .add-button { margin-top: 20px; margin-bottom: 20px; }
        #addRoomForm { display: none; } /* Initially hide the form */
    </style>
</head>
<body>

<!-- Back Button -->
<a href="adminIndex.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Manage Rooms</h1>

    <!-- Message (if any) -->
    <?php if (isset($message)) { ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- Button to toggle visibility of the Add Room form -->
    <button class="btn btn-success add-button" onclick="toggleForm()">Add New Room</button>

    <!-- Add New Room Form -->
    <div class="card" id="addRoomForm">
        <h3>Add New Room</h3>
        <form method="POST" action="manage_rooms.php">
            <div class="mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <select class="form-control" name="room_type" required>
                    <option value="" disabled selected>Select Room Type</option>
                    <?php
                    // Fetch room types for dropdown
                    $roomTypeQuery = "SELECT type_id, type_name FROM swx_room_type";
                    $roomTypeResult = $pdo->query($roomTypeQuery);
                    while ($roomType = $roomTypeResult->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$roomType['type_id']}'>{$roomType['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="nearElevator" class="form-label">Near Elevator</label>
                <select class="form-control" name="nearElevator" required>
                    <option value="" disabled selected>Select Option</option>
                    <option value="Ja">Yes</option>
                    <option value="Nei">No</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="floor" class="form-label">Floor</label>
                <select class="form-control" name="floor" required>
                    <option value="" disabled selected>Select Floor</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="availability" class="form-label">Availability</label>
                <select class="form-control" name="availability" required>
                    <option value="" disabled selected>Select Availability</option>
                    <option value="ledig">Available</option>
                    <option value="opptatt">Occupied</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="under_construction" class="form-label">Under Construction</label>
                <select class="form-control" name="under_construction" required>
                    <option value="" disabled selected>Select Option</option>
                    <option value="Ja">Yes</option>
                    <option value="Nei">No</option>
                </select>
            </div>

            <button type="submit" name="add_room" class="btn btn-success">Add Room</button>
        </form>
    </div>

    <!-- Room List Table -->
    <div class="card">
        <h3>Room List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Room ID</th>
                    <th>Room Type</th>
                    <th>Max Capacity</th>
                    <th>Near Elevator</th>
                    <th>Floor</th>
                    <th>Availability</th>
                    <th>Under Construction</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($room = $roomResult->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $room['room_id']; ?></td>
                        <td><?php echo $room['room_type']; ?></td>
                        <td><?php echo $room['max_capacity']; ?></td>
                        <td><?php echo $room['nearElevator']; ?></td>
                        <td><?php echo $room['floor']; ?></td>
                        <td><?php echo $room['availability']; ?></td>
                        <td><?php echo $room['under_construction']; ?></td>
                        <td><?php echo $room['created_at']; ?></td>
                        <td><?php echo $room['updated_at']; ?></td>
                        <td>
                            <!-- Edit and Delete buttons -->
                            <a href="edit_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="manage_rooms.php?delete_id=<?php echo $room['room_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this room?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<script>
    // Function to toggle the visibility of the Add Room Form
    function toggleForm() {
        var form = document.getElementById('addRoomForm');
        form.style.display = form.style.display === "none" ? "block" : "none";
    }
</script>

</body>
</html>

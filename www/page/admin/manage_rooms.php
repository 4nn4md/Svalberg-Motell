<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Get filter parameters from GET request
$roomTypeFilter = $_GET['room_type_filter'] ?? '';
$nearElevatorFilter = $_GET['nearElevator_filter'] ?? '';
$floorFilter = $_GET['floor_filter'] ?? '';
$sortOrder = $_GET['sort_order'] ?? 'ASC'; // Default to ascending order

// Build the WHERE clause
$whereClauses = [];
if ($roomTypeFilter) {
    $whereClauses[] = "r.room_type = ?";
}
if ($nearElevatorFilter) {
    $whereClauses[] = "r.nearElevator = ?";
}
if ($floorFilter) {
    $whereClauses[] = "r.floor = ?";
}

// Build the WHERE SQL part
$whereSql = "";
if (!empty($whereClauses)) {
    $whereSql = "WHERE " . implode(' AND ', $whereClauses);
}

// Build the ORDER BY clause based on the sort order
$orderSql = "ORDER BY r.room_id $sortOrder"; // Default is ascending order

// Fetch room information with filters applied
$roomQuery = "
    SELECT r.room_id, rt.type_name as room_type, r.nearElevator, r.floor, r.availability, r.under_construction, 
           rt.max_capacity, r.created_at, r.updated_at
    FROM swx_room r
    JOIN swx_room_type rt ON r.room_type = rt.type_id
    $whereSql $orderSql";  // Apply the WHERE and ORDER BY clause

$stmt = $pdo->prepare($roomQuery);

// Bind parameters if filters are applied
$paramIndex = 1;
if ($roomTypeFilter) {
    $stmt->bindParam($paramIndex++, $roomTypeFilter, PDO::PARAM_INT);
}
if ($nearElevatorFilter) {
    $stmt->bindParam($paramIndex++, $nearElevatorFilter, PDO::PARAM_STR);
}
if ($floorFilter) {
    $stmt->bindParam($paramIndex++, $floorFilter, PDO::PARAM_INT);
}

$stmt->execute();
$roomResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if no rooms are found
if (empty($roomResult)) {
    $message = "No rooms found with the selected filters.";
    $message_type = "info";
}

// Check if a room is occupied based on current bookings
function checkRoomOccupancy($room_id, $pdo) {
    // Get today's date
    $today = date('Y-m-d');
    
    // Query to check if there are any ongoing or future bookings
    $query = "
        SELECT 1 
        FROM swx_booking 
        WHERE room_id = :room_id 
        AND (check_in_date <= :today AND check_out_date >= :today)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':room_id' => $room_id,
        ':today' => $today
    ]);
    
    // If a booking is found, the room is occupied
    return $stmt->rowCount() > 0;
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
    $roomCountQuery = "SELECT COUNT(*) AS room_count FROM swx_room";
    $roomCountResult = $pdo->query($roomCountQuery);
    $roomCount = $roomCountResult->fetch(PDO::FETCH_ASSOC)['room_count'];

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

    <!-- Filter Form -->
    <form method="GET" action="manage_rooms.php" class="mb-3">
        <div class="row">
            <div class="col">
                <label for="room_type_filter" class="form-label">Room Type</label>
                <select class="form-control" name="room_type_filter">
                    <option value="">All Room Types</option>
                    <?php
                    // Fetch room types for filter dropdown
                    $roomTypeQuery = "SELECT type_id, type_name FROM swx_room_type";
                    $roomTypeResult = $pdo->query($roomTypeQuery);
                    while ($roomType = $roomTypeResult->fetch(PDO::FETCH_ASSOC)) {
                        $selected = (isset($_GET['room_type_filter']) && $_GET['room_type_filter'] == $roomType['type_id']) ? 'selected' : '';
                        echo "<option value='{$roomType['type_id']}' {$selected}>{$roomType['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col">
                <label for="nearElevator_filter" class="form-label">Near Elevator</label>
                <select class="form-control" name="nearElevator_filter">
                    <option value="">All</option>
                    <option value="Ja" <?php echo (isset($_GET['nearElevator_filter']) && $_GET['nearElevator_filter'] == 'Ja') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Nei" <?php echo (isset($_GET['nearElevator_filter']) && $_GET['nearElevator_filter'] == 'Nei') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>

            <div class="col">
                <label for="floor_filter" class="form-label">Floor</label>
                <select class="form-control" name="floor_filter">
                    <option value="">All Floors</option>
                    <option value="1" <?php echo (isset($_GET['floor_filter']) && $_GET['floor_filter'] == '1') ? 'selected' : ''; ?>>Floor 1</option>
                    <option value="2" <?php echo (isset($_GET['floor_filter']) && $_GET['floor_filter'] == '2') ? 'selected' : ''; ?>>Floor 2</option>
                </select>
            </div>

            <div class="col">
                <label for="sort_order" class="form-label">Sort Order</label>
                <select class="form-control" name="sort_order">
                    <option value="ASC" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'DESC') ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>

            <div class="col">
                <button type="submit" class="btn btn-primary mt-4">Filter</button>
            </div>
        </div>
    </form>

    <!-- Button to toggle visibility of the Add Room form -->
    <button class="btn btn-success add-button" onclick="toggleForm()">Add New Room</button>

    <!-- Add New Room Form -->
    <div class="card" id="addRoomForm">
        <h3>Add New Room</h3>
        <form method="POST" action="manage_rooms.php">
            <div class="mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <select class="form-control" name="room_type">
                    <option value="">Select Room Type</option>
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
                <select class="form-control" name="nearElevator">
                    <option value="">Select Option</option>
                    <option value="Ja">Yes</option>
                    <option value="Nei">No</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="floor" class="form-label">Floor</label>
                <select class="form-control" name="floor">
                    <option value="">Select Floor</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="availability" class="form-label">Availability</label>
                <select class="form-control" name="availability">
                    <option value="">Select Availability</option>
                    <option value="ledig">Available</option>
                    <option value="opptatt">Occupied</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="under_construction" class="form-label">Under Construction</label>
                <select class="form-control" name="under_construction">
                    <option value="">Select Option</option>
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
                <?php if (isset($message)) { ?>
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($roomResult as $room) { ?>
                        <tr>
                            <td><?php echo $room['room_id']; ?></td>
                            <td><?php echo $room['room_type']; ?></td>
                            <td><?php echo $room['max_capacity']; ?></td>
                            <td><?php echo $room['nearElevator']; ?></td>
                            <td><?php echo $room['floor']; ?></td>
                            <td><?php 
                                if (checkRoomOccupancy($room['room_id'], $pdo)) {
                                    echo "opptatt"; // Occupied in Norwegian
                                } else {
                                    echo "ledig"; // Available in Norwegian
                                }
                            ?></td>
                            <td><?php echo $room['under_construction']; ?></td>
                            <td><?php echo $room['created_at']; ?></td>
                            <td><?php echo $room['updated_at']; ?></td>
                            <td>
                                <a href="view_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="manage_rooms.php?delete_id=<?php echo $room['room_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this room?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
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

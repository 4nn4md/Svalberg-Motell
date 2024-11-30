<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/config.php';

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
$roomResult = mysqli_query($conn, $roomQuery);
if (!$roomResult) {
    die("Error executing room query: " . mysqli_error($conn));
}

// Handle adding new room
if (isset($_POST['add_room'])) {
    $room_type = $_POST['room_type'];
    $nearElevator = $_POST['nearElevator'];
    $floor = $_POST['floor'];
    $availability = $_POST['availability'];
    $under_construction = $_POST['under_construction'];

    $insertQuery = "INSERT INTO room (room_type, nearElevator, floor, availability, under_construction) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("issss", $room_type, $nearElevator, $floor, $availability, $under_construction);
    if ($stmt->execute()) {
        $message = "Room added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding room: " . mysqli_error($conn);
        $message_type = "error";
    }
    $stmt->close();
}

// Handle deleting room
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM swx_room WHERE room_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Room deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting room: " . mysqli_error($conn);
        $message_type = "error";
    }
    $stmt->close();

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
        .logout-button:hover { background: #c82333; }
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

    <!-- Button to toggle visibility of the Add Room form -->
    <button class="btn btn-success add-button" onclick="toggleForm()">Add New Room</button>

    <!-- Add New Room Form -->
    <div class="card" id="addRoomForm">
        <h3>Add New Room</h3>
        <form method="POST" action="manage_rooms.php">
            <div class="mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <select class="form-control" name="room_type" id="room_type" required>
                    <?php
                    // Fetch room types for dropdown
                    $roomTypeQuery = "SELECT type_id, type_name FROM room_type";
                    $roomTypeResult = mysqli_query($conn, $roomTypeQuery);
                    while ($roomType = mysqli_fetch_assoc($roomTypeResult)) {
                        echo "<option value='{$roomType['type_id']}'>{$roomType['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nearElevator" class="form-label">Near Elevator</label>
                <select class="form-control" name="nearElevator" id="nearElevator" required>
                    <option value="Ja">Yes</option>
                    <option value="Nei">No</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="floor" class="form-label">Floor</label>
                <input type="number" class="form-control" name="floor" id="floor" required>
            </div>
            <div class="mb-3">
                <label for="availability" class="form-label">Availability</label>
                <select class="form-control" name="availability" id="availability" required>
                    <option value="ledig">Available</option>
                    <option value="opptatt">Occupied</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="under_construction" class="form-label">Under Construction</label>
                <select class="form-control" name="under_construction" id="under_construction" required>
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
                <?php while ($room = mysqli_fetch_assoc($roomResult)) { ?>
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

<!-- Modal for Success/Error Messages -->
<?php if (isset($message)) { ?>
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Room Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo $message; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show the modal if there is a message
        var myModal = new bootstrap.Modal(document.getElementById('messageModal'));
        myModal.show();
    </script>
<?php } ?>

<script>
    // Function to toggle the visibility of the Add Room Form
    function toggleForm() {
        var form = document.getElementById('addRoomForm');
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
</script>

</body>
</html>

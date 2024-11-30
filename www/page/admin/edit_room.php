<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';


// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
// TO DO: SJEKK OM SPØRRINGEN ER RIKTIG NÅ. 
// Fetch room data for editing
if (isset($_GET['id'])) {
    $room_id = $_GET['id'];
    $roomQuery = "SELECT r.room_id, r.room_type, r.nearElevator, r.floor, r.availability, r.under_construction, 
                         rt.max_capacity, rt.price, rt.description
                  FROM swx_room r
                  JOIN swx_room_type rt ON r.room_type = rt.type_id
                  WHERE r.room_id = ?";
    $stmt = $conn->prepare($roomQuery);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $roomResult = $stmt->get_result();
    $room = $roomResult->fetch_assoc();
    $stmt->close();
} else {
    header("Location: manage_rooms.php");
    exit();
}

// Handle editing the room
if (isset($_POST['edit_room'])) {
    // Get all the form values
    $room_type = $_POST['room_type'];
    $nearElevator = $_POST['nearElevator'];
    $floor = $_POST['floor'];
    $availability = $_POST['availability'];
    $under_construction = $_POST['under_construction'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Update the room data in the room and room_type tables
    $updateQuery = "UPDATE swx_room r
                    JOIN swx_room_type rt ON r.room_type = rt.type_id
                    SET r.room_type = ?, r.nearElevator = ?, r.floor = ?, r.availability = ?, r.under_construction = ?,
                        rt.price = ?, rt.description = ?
                    WHERE r.room_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("issssisi", $room_type, $nearElevator, $floor, $availability, $under_construction, $price, $description, $room_id);
    
    if ($stmt->execute()) {
        $message = "Room updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating room: " . mysqli_error($conn);
        $message_type = "error";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px; padding: 20px; }
        .logout-button { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .logout-button:hover { background: #c82333; }
        .back-button { position: absolute; top: 10px; left: 10px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="manage_rooms.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Edit Room</h1>

    <!-- Edit Room Form -->
    <div class="card">
        <h3>Edit Room Information</h3>
        <form method="POST" action="edit_room.php?id=<?php echo $room['room_id']; ?>">
            <!-- Room ID (Optional if you allow editing) -->
            <div class="mb-3">
                <label for="room_id" class="form-label">Room ID</label>
                <input type="text" class="form-control" id="room_id" name="room_id" value="<?php echo $room['room_id']; ?>" readonly>
            </div>

            <!-- Room Type -->
            <div class="mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <select class="form-control" name="room_type" id="room_type" required>
                    <?php
                    // Fetch room types for dropdown
                    $roomTypeQuery = "SELECT type_id, type_name FROM room_type";
                    $roomTypeResult = mysqli_query($conn, $roomTypeQuery);
                    while ($roomType = mysqli_fetch_assoc($roomTypeResult)) {
                        $selected = ($room['room_type'] == $roomType['type_id']) ? 'selected' : '';
                        echo "<option value='{$roomType['type_id']}' {$selected}>{$roomType['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Max Capacity (Readonly as it should be managed in room_type) -->
            <div class="mb-3">
                <label for="max_capacity" class="form-label">Max Capacity</label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity" value="<?php echo $room['max_capacity']; ?>" readonly>
            </div>

            <!-- Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo $room['price']; ?>" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $room['description']; ?></textarea>
            </div>

            <!-- Other Fields (Near Elevator, Floor, Availability, Under Construction) -->
            <div class="mb-3">
                <label for="nearElevator" class="form-label">Near Elevator</label>
                <select class="form-control" id="nearElevator" name="nearElevator" required>
                    <option value="Ja" <?php echo ($room['nearElevator'] == 'Ja') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Nei" <?php echo ($room['nearElevator'] == 'Nei') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="floor" class="form-label">Floor</label>
                <input type="number" class="form-control" id="floor" name="floor" value="<?php echo $room['floor']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="availability" class="form-label">Availability</label>
                <select class="form-control" id="availability" name="availability" required>
                    <option value="ledig" <?php echo ($room['availability'] == 'ledig') ? 'selected' : ''; ?>>Available</option>
                    <option value="opptatt" <?php echo ($room['availability'] == 'opptatt') ? 'selected' : ''; ?>>Occupied</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="under_construction" class="form-label">Under Construction</label>
                <select class="form-control" id="under_construction" name="under_construction" required>
                    <option value="Ja" <?php echo ($room['under_construction'] == 'Ja') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Nei" <?php echo ($room['under_construction'] == 'Nei') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>

            <button type="submit" name="edit_room" class="btn btn-primary">Update Room</button>
        </form>
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
                    <h5 class="modal-title" id="messageModalLabel">Room Update</h5>
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

</body>
</html>

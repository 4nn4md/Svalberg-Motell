<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch room data for editing
if (isset($_GET['id'])) {
    $room_id = $_GET['id'];
    $roomQuery = "SELECT r.room_id, r.room_type, r.nearElevator, r.floor, r.availability, r.under_construction, 
                         rt.max_capacity, rt.price, rt.description
                  FROM swx_room r
                  JOIN swx_room_type rt ON r.room_type = rt.type_id
                  WHERE r.room_id = ?";
    $stmt = $pdo->prepare($roomQuery);
    $stmt->bindParam(1, $room_id, PDO::PARAM_INT);
    $stmt->execute();
    $roomResult = $stmt->fetch(PDO::FETCH_ASSOC);
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

    // PHP Validation
    if (!in_array($floor, [1, 2])) {
        $message = "Floor must be either 1 or 2!";
        $message_type = "error";
    } elseif (empty($room_type) || empty($nearElevator) || empty($availability) || empty($under_construction) || empty($price) || empty($description)) {
        $message = "All fields are required!";
        $message_type = "error";
    } else {
        // If "Under Construction" is "Yes", set "Availability" to "Occupied"
        if ($under_construction == 'Ja') {
            $availability = 'opptatt'; // Automatically set Availability to "Occupied"
        }

        // Update the room data in the room and room_type tables
        $updateQuery = "UPDATE swx_room r
                        JOIN swx_room_type rt ON r.room_type = rt.type_id
                        SET r.room_type = ?, r.nearElevator = ?, r.floor = ?, r.availability = ?, r.under_construction = ?,
                            rt.price = ?, rt.description = ?
                        WHERE r.room_id = ?";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(1, $room_type, PDO::PARAM_STR);
        $stmt->bindParam(2, $nearElevator, PDO::PARAM_STR);
        $stmt->bindParam(3, $floor, PDO::PARAM_INT);
        $stmt->bindParam(4, $availability, PDO::PARAM_STR);
        $stmt->bindParam(5, $under_construction, PDO::PARAM_STR);
        $stmt->bindParam(6, $price, PDO::PARAM_STR);
        $stmt->bindParam(7, $description, PDO::PARAM_STR);
        $stmt->bindParam(8, $room_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $message = "Room updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating room: " . $pdo->errorInfo()[2];
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
    <title>Edit Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px; padding: 20px; }
        .logout-button { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .logout-button:hover { background: #c82333; }
        .back-button { position: absolute; top: 10px; left: 10px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .message { margin-bottom: 20px; }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="manage_rooms.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Edit Room</h1>

    <!-- Show Message -->
    <?php if (isset($message)) { ?>
        <div class="alert alert-<?php echo $message_type; ?> message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- Edit Room Form -->
    <div class="card">
        <h3>Edit Room Information</h3>
        <form method="POST" action="edit_room.php?id=<?php echo $roomResult['room_id']; ?>">

            <!-- Room ID (Optional if you allow editing) -->
            <div class="mb-3">
                <label for="room_id" class="form-label">Room ID</label>
                <input type="text" class="form-control" id="room_id" name="room_id" value="<?php echo $roomResult['room_id']; ?>" readonly>
            </div>

            <!-- Room Type -->
            <div class="mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <select class="form-control" name="room_type" id="room_type">
                    <option value="" disabled selected>Select Room Type</option>
                    <?php
                    // Fetch room types for dropdown
                    $roomTypeQuery = "SELECT type_id, type_name FROM swx_room_type";
                    $roomTypeResult = $pdo->query($roomTypeQuery);
                    while ($roomType = $roomTypeResult->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($roomResult['room_type'] == $roomType['type_id']) ? 'selected' : '';
                        echo "<option value='{$roomType['type_id']}' {$selected}>{$roomType['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Max Capacity (Readonly as it should be managed in room_type) -->
            <div class="mb-3">
                <label for="max_capacity" class="form-label">Max Capacity</label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity" value="<?php echo $roomResult['max_capacity']; ?>" readonly>
            </div>

            <!-- Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo $roomResult['price']; ?>" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $roomResult['description']; ?></textarea>
            </div>

            <!-- Other Fields (Near Elevator, Floor, Availability, Under Construction) -->
            <div class="mb-3">
                <label for="nearElevator" class="form-label">Near Elevator</label>
                <select class="form-control" id="nearElevator" name="nearElevator" required>
                    <option value="Ja" <?php echo ($roomResult['nearElevator'] == 'Ja') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Nei" <?php echo ($roomResult['nearElevator'] == 'Nei') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="floor" class="form-label">Floor</label>
                <select class="form-control" id="floor" name="floor" required>
                    <option value="1" <?php echo ($roomResult['floor'] == '1') ? 'selected' : ''; ?>>1</option>
                    <option value="2" <?php echo ($roomResult['floor'] == '2') ? 'selected' : ''; ?>>2</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="availability" class="form-label">Availability</label>
                <select class="form-control" id="availability" name="availability" required>
                    <option value="ledig" <?php echo ($roomResult['availability'] == 'ledig') ? 'selected' : ''; ?>>Available</option>
                    <option value="opptatt" <?php echo ($roomResult['availability'] == 'opptatt') ? 'selected' : ''; ?>>Occupied</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="under_construction" class="form-label">Under Construction</label>
                <select class="form-control" id="under_construction" name="under_construction" required>
                    <option value="Ja" <?php echo ($roomResult['under_construction'] == 'Ja') ? 'selected' : ''; ?>>Yes</option>
                    <option value="Nei" <?php echo ($roomResult['under_construction'] == 'Nei') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>

            <button type="submit" name="edit_room" class="btn btn-primary">Update Room</button>
        </form>
    </div>

</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>
</html>

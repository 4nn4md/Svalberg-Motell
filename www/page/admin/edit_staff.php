<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch staff data for editing
if (isset($_GET['id'])) {
    $staffId = $_GET['id'];
    $staffQuery = "SELECT staff_id, email, name, position FROM swx_staff WHERE staff_id = ?";
    $stmt = $conn->prepare($staffQuery);
    $stmt->bind_param("i", $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: manage_staff.php");
    exit();
}

// Handle staff update
if (isset($_POST['update_staff'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $position = $_POST['position'];  // Get the selected position
    $password = $_POST['password'];

    // Email validation: Check if email already exists (other than the current staff's email)
    $duplicateEmailQuery = "SELECT staff_id FROM swx_staff WHERE email = ? AND staff_id != ?";
    $stmt = $conn->prepare($duplicateEmailQuery);
    $stmt->bind_param("si", $email, $staffId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Duplicate email found
        $message = "Error: The email is already taken. Please choose a different email.";
        $message_type = "error";
    } else {
        // Update staff details
        if (!empty($password)) {
            // If password is provided, hash and update it
            $password = password_hash($password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE swx_taff SET email = ?, name = ?, position = ?, password = ? WHERE staff_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ssssi", $email, $name, $position, $password, $staffId);
        } else {
            // If no password change, just update email, name, and position
            $updateQuery = "UPDATE swx_staff SET email = ?, name = ?, position = ? WHERE staff_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sssi", $email, $name, $position, $staffId);
        }

        if ($stmt->execute()) {
            $message = "Staff member updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating staff: " . mysqli_error($conn);
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
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
<a href="manage_staff.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Edit Staff Member</h1>

    <!-- Edit Staff Form -->
    <div class="card">
        <h3>Edit Staff Information</h3>
        <form method="POST" action="edit_staff.php?id=<?php echo $staff['staff_id']; ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $staff['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $staff['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <select class="form-control" id="position" name="position" required>
                    <option value="Admin" <?php echo $staff['position'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Staff" <?php echo $staff['position'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                    <option value="Manager" <?php echo $staff['position'] == 'Manager' ? 'selected' : ''; ?>>Manager</option>
                    <option value="Receptionist" <?php echo $staff['position'] == 'Receptionist' ? 'selected' : ''; ?>>Receptionist</option>
                    <option value="Housekeeper" <?php echo $staff['position'] == 'Housekeeper' ? 'selected' : ''; ?>>Housekeeper</option>
                    <option value="Maintenance" <?php echo $staff['position'] == 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (Leave blank if not changing)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" name="update_staff" class="btn btn-primary">Update Staff</button>
        </form>
    </div>

</div>

<!-- Modal for Success/Error Messages -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Staff Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo isset($message) ? $message : ''; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<script>
    // Show the modal if there is a message
    <?php if (isset($message)) { ?>
        var myModal = new bootstrap.Modal(document.getElementById('messageModal'));
        myModal.show();
    <?php } ?>
</script>

</body>
</html>

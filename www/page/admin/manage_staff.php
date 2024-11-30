<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch staff information
$staffQuery = "SELECT staff_id, email, name, position FROM swx_staff";
$staffResult = mysqli_query($conn, $staffQuery);
if (!$staffResult) {
    die("Error executing staff query: " . mysqli_error($conn));
}

// Handle adding new staff
if (isset($_POST['add_staff'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Email validation: Check if email ends with '@svalberg.no'
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@svalberg\.no$/", $email)) {
        // If email doesn't match the required pattern, show an error message
        $message = "Error: The email must end with @svalberg.no.";
        $message_type = "error";
    } else {
        // Email validation: Check if email already exists
        $duplicateEmailQuery = "SELECT staff_id FROM swx_staff WHERE email = ?";
        $stmt = $conn->prepare($duplicateEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Duplicate email found
            $message = "Error: The email is already taken. Please choose a different email.";
            $message_type = "error";
        } else {
            // Email is valid, insert new staff into the database
            $insertQuery = "INSERT INTO swx_staff (email, password, position, name) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $email, $password, $position, $name);
            if ($stmt->execute()) {
                $message = "Staff member added successfully!";
                $message_type = "success";
            } else {
                $message = "Error adding staff: " . mysqli_error($conn);
                $message_type = "error";
            }
            $stmt->close();
        }
    }
}

// Handle staff deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM swx_staff WHERE staff_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Staff member deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting staff: " . mysqli_error($conn);
        $message_type = "error";
    }
    $stmt->close();

    // Redirect to the same page after deletion to avoid re-triggering the deletion on back
    header("Location: manage_staff.php"); // Redirect to the same page
    exit(); // Ensure no further code is executed after redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px; padding: 20px; }
        .logout-button { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .back-button { position: absolute; top: 10px; left: 10px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .add-button { margin-top: 20px; margin-bottom: 20px; }
        #addStaffForm { display: none; } /* Initially hide the form */
    </style>
</head>
<body>

<!-- Back Button -->
<a href="adminIndex.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Manage Staff</h1>

    <!-- Button to toggle visibility of the Add Staff form -->
    <button class="btn btn-success add-button" onclick="toggleForm()">Add New Staff Member</button>

    <!-- Add New Staff Form -->
    <div class="card" id="addStaffForm">
        <h3>Add New Staff Member</h3>
        <form method="POST" action="manage_staff.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <select class="form-control" id="position" name="position" required>
                    <option value="Admin">Admin</option>
                    <option value="Staff">Staff</option>
                    <option value="Manager">Manager</option>
                    <option value="Receptionist">Receptionist</option>
                    <option value="Housekeeper">Housekeeper</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="add_staff" class="btn btn-primary">Add Staff</button>
        </form>
    </div>

    <!-- Staff List Table -->
    <div class="card">
        <h3>Staff List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($staff = mysqli_fetch_assoc($staffResult)) { ?>
                    <tr>
                        <td><?php echo $staff['email']; ?></td>
                        <td><?php echo $staff['name']; ?></td>
                        <td><?php echo $staff['position']; ?></td>
                        <td>
                            <!-- Edit and Delete buttons for staff -->
                            <a href="edit_staff.php?id=<?php echo $staff['staff_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="manage_staff.php?delete_id=<?php echo $staff['staff_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
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
    // Function to toggle the visibility of the Add Staff Form
    function toggleForm() {
        var form = document.getElementById('addStaffForm');
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    // Show the modal if there is a message
    <?php if (isset($message)) { ?>
        var myModal = new bootstrap.Modal(document.getElementById('messageModal'));
        myModal.show();
    <?php } ?>
</script>

</body>
</html>

<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Initialize error message variable
$message = "";
$message_type = "";

// Define the $position variable to avoid the undefined variable error
$position = ""; // Default empty value for the position

// Fetch staff information
$staffQuery = "SELECT staff_id, email, first_name, last_name, position FROM swx_staff";
$staffResult = $pdo->query($staffQuery); // Use PDO query method
if (!$staffResult) {
    die("Error executing staff query: " . $pdo->errorInfo()[2]);
}

// Function to generate email based on first and last name with multiple formats, prioritizing certain options
function generateEmail($firstName, $lastName, $pdo) {
    // Primary Formats:
    // Format 1: First name + last name
    $email1 = strtolower($firstName) . strtolower($lastName) . '@svalberg.no';
    
    // Format 2: First letter of first name + last name
    $email2 = strtolower(substr($firstName, 0, 1)) . strtolower($lastName) . '@svalberg.no';
    
    // Format 3: First name + last name + number (e.g., JohnDoe1@svalberg.no)
    $email6 = strtolower($firstName) . strtolower($lastName) . '1' . '@svalberg.no'; // This is now a primary format
    
    // Store all primary email formats in an array
    $emailFormats = [$email6, $email1, $email2]; // Prioritize the first three options
    
    // Secondary Formats (used only if primary options conflict):
    // Format 4: First name only
    $email4 = strtolower($firstName) . '@svalberg.no';
    
    // Format 5: Last name only
    $email5 = strtolower($lastName) . '@svalberg.no';
    
    // Format 7: First name + dot + last name
    $email7 = strtolower($firstName) . '.' . strtolower($lastName) . '@svalberg.no';

    // Merge secondary formats into the primary formats array
    $emailFormats = array_merge($emailFormats, [$email4, $email5, $email7]);

    // Shuffle the array to randomize the order for selection (this isn't necessary since we prioritized the formats)
    shuffle($emailFormats);
    
    // Start with the first random format
    $email = $emailFormats[0];
    
    // Check if the email already exists in the database
    $duplicateEmailQuery = "SELECT staff_id FROM swx_staff WHERE email = ?";
    $stmt = $pdo->prepare($duplicateEmailQuery);
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    
    // If the email exists, append a number to make it unique
    $counter = 1;
    while ($stmt->rowCount() > 0) {
        // Check if we are dealing with one of the primary formats (email1, email2, email6)
        if (in_array($email, [$email1, $email2, $email6])) {
            // If the email is from the primary formats (first name + last name variants), keep appending a number
            $email = strtolower($firstName) . strtolower($lastName) . $counter . '@svalberg.no';
        } else {
            // Otherwise, append a number to the selected format (secondary formats like first name, last name, etc.)
            $email = preg_replace('/@/', $counter . '@', $email); // Add counter before '@'
        }
        $stmt->execute();  // Re-run the query to check if the new email is unique
        $counter++;
    }
    
    return $email;
}

// Handle adding new staff
if (isset($_POST['add_staff'])) {
    // Get form data
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $position = $_POST['position'];
    $password = $_POST['password'];

    // Generate email based on first and last name with multiple formats (and conflict resolution)
    $email = generateEmail($firstName, $lastName, $pdo);

    // PHP Validation for fields
    if (empty($email)) {
        $message = "Email is required.";
        $message_type = "error";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@svalberg\.no$/", $email)) {
        $message = "Error: The email must end with @svalberg.no.";
        $message_type = "error";
    } elseif (empty($firstName) || empty($lastName)) {
        $message = "First and Last Name are required.";
        $message_type = "error";
    } elseif (empty($password)) {
        $message = "Password is required.";
        $message_type = "error";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d.*\d)(?=.*[\W_]).{9,}$/', $password)) {
        $message = "Password must be at least 9 characters long, contain one uppercase letter, two digits, and one special character.";
        $message_type = "error";
    } elseif (empty($position)) {
        $message = "Position is required.";
        $message_type = "error";
    } else {
        // Hash the password
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert new staff into the database
        $insertQuery = "INSERT INTO swx_staff (email, password, position, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->bindParam(2, $password_hashed, PDO::PARAM_STR);
        $stmt->bindParam(3, $position, PDO::PARAM_STR);
        $stmt->bindParam(4, $firstName, PDO::PARAM_STR);
        $stmt->bindParam(5, $lastName, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $message = "Staff member added successfully!";
            $message_type = "success";
        } else {
            $message = "Error adding staff: " . $pdo->errorInfo()[2];
            $message_type = "error";
        }
    }
}

// Handle staff deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM swx_staff WHERE staff_id = ?";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(1, $delete_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $message = "Staff member deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting staff: " . $pdo->errorInfo()[2];
        $message_type = "error";
    }

    // Redirect to the same page after deletion to avoid re-triggering the deletion on back
    header("Location: manage_staff.php");
    exit();
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

    <!-- Show Message -->
    <?php if ($message) { ?>
        <div class="alert alert-<?php echo $message_type; ?> message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- Add New Staff Form -->
    <div class="card" id="addStaffForm">
        <h3>Add New Staff Member</h3>
        <form method="POST" action="manage_staff.php">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName ?? ''); ?>" placeholder="Enter first name" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName ?? ''); ?>" placeholder="Enter last name" required>
            </div>

            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <select class="form-control" id="position" name="position" required>
                    <option value="" disabled selected>Select Position</option>
                    <option value="Admin" <?php echo ($position == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="Staff" <?php echo ($position == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                    <option value="Manager" <?php echo ($position == 'Manager') ? 'selected' : ''; ?>>Manager</option>
                    <option value="Receptionist" <?php echo ($position == 'Receptionist') ? 'selected' : ''; ?>>Receptionist</option>
                    <option value="Housekeeper" <?php echo ($position == 'Housekeeper') ? 'selected' : ''; ?>>Housekeeper</option>
                    <option value="Maintenance" <?php echo ($position == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password ?? ''); ?>" placeholder="Enter password" required>
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
                <?php while ($staff = $staffResult->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $staff['email']; ?></td>
                        <td><?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></td>
                        <td><?php echo $staff['position']; ?></td>
                        <td>
                            <a href="edit_staff.php?id=<?php echo $staff['staff_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="manage_staff.php?delete_id=<?php echo $staff['staff_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
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
    // Function to toggle the visibility of the Add Staff Form
    function toggleForm() {
        var form = document.getElementById('addStaffForm');
        form.style.display = form.style.display === "none" ? "block" : "none";
    }
</script>

</body>
</html>

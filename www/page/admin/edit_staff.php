<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); // Ensure sanitize function is included

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch staff data for editing
if (isset($_GET['id'])) {
    $staffId = sanitize($_GET['id']); // Sanitize the staff ID from the URL
    $staffQuery = "SELECT staff_id, email, first_name, last_name, position FROM swx_staff WHERE staff_id = ?";
    $stmt = $pdo->prepare($staffQuery);  // Use PDO here
    $stmt->bindParam(1, $staffId, PDO::PARAM_INT);
    $stmt->execute();
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch data directly from the query result
} else {
    header("Location: manage_staff.php");
    exit();
}

// Initialize message variable
$message = '';
$message_type = '';
$validPassword = true; // Flag for password validity

// Handle staff update
if (isset($_POST['update_staff'])) {
    // Sanitize form data
    $email = sanitize($_POST['email']);
    $firstName = sanitize($_POST['first_name']);
    $lastName = sanitize($_POST['last_name']);
    $position = sanitize($_POST['position']);  // Get the selected position
    $password = sanitize($_POST['password']);

    // Email validation: Check if email already exists (other than the current staff's email)
    $duplicateEmailQuery = "SELECT staff_id FROM swx_staff WHERE email = ? AND staff_id != ?";
    $stmt = $pdo->prepare($duplicateEmailQuery);  // Use PDO here
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->bindParam(2, $staffId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Duplicate email found
        $message = "Error: The email is already taken. Please choose a different email.";
        $message_type = "error";
    } else {
        // Password validation (if provided)
        if (!empty($password)) {
            // Check password with regex (at least 9 characters, one uppercase, two digits, one special character)
            if (!preg_match('/^(?=.*[A-Z])(?=.*\d.*\d)(?=.*[\W_]).{9,}$/', $password)) {
                // Password doesn't meet the criteria
                $validPassword = false; // Set flag to false
                $message = "Password must be at least 9 characters long, contain one uppercase letter, two digits, and one special character.";
                $message_type = "error";
            } else {
                // If password is valid, hash and update it
                $password = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        if ($validPassword) {
            // If password is valid or not provided, update the staff details
            if (!empty($password)) {
                $updateQuery = "UPDATE swx_staff SET email = ?, first_name = ?, last_name = ?, position = ?, password = ? WHERE staff_id = ?";
                $stmt = $pdo->prepare($updateQuery);  // Use PDO here
                $stmt->bindParam(1, $email, PDO::PARAM_STR);
                $stmt->bindParam(2, $firstName, PDO::PARAM_STR);
                $stmt->bindParam(3, $lastName, PDO::PARAM_STR);
                $stmt->bindParam(4, $position, PDO::PARAM_STR);
                $stmt->bindParam(5, $password, PDO::PARAM_STR);
                $stmt->bindParam(6, $staffId, PDO::PARAM_INT);
            } else {
                // If no password change, just update email, first name, last name, and position
                $updateQuery = "UPDATE swx_staff SET email = ?, first_name = ?, last_name = ?, position = ? WHERE staff_id = ?";
                $stmt = $pdo->prepare($updateQuery);  // Use PDO here
                $stmt->bindParam(1, $email, PDO::PARAM_STR);
                $stmt->bindParam(2, $firstName, PDO::PARAM_STR);
                $stmt->bindParam(3, $lastName, PDO::PARAM_STR);
                $stmt->bindParam(4, $position, PDO::PARAM_STR);
                $stmt->bindParam(5, $staffId, PDO::PARAM_INT);
            }

            // Execute the update query only if password is valid
            if (isset($stmt) && $stmt->execute()) {
                $message = "Staff member updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating staff: " . $pdo->errorInfo()[2];
                $message_type = "error";
            }
        }
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
        .email-container { display: flex; align-items: center; }
        .generate-btn { margin-left: 10px; }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="manage_staff.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Edit Staff Member</h1>

    <!-- Show Message -->
    <?php if (isset($message)) { ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- Edit Staff Form -->
    <div class="card">
        <h3>Edit Staff Information</h3>
        <form method="POST" action="edit_staff.php?id=<?php echo sanitize($staff['staff_id']); ?>">

            <div class="mb-3 email-container">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize($staff['email']); ?>" required>
                <button type="button" class="btn btn-secondary generate-btn" onclick="generateNewEmail()">Generate New Email</button>
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo sanitize($staff['first_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo sanitize($staff['last_name']); ?>" required>
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

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<script>
// Function to generate a new email based on first and last name
function generateNewEmail() {
    var firstName = document.getElementById('first_name').value;
    var lastName = document.getElementById('last_name').value;

    // Clear the current email field (optional)
    document.getElementById('email').value = '';

    // Send data to PHP to generate a new email
    fetch('generate_email.php', {
        method: 'POST',
        body: JSON.stringify({ firstName: firstName, lastName: lastName }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('email').value = data.email; // Set the new email in the input field
        } else {
            alert('Error generating email: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error generating email: ' + error);
    });
}
</script>

</body>
</html>

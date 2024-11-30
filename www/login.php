<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';

// Define max login attempts and the cooldown period (in seconds)
define('MAX_ATTEMPTS', 3);
define('COOLDOWN_TIME', 180); // 180 seconds = 3 minutes

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        // Get email and password from the form
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if the user exists in the 'staff' table (admin/staff users)
        $stmt = $conn->prepare("SELECT staff_id, password, position, login_attempts, locked_until FROM swx_staff WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // If the email is not found in 'staff', check the 'users' table (guest users)
        if ($stmt->num_rows == 0) {
            $stmt = $conn->prepare("SELECT user_id, password, login_attempts, locked_until FROM swx_users WHERE username = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
        }

        // Initialize variables
        $role = null; // For guests, no role exists
        $hashed_password = null; // To hold the password hash from DB
        $login_attempts = null; // For login attempt tracking
        $locked_until = null; // For lockout time

        // Bind the result for both staff and guest users
        if ($stmt->num_rows > 0) {
            if ($stmt->field_count == 5) {
                // If there are 5 columns (staff table), bind all 5 fields
                $stmt->bind_result($user_id, $hashed_password, $role, $login_attempts, $locked_until);
            } else {
                // If there are 4 columns (users table), bind only the relevant 4 fields
                $stmt->bind_result($user_id, $hashed_password, $login_attempts, $locked_until);
                $role = 'user'; // Guests will not have a 'role' field, so default to 'user'
            }
            $stmt->fetch();
        } else {
            // No user found, handle error
            echo "User not found.";
            exit();
        }

        $current_time = time();

        // If the user is locked out, show the lockout time
        if ($locked_until && strtotime($locked_until) > $current_time) {
            $time_remaining = strtotime($locked_until) - $current_time;
            $minutes_remaining = ceil($time_remaining / 60);
            echo "You are locked out. Please try again in $minutes_remaining minute(s).";
            exit();
        }

        // If the email is found and password matches, proceed
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role; // Set role as 'admin', 'staff' or other staff, or 'user'
            $_SESSION['attempts'] = 0; // Reset attempts on successful login
            $_SESSION['locked_until'] = null; // Clear locked time on success

            // Reset login attempts and lockout time in the database after a successful login
            if ($role == 'Admin' || $role == 'Staff') {
                $update_stmt = $conn->prepare("UPDATE swx_staff SET login_attempts = 0, locked_until = NULL WHERE email = ?");
                $update_stmt->bind_param("s", $email);
            } else {
                $update_stmt = $conn->prepare("UPDATE swx_users SET login_attempts = 0, locked_until = NULL WHERE username = ?");
                $update_stmt->bind_param("s", $email);
            }
            $update_stmt->execute();

            // Redirect based on user role
            if ($role == 'Admin') {
                header("Location: page/admin/adminIndex.php");  // Redirect to the admin dashboard
            } elseif ($role == 'Staff') {
                header("Location: staff_dashboard.php");  // Redirect to the staff dashboard
            } elseif ($role == 'Manager') {
                header("Location: manager_dashboard.php");  // Redirect to the manager dashboard
            } elseif ($role == 'Receptionist') {
                header("Location: receptionist_dashboard.php");  // Redirect to the receptionist dashboard
            } elseif ($role == 'Housekeeper') {
                header("Location: housekeeper_dashboard.php");  // Redirect to the housekeeper dashboard
            } elseif ($role == 'Maintenance') {
                header("Location: maintenance_dashboard.php");  // Redirect to the maintenance dashboard
            } else {
                header("Location: index1.php");  // Redirect to the guest dashboard
            }
            exit();
        } else {
            handle_failed_attempt($email);
        }
    }

    // Handle registration
    if (isset($_POST['register'])) {
        // Get the form data
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['reg_email'];
        $password = $_POST['reg_password'];
        $phone = $_POST['tlf'];

        // Check if the email is from a staff domain (no @svalberg.no for guests)
        if (preg_match('/@svalberg\.no$/', $email)) {
            echo "Error: Staff emails (ending with @svalberg.no) cannot be used for guest registration.";
            exit();
        }

        // Check if the email already exists in the users table (guests)
        $stmt = $conn->prepare("SELECT user_id FROM swx_users WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // If the email is already registered, stop registration
        if ($stmt->num_rows > 0) {
            echo "Error: This email is already registered.";
            exit();
        }

        // Hash the password before saving
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the 'users' table
        $stmt = $conn->prepare("INSERT INTO swx_users (firstName, lastName, tlf, username, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
        $stmt->bind_param("ssiss", $firstName, $lastName, $phone, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Registration successful! You can now log in.";
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            echo "Error: Could not register the user.";
        }

        $stmt->close();
    }

    $conn->close();
}

// Function to handle failed login attempts
function handle_failed_attempt($email) {
    global $conn;

    // Retrieve current failed attempts and locked_until for the user
    $stmt = $conn->prepare("SELECT login_attempts, locked_until FROM swx_users WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($login_attempts, $locked_until);
    $stmt->fetch();

    // If the user is locked out, don't allow further attempts
    if ($locked_until && strtotime($locked_until) > time()) {
        $time_remaining = strtotime($locked_until) - time();
        $minutes_remaining = ceil($time_remaining / 60);
        echo "Too many failed login attempts. Please try again in $minutes_remaining minute(s).";
        exit();
    }

    // Increment login attempts for each failed login
    if ($login_attempts >= MAX_ATTEMPTS) {
        $locked_until = date("Y-m-d H:i:s", time() + COOLDOWN_TIME);  // Set the lockout time
        $login_attempts = 0; // Reset attempts after reaching max
    } else {
        $login_attempts++;
    }

    // Update the login attempts and locked_until in the database
    if ($stmt->num_rows == 0) {
        // User doesn't exist, handle as needed
        echo "User not found.";
        exit();
    }

    $update_stmt = $conn->prepare("UPDATE swx_users SET login_attempts = ?, locked_until = ? WHERE username = ?");
    $update_stmt->bind_param("iss", $login_attempts, $locked_until, $email);
    $update_stmt->execute();

    // Inform the user about failed attempts
    if ($login_attempts >= MAX_ATTEMPTS) {
        echo "Too many failed login attempts. You are locked out for 3 minutes.";
    } else {
        echo "Incorrect login details. You have " . (MAX_ATTEMPTS - $login_attempts) . " attempt(s) left.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/Svalberg-Motell/www/assets/css/loginStyle.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow login-card">
            <h3 class="text-center mb-4" id="form-title">Login</h3>

            <!-- Login Form -->
            <form action="login.php" method="post" id="login-form">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                <p class="text-center mt-3">
                    Don't have an account? <a href="#" onclick="toggleForm()">Register here</a>
                </p>
            </form>

            <!-- Registration Form -->
            <form action="login.php" method="post" id="register-form" style="display: none;">
                <div class="form-group mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                </div>
                <div class="form-group mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                </div>
                <div class="form-group mb-3">
                    <label for="reg_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="reg_email" name="reg_email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="reg_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="reg_password" name="reg_password" required>
                </div>
                <div class="form-group mb-3">
                    <label for="tlf" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="tlf" name="tlf" required>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                <p class="text-center mt-3">
                    Already have an account? <a href="#" onclick="toggleForm()">Login here</a>
                </p>
            </form>

            <!-- Message after registration or error -->
            <?php if (isset($message)) { ?>
                <div class="alert alert-<?php echo $message_type; ?> mt-3">
                    <?php echo $message; ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        // Toggle between Login and Register forms
        function toggleForm() {
            const loginForm = document.getElementById("login-form");
            const registerForm = document.getElementById("register-form");
            const formTitle = document.getElementById("form-title");

            if (loginForm.style.display === "none") {
                loginForm.style.display = "block";
                registerForm.style.display = "none";
                formTitle.textContent = "Login";
            } else {
                loginForm.style.display = "none";
                registerForm.style.display = "block";
                formTitle.textContent = "Register";
            }
        }
    </script>
</body>
</html>

<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once($_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php');

// Define max login attempts and the cooldown period (in seconds)
define('MAX_ATTEMPTS', 3);
define('COOLDOWN_TIME', 180); // 180 seconds = 3 minutes

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $password = "";
    $email_err = $password_err = "";

    if (isset($_POST['login'])) {
        // Validate email
        if (empty($_POST['email'])) {
            $email_err = "Email is required.";
        } else {
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_err = "Invalid email format.";
            }
        }

        // Validate password
        if (empty($_POST['password'])) {
            $password_err = "Password is required.";
        } else {
            $password = trim($_POST['password']);
            // Regular expression to check for at least one uppercase letter, two digits, one special character, and minimum length of 9
            if (!preg_match('/^(?=.*[A-Z])(?=.*\d.*\d)(?=.*[\W_]).{9,}$/', $password)) {
                $password_err = "Incorrect password, try again.";
            }
        }

        // Proceed if no errors
        if (empty($email_err) && empty($password_err)) {
            // Check if the user exists in the 'staff' table (admin/staff users)
            $stmt = $pdo->prepare("SELECT staff_id, password, position, login_attempts, locked_until FROM swx_staff WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // If the email is not found in 'staff', check the 'users' table (guest users)
            if (!$result) {
                $stmt = $pdo->prepare("SELECT user_id, password, login_attempts, locked_until FROM swx_users WHERE username = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Initialize variables
            $role = null; // For guests, no role exists
            $hashed_password = null; // To hold the password hash from DB
            $login_attempts = null; // For login attempt tracking
            $locked_until = null; // For lockout time

            // If a result is found, assign values
            if ($result) {
                $user_id = $result['staff_id'] ?? $result['user_id'];
                $hashed_password = $result['password'];
                $login_attempts = $result['login_attempts'];
                $locked_until = $result['locked_until'];
                $role = $result['position'] ?? 'user';
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
                session_regenerate_id(true); // Regenerate session ID to prevent session fixation

                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role; // Set role as 'admin', 'staff', or 'user'
                $_SESSION['attempts'] = 0; // Reset attempts on successful login
                $_SESSION['locked_until'] = null; // Clear locked time on success

                // Reset login attempts and lockout time in the database after a successful login
                if ($role == 'Admin' || $role == 'Staff') {
                    $update_stmt = $pdo->prepare("UPDATE swx_staff SET login_attempts = 0, locked_until = NULL WHERE email = :email");
                } else {
                    $update_stmt = $pdo->prepare("UPDATE swx_users SET login_attempts = 0, locked_until = NULL WHERE username = :email");
                }
                $update_stmt->bindParam(':email', $email);
                $update_stmt->execute();

                // Redirect based on user role
                if ($role == 'Admin') {
                    header("Location: page/admin/adminIndex.php");
                } elseif ($role == 'Staff') {
                    header("Location: staff_dashboard.php");
                } elseif ($role == 'Manager') {
                    header("Location: manager_dashboard.php");
                } elseif ($role == 'Receptionist') {
                    header("Location: receptionist_dashboard.php");
                } elseif ($role == 'Housekeeper') {
                    header("Location: housekeeper_dashboard.php");
                } elseif ($role == 'Maintenance') {
                    header("Location: maintenance_dashboard.php");
                } else {
                    if (isset($_SESSION['redirect_step'])) {
                        error_log("Redirect step found: " . $_SESSION['redirect_step']);
                        $redirect_step = $_SESSION['redirect_step'];
                        unset($_SESSION['redirect_step']);
                        header("Location: page/user/$redirect_step.php");
                        exit();
                    } else {
                        error_log("No redirect step found. Redirecting to index1.php.");
                        header("Location: index1.php");
                        exit();
                    }
                }
                exit();
            } else {
                // Handle failed login attempt
                handle_failed_attempt($email);
            }
        }
    }
}

// Function to handle failed login attempts
function handle_failed_attempt($email) {
    global $pdo;

    // Retrieve current failed attempts and locked_until for the user (check both users and staff)
    $stmt = $pdo->prepare("SELECT login_attempts, locked_until FROM swx_users WHERE username = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        // If not found in users table, check the staff table
        $stmt = $pdo->prepare("SELECT login_attempts, locked_until FROM swx_staff WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($result) {
        $login_attempts = $result['login_attempts'];
        $locked_until = $result['locked_until'];
    } else {
        echo "User not found.";
        exit();
    }

    // If the user is locked out, don't allow further attempts
    if ($locked_until && strtotime($locked_until) > time()) {
        $time_remaining = strtotime($locked_until) - time();
        $minutes_remaining = ceil($time_remaining / 60);
        echo "Too many failed login attempts. Please try again in $minutes_remaining minute(s).";
        exit();
    }

    // Increment login attempts for each failed login
    $login_attempts = ($login_attempts >= MAX_ATTEMPTS) ? 0 : $login_attempts + 1;
    $locked_until = ($login_attempts >= MAX_ATTEMPTS) ? date("Y-m-d H:i:s", time() + COOLDOWN_TIME) : null;

    if (strpos($email, '@svalberg.no') !== false) { // It's from the staff domain
        $update_stmt = $pdo->prepare("UPDATE swx_staff SET login_attempts = :login_attempts, locked_until = :locked_until WHERE email = :email");
    } else {
        $update_stmt = $pdo->prepare("UPDATE swx_users SET login_attempts = :login_attempts, locked_until = :locked_until WHERE username = :email");
    }

    $update_stmt->bindParam(':login_attempts', $login_attempts, PDO::PARAM_INT);
    $update_stmt->bindParam(':locked_until', $locked_until);
    $update_stmt->bindParam(':email', $email);
    $update_stmt->execute();

    // Inform the user about failed attempts
    if ($locked_until) {
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
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>">
                    <span class="text-danger"><?php echo $email_err ?? ''; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password ?? '', ENT_QUOTES); ?>">
                    <span class="text-danger"><?php echo $password_err ?? ''; ?></span>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                <p class="text-center mt-3">
                    Don't have an account? <a href="register.php">Register here</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
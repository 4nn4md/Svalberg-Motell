<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'assets/inc/config.php';

// Handle login and registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        // Login form handling
        $email = $_POST['email'];  // Use email instead of username
        $password = $_POST['password'];

        // Prepare SQL query to check user credentials
        $stmt = $conn->prepare("SELECT password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email); // Use email to find the user
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password, $user_role);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['email'] = $email;  // Store email in session
                $_SESSION['role'] = $user_role;

                // Redirect based on role
                if ($user_role == "staff") {
                    header("Location: adminIndex.php");  // Admin
                    exit();
                } else {
                    header("Location: index1.php");  // User
                    exit();
                }
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "Invalid login details.";
        }
        $stmt->close();
    } elseif (isset($_POST['register'])) {
        // Registration form handling
        $email = $_POST['reg_email'];
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);

        // Determine user role based on email domain
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@svalberg\.no$/', $email)) {
            $role = 'staff';  // Use admin role for @svalberg.no
        } else {
            $role = 'user';  // Use regular user role
        }

        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo "The email is already registered. Choose another one.";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $password, $role);

            if ($stmt->execute()) {
                echo "Registration successful! You can now log in.";
            } else {
                echo "Error: Could not register user.";
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Register</title>
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
                    <label for="reg_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="reg_email" name="reg_email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="reg_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="reg_password" name="reg_password" required>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                <p class="text-center mt-3">
                    Already have an account? <a href="#" onclick="toggleForm()">Login here</a>
                </p>
            </form>
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

        // Change button text based on whether the user is logged in
        window.onload = function() {
            const loginLogoutButton = document.getElementById("login-logout-btn");

            <?php if (isset($_SESSION['email'])): ?>
                loginLogoutButton.innerText = "Logout";
            <?php else: ?>
                loginLogoutButton.innerText = "Login";
            <?php endif; ?>
        };
    </script>
</body>
</html>

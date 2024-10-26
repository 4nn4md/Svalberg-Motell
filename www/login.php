<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'assets/inc/config.php';

// Your existing code...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determine if we're handling login or registration
    if (isset($_POST['login'])) {
        // Login form handling
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL query to check user credentials
        $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password, $user_role);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user_role;

                // Redirect based on role
                if ($user_role == "staff") {
                    header("Location: adminIndex.php"); // Redirecting staff
                    exit();
                } else {
                    header("Location: index1.php"); // Redirecting users
                    exit();
                }
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "Invalid credentials.";
        }
        $stmt->close();
    } elseif (isset($_POST['register'])) {
        // Registration form handling
        $username = $_POST['reg_username'];
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);

        // Determine the role based on the email domain
        if (filter_var($username, FILTER_VALIDATE_EMAIL) && preg_match('/@svalberg\.no$/', $username)) {
            $role = 'staff'; // If the email domain is @svalberg.no, set role to staff
        } else {
            $role = 'user'; // Otherwise, set role to user
        }

        // Registration form handling
if (isset($_POST['register'])) {
    // Registration form handling
    $username = $_POST['reg_username'];
    $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);
    
    // Determine the role based on the username
    $role = (preg_match('/@svalberg\.no$/', $username)) ? 'staff' : 'user';

    // Prepare SQL query to check if the username already exists
    $checkStmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "Username already exists. Please choose another one.";
    } else {
        // Prepare SQL query to insert a new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            echo "Registration successful! You can now log in.";
        } else {
            echo "Error: Could not register user.";
        }
        $stmt->close();
    }
    $checkStmt->close();
}


        // Prepare SQL query to insert a new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            echo "Registration successful! You can now log in.";
        } else {
            echo "Error: Could not register user.";
        }
        $stmt->close();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="http://localhost/Svalberg-Motell/www/assets/css/loginStyle.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow login-card">
            <h3 class="text-center mb-4" id="form-title">Login</h3>

            <!-- Login Form -->
            <form action="login.php" method="post" id="login-form">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username (Email)</label>
                    <input type="text" class="form-control" id="username" name="username" required>
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
                    <label for="reg_username" class="form-label">Username (Email)</label>
                    <input type="text" class="form-control" id="reg_username" name="reg_username" required>
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

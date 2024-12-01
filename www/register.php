<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once($_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php');

// Handle registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $lastName = $email = $password = $phone = "";
    $firstName_err = $lastName_err = $email_err = $password_err = $phone_err = "";

    if (isset($_POST['register'])) {
        // Validate first name
        if (empty($_POST['firstName'])) {
            $firstName_err = "First name is required.";
        } else {
            $firstName = trim($_POST['firstName']);
        }

        // Validate last name
        if (empty($_POST['lastName'])) {
            $lastName_err = "Last name is required.";
        } else {
            $lastName = trim($_POST['lastName']);
        }

        // Validate email
        if (empty($_POST['reg_email'])) {
            $email_err = "Email is required.";
        } else {
            $email = filter_var(trim($_POST['reg_email']), FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_err = "Invalid email format.";
            }
        }

        // Validate password
        if (empty($_POST['reg_password'])) {
            $password_err = "Password is required.";
        } else {
            $password = trim($_POST['reg_password']);
            // Regular expression to check for at least one uppercase letter, two digits, one special character, and minimum length of 9
            if (!preg_match('/^(?=.*[A-Z])(?=.*\d.*\d)(?=.*[\W_]).{9,}$/', $password)) {
                $password_err = "Password must contain at least one uppercase letter, two digits, one special character, and be at least 9 characters long.";
            }
        }

        // Validate phone
        if (empty($_POST['tlf'])) {
            $phone_err = "Phone number is required.";
        } else {
            $phone = trim($_POST['tlf']);
        }

        // Proceed if no errors
        if (empty($firstName_err) && empty($lastName_err) && empty($email_err) && empty($password_err) && empty($phone_err)) {
            // Check if the email already exists in the users table (guests)
            $stmt = $pdo->prepare("SELECT user_id FROM swx_users WHERE username = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // If the email is already registered, stop registration
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "Error: This email is already registered.";
                exit();
            }

            // Hash the password before saving
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the 'users' table
            $stmt = $pdo->prepare("INSERT INTO swx_users (firstName, lastName, tlf, username, password, role) VALUES (:firstName, :lastName, :phone, :username, :password, 'user')");
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':username', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
               //echo "Registration successful! You can now log in.";
                header("Location: login.php");
                exit();
            } else {
                echo "Error: Could not register the user.";
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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/Svalberg-Motell/www/assets/css/loginStyle.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow login-card">
            <h3 class="text-center mb-4" id="form-title">Register</h3>

            <!-- Registration Form -->
            <form action="register.php" method="post" id="register-form">
                <div class="form-group mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName ?? '', ENT_QUOTES); ?>">
                    <span class="text-danger"><?php echo $firstName_err ?? ''; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName ?? '', ENT_QUOTES); ?>">
                    <span class="text-danger"><?php echo $lastName_err ?? ''; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="reg_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="reg_email" name="reg_email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>">
                    <span class="text-danger"><?php echo $email_err ?? ''; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="reg_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="reg_password" name="reg_password" value="<?php echo htmlspecialchars($password ?? '', ENT_QUOTES); ?>">
                    <span class="text-danger"><?php echo $password_err ?? ''; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="tlf" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="tlf" name="tlf" value="<?php echo htmlspecialchars($phone ?? '', ENT_QUOTES); ?>">
                    <span class="text-danger"><?php echo $phone_err ?? ''; ?></span>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                <p class="text-center mt-3">
                    Already have an account? <a href="login.php">Login here</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>

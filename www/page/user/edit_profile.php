<?php
// Include the database connection and functions
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php");

// Start the session to access user information
session_start();

// Check if user is logged in (assume user ID is stored in session)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data from the database
$stmt = $pdo->prepare("SELECT * FROM swx_users WHERE user_id = ?");
$stmt->execute([$user_id]);

// Fetch the user data, check if the query was successful
$user = $stmt->fetch();

// Check if the user data is valid (not false)
if (!$user || !is_array($user)) {
    // If no user data was found or fetch failed, display an error message and exit
    echo "No user data found or query failed.";
    exit();
}

// If the user data is valid, you can safely access the array
$firstName = $user['firstName'];
$lastName = $user['lastName'];
$email = $user['username'];  // Assuming 'username' stores the email
$tlf = $user['tlf'];  // Assuming 'tlf' stores the user's phone number

// Process form submission to update user data (excluding email)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input data
    $first_name = sanitize($_POST['firstName']);
    $last_name = sanitize($_POST['lastName']);
    $tlf = sanitize($_POST['tlf']);
    
    // Validate that no field is empty
    if (empty($first_name) || empty($last_name) || empty($tlf)) {
        $error_message = "All fields except email are required!";
    } else {
        // Check if phone number is valid (it should be an integer)
        if (!is_numeric($tlf) || strlen($tlf) < 5 || strlen($tlf) > 15) {
            $error_message = "Please enter a valid phone number.";
        } else {
            // Update user data in the database (excluding email)
            $update_stmt = $pdo->prepare("UPDATE swx_users SET firstName = ?, lastName = ?, tlf = ? WHERE user_id = ?");
            $update_stmt->execute([$first_name, $last_name, $tlf, $user_id]);
                
            // Display success message
            $success_message = "Profile updated successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"], input[type="email"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .back-btn {
            background-color: #f8f8f8;
            color: #333;
            padding: 10px 15px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

    <a href="user_profile_two.php" class="back-btn">&larr; Back to Profile</a>

    <div class="container">
        <h1>Edit Profile</h1>

        <?php if (isset($success_message)) { ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php } elseif (isset($error_message)) { ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php } ?>

        <form method="post" action="">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required><br>

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required><br>

            <label for="tlf">Mobile Number:</label>
            <input type="text" id="tlf" name="tlf" value="<?php echo htmlspecialchars($tlf); ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly><br>

            <input type="submit" value="Update Profile">
        </form>
    </div>

</body>
</html>

<?php
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/functions.php"); // Ensure sanitize function is included

// Read the input data from the JavaScript request
$inputData = json_decode(file_get_contents('php://input'), true);
$firstName = sanitize($inputData['firstName']);  // Sanitize first name
$lastName = sanitize($inputData['lastName']);    // Sanitize last name

// Function to generate a random email based on multiple formats
function generateEmail($firstName, $lastName, $pdo) {
    // Create different email formats
    $formats = [
        strtolower($firstName) . strtolower($lastName) . '@svalberg.no',        // john.doe@svalberg.no
        strtolower(substr($firstName, 0, 1)) . strtolower($lastName) . '@svalberg.no', // j.doe@svalberg.no
        strtolower($firstName) . strtolower(substr($lastName, 0, 1)) . '@svalberg.no', // johnd@svalberg.no
        strtolower($firstName) . '.' . strtolower($lastName) . '@svalberg.no', // john.doe@svalberg.no
        strtolower($firstName) . strtolower($lastName) . rand(1, 100) . '@svalberg.no', // john.doe23@svalberg.no (with random number)
    ];

    // Randomly shuffle the formats array to choose one format at random
    shuffle($formats);

    // Try the first random format
    $email = $formats[0];

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT staff_id FROM swx_staff WHERE email = ?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();

    // If the email exists, try the next format
    $counter = 1;
    while ($stmt->rowCount() > 0) {
        // Try the next format in the shuffled list
        $email = $formats[$counter % count($formats)];
        $stmt->execute();
        $counter++;
    }

    return $email;
}

// Generate the email
$email = generateEmail($firstName, $lastName, $pdo);

// PHP Validation (check if email ends with @svalberg.no)
if (preg_match("/^[a-zA-Z0-9._%+-]+@svalberg\.no$/", $email)) {
    echo json_encode(['success' => true, 'email' => $email]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
}
?>

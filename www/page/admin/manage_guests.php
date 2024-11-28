<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/config.php';

// Ensure the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch guest information from the database
$guestQuery = "SELECT user_id, firstName, lastName, username, tlf FROM users"; // Changed 'email' to 'username'
$guestResult = mysqli_query($conn, $guestQuery);
if (!$guestResult) {
    die("Error executing guest query: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Guests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px; padding: 20px; }
        .logout-button { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .back-button { position: absolute; top: 10px; left: 10px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="adminIndex.php" class="back-button">Back</a>

<!-- Logout Button -->
<a href="../../logout.php" class="logout-button">Logout</a>

<div class="container">
    <h1>Manage Guests</h1>

    <!-- Guest List Table -->
    <div class="card">
        <h3>Guest List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th> <!-- Changed 'Email' to 'Username' -->
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($guest = mysqli_fetch_assoc($guestResult)) { ?>
                    <tr>
                        <td><?php echo $guest['username']; ?></td> <!-- Displaying 'username' instead of 'email' -->
                        <td><?php echo $guest['firstName'] . ' ' . $guest['lastName']; ?></td>
                        <td><?php echo $guest['tlf']; ?></td>
                        <td>
                            <!-- Add any actions for the guests here -->
                            <a href="view_guest.php?id=<?php echo $guest['user_id']; ?>" class="btn btn-info btn-sm">View</a>
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

</body>
</html>

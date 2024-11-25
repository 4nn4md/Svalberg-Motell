<?php
    // Define constants for the database connection details
    define('DB_HOST', 'localhost'); // Database host = localhost
    define('DB_USER', 'root'); // Database username = root
    define('DB_PASS', ''); //Database password = empty 
    define('DB_NAME', 'innlevering8'); // Database name

    $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST; 

    // Attemt to create a new PDO instance for database connection
    try {
        // The PDO object is used to interact with the database
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        // Log the error message to a file for developers to see (not shown to users)
        error_log("Database connection failed: " . $e->getMessage()); // This will write the error to the server's log file

        // Show a generic error message to the user
        echo "Ops, noe gikk galt, kom tilbake senere"; // This is what the user sees
    }   
?>
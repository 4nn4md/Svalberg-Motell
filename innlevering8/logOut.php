<?php
session_start();
// Fjern alle session-variabler
session_unset();
// Ødelegg sessionen
session_destroy();


session_start();
$_SESSION['logout_message'] = "Du er nå logget ut.";

// Omdiriger til login-siden
header("Location: index.php");
exit;

?>
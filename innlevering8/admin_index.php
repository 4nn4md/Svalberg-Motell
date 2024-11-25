<?php 
include_once("inc/header1.php");
?>

<html>
  <head>
    <title>W3.CSS Template</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
      body,h1 {font-family: "Raleway", Arial, sans-serif}
      h1 {letter-spacing: 6px}
      .w3-row-padding img {margin-bottom: 12px}
    </style>
  </head>
  <body>
    <div class="w3-content" style="max-width:1500px">

    <header class="w3-panel w3-center w3-opacity" style="padding:128px 16px">
      <h1 class="w3-xlarge">Admin</h1>
      <h1><?php echo htmlspecialchars($_SESSION['user']['email']);?></h1> <!-- Fetch mail from session --> 
      <div class="w3-padding-32">
        <div class="w3-bar w3-border">
          <a href="#" class="w3-bar-item w3-button">Home</a>
          <a href="#" class="w3-bar-item w3-button w3-light-grey">Bookings</a>
          <a href="registrer_staff.php" class="w3-bar-item w3-button">Registrer ny ansatt</a>
        </div>
      </div>
    </header>
    </div>
  </body>
</html>

<!-- Lag skjema for å registrere en oppgave eller en booking og lagre oppgitte data i databasen.-->

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fornavn = $_POST['fnavn'];       // Hentet fra et skjema
    $etternavn = $_POST['lnavn'];        // Hentet fra et skjema
    $tlf = $_POST['tlf'];             // Hentet fra et skjema
   

 
    require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");

    $sql = "INSERT INTO Rom_booking
            (fornavn, etternavn, tlf) 
            VALUES 
            (:fornavn, :etternavn, :tlf)"; 
    // Alternativt: INSERT IGNORE INTO users osv. Da må f.eks. feltet email settes til UNIQUE.

    $q = $pdo->prepare($sql);

    $q->bindParam(':fornavn', $fornavn, PDO::PARAM_STR);
    $q->bindParam(':etternavn', $etternavn, PDO::PARAM_STR);
    $q->bindParam(':tlf', $tlf, PDO::PARAM_INT);
   

  
    try {
        $q->execute();
    } catch (PDOException $e) {
        echo "Feil ved tilkobling: " . $e->getMessage() . "<br>"; // Kun for læring, bør logges!
    }
//$q->debugDumpParams(); 

    if($pdo->lastInsertId() > 0) {
        echo "Data er lagt til, identifisert ved UID " . $pdo->lastInsertId() . ".";
    } else {
        echo "Data ble ikke lagt til databasen. Vennligst forsøk igjen senere.";
    }
}

?>


<html>
    <head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header.php"); ?>  
    </head>
    <body>
        <form method="POST" action="">
            <h1>Book rom!</h1>
            <label for="fnavn">Fornavn</label>
            <input type="text" id="fnavn" name="fnavn" require><br>
            <label for="lnavn">Etternavn</label>
            <input type="text" id="lnavn" name="lnavn" require><br>
            <label for="tlf">Telefon nummer</label>
            <input id="tlf" type="number" name="tlf" require><br>
            
            
            <input type="submit" value="Send">
        </form>
    </body>
</html>
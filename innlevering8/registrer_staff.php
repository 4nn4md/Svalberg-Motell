<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/innlevering8/inc/db.php");
include_once("inc/function.php");
include_once("inc/validate.php");

$errorMessage = ""; // Variabel for å lagre feilmeldinger

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider og hent data fra skjemaet
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $validation = new Validering();
    
    $validation->validereFeltene($email, $password, $role);
    $validation->validateEpostStaff($email);
    
    if (!empty($validation->getValidateError())) {
        // Samle feilmeldinger som én HTML-streng
        $errorMessage = implode("<br>", $validation->getValidateError());
    } else {
        // Krypter passordet
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Sett inn i databasen
            $sql = "INSERT INTO swx_staff (email, password_hash, role) VALUES (:email, :password, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->execute();

            $successMessage = "Ansatt registrert med rollen $role!";
        } catch (PDOException $e) {
            // Feilhåndtering
            $errorMessage = "Feil ved registrering: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body>
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="w-50">
                <!-- Feilmeldinger -->
                <?php if (!empty($errorMessage)) { ?>
                    <div class="alert alert-danger">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php } ?>

                <!-- SuccesMeldinger -->
                <?php if (!empty($successMessage)) { ?>
                    <div class="alert alert-success">
                        <?php echo $successMessage; ?>
                    </div>
                <?php } ?>

                <h1>Legg til ny ansatt</h1>
                <form method="post" action="">
                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <input type="email" name="email" id="loginName" class="form-control"  />
                        <label class="form-label" for="loginName">Email</label>
                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-4">
                        <input type="password" name="password" id="loginPassword" class="form-control"  />
                        <label class="form-label" for="loginPassword">Password</label>
                    </div>

                    <!-- Role select input -->
                    <div class="mb-4">
                        <label class="form-label" for="role">Velg rolle</label>
                        <select name="role" id="role" class="form-select" >
                            <option value="">Velg rolle</option>
                            <option value="staff">Ansatt</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mb-4">Registrer ny ansatt</button>
                </form>

                <!-- Tilbake-knapp -->
                <a href="admin_index.php" class="btn btn-secondary btn-block mt-2">Tilbake</a>
            </div>
        </div>
    </body>
</html>
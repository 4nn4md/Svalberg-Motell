<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT'] . "/innlevering8/inc/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider og hent data fra skjemaet
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        die("E-post og passord er påkrevd.");
    }

    // Krypter passordet
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Sett inn i databasen
        $sql = "INSERT INTO swx_users (email, password_hash) VALUES (:email, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        echo "Bruker registrert!";
    } catch (PDOException $e) {
        echo "Feil ved registrering: " . $e->getMessage();
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
                <?php if (isset($logoutMessage)) { ?>
                    <div style="color: green; margin-top: 10px;">
                        <?php echo htmlspecialchars($logoutMessage); ?>
                    </div>
                <?php } ?>
                <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="btn nav-link" id="tab-login" href="index.php" role="button">
                        Login
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="btn nav-link active" href="registrer_user.php" role="button">
                        Register
                    </a>
                </li>
                
                </ul>
            
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
                        <form method="post" action="">
                            <!-- Email input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="email" name="email" id="loginName" class="form-control" />
                                <label class="form-label"  for="loginName">Email</label>
                            </div>

                            <!-- Password input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="password" name="password" id="loginPassword" class="form-control" />
                                <label class="form-label"  for="loginPassword">Password</label>
                            </div>
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Registrer ny bruker</button>
                            
                        </form>

                        <?php if (!empty($errorMessage)) { ?>
                            <div style="color: red; margin-top: 10px;">
                                <?php echo htmlspecialchars($errorMessage); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

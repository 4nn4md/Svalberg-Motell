<?php 
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/innlevering8/inc/db.php");
include_once("inc/function.php");

$errorMessage = ""; // save error message

// resieve message when you are logged out and unset the session. 
if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Hent bruker fra databasen
        $user = getUserByEmail($email, $pdo);
        if (!$user) {
            $errorMessage = "Ingen bruker funnet med denne e-posten.";
        } else {
            // Check to see if the konto is locked
            $lockMessage = isAccountLocked($user);
            if ($lockMessage) {
                $errorMessage = $lockMessage;
            } elseif (password_verify($password, $user['password_hash'])) {
                $_SESSION['user'] = [
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'logged_in' => true,
                ];
                resetLoginAttempts($user['email'], $pdo, $user['table']);

                if ($user['role'] === 'admin') {
                    header("Location: admin_index.php");
                    exit;
                } elseif ($user['role'] === 'user') {
                    header("Location: user_profile.php");
                    exit;
                } elseif ($user['role'] === 'staff') {
                    $errorMessage = "Du har ikke tilgang til admin-siden.";
                } else {
                    $errorMessage = "Ugyldig rolle.";
                }
            } else {
                $errorMessage = updateFailedAttempts($user, $user['table'], $pdo);
            }
        }
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        $errorMessage = "En feil oppstod. Prøv igjen senere.";
    }
}

?>
<!-- html log in skjema er hentet her fra: https://mdbootstrap.com/docs/standard/extended/login/#section-introduction -->
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
                    <a class="btn nav-link active" id="tab-login" href="index.php" role="button">
                        Login
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="btn nav-link" href="registrer_user.php" role="button">
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
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Sign in</button>
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
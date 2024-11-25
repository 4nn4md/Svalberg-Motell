<?php 
function lockOut($email, $pdo) {
    $lockedUntil = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $pdo->prepare("UPDATE swx_users SET locked_until = :locked_until WHERE email = :email");
    $stmt->bindParam(':locked_until', $lockedUntil, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
}

function resetLoginAttempts($email, $pdo, $table) {
    $stmt = $pdo->prepare("UPDATE $table SET failed_attempts = 0, locked_until = NULL WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
}

function getUserInfo($column, $email, $pdo) {
    $stmt = $pdo->prepare("SELECT $column FROM swx_users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result[$column];
}

function bigFirstLetters($name){
    $name = strtolower($name);
    $name = ucfirst($name);
    return $name;
}

function getUserByEmail($email, $pdo) {
    $tables = ['swx_users', 'swx_staff'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT *, '$table' as table_name FROM $table WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user['table'] = $table; 

            if ($table === 'swx_users') {
                $user['role'] = 'user';
            }
            return $user;
        }
    }
    return null; 
}

function isAccountLocked($user) {
    if (!is_null($user['locked_until']) && strtotime($user['locked_until']) > time()) {
        return "Din konto er låst. Prøv igjen etter " . date('H:i', strtotime($user['locked_until'])) . ".";
    }
    return false; // Konto er ikke låst
}

function updateFailedAttempts($user, $table, $pdo) {
    $attempts = $user['failed_attempts'] + 1;
    $stmt = $pdo->prepare("UPDATE $table SET failed_attempts = :attempts WHERE email = :email");
    $stmt->bindParam(':attempts', $attempts, PDO::PARAM_INT);
    $stmt->bindParam(':email', $user['email'], PDO::PARAM_STR);
    $stmt->execute();

    if ($attempts >= 3) {
        lockOut($user['email'], $pdo, $table);
        return "Du har skrevet feil passord eller email for mange ganger. Din konto er låst i 1 time.";
    }
    return "Feil passord. Du har " . (3 - $attempts) . " forsøk igjen.";
}
?>
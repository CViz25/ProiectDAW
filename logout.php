<?php
// Începe sau reia o sesiune
session_start();

// Distruge toate datele asociate cu sesiunea curentă
$_SESSION = array(); // Curăță array-ul $_SESSION

// Distruge sesiunea
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Distruge sesiunea

// Redirecționează utilizatorul către pagina de autentificare
header("Location: login.php");
exit;
?>

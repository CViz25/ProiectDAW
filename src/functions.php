<?php

/**
 * Verifică dacă utilizatorul este autentificat.
 *
 * @return bool True dacă utilizatorul este autentificat, altfel False.
 */
function checkUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

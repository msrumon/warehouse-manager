<?php

// Starting session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    $_SESSION['flash']['success'] = 'You have been logged out!';
}

return header('Location: /login.php');

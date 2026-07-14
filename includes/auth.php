<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$publicPages = ['login.php', 'register.php'];
if (!isset($_SESSION['user_id']) && !in_array(basename($_SERVER['PHP_SELF']), $publicPages, true)) {
    header('Location: login.php');
    exit;
}

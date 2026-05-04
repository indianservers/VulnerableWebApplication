<?php
/**
 * LOGOUT PAGE
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
session_unset();

// Destroy session
session_destroy();

// Redirect to home page
header('Location: ../index.html');
exit();

<?php
session_start();

// Check if a user is logged in, then log them out
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
}

// Redirect to login page
header('Location: ./login.php');
exit;
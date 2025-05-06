<?php
session_start(); // Always start the session at the beginning

// Check if user is logged in
if (isset($_SESSION['user_email'])) {
    echo "Logged in as: " . $_SESSION['user_email'];
} else {
    echo "User not logged in.";
}
?>

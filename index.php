<?php 
// Start the session to access session data
session_start();

// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
?>
<?php include 'includes/header.php';?>
<div class="container vh-100 d-flex flex-column justify-content-center align-items-center bg-light text-center">
    <img src="assets/image.png" alt="Seat Plan Logo" class="img-fluid mb-4" style="max-width: 200px;">
    <h1 class="">Advance Automated Seat Plan Generator</h1>
    <p class="mt-3">
        <a href="https://sibsagaruniversity.ac.in/" class="text-decoration-none text-primary fw-semibold" target="_blank" rel="noopener noreferrer">
            Visit Sibsagar University
        </a>
    </p>
</div>
<?php include 'includes/footer.php';?>
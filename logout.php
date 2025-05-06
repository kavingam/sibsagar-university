<?php
session_start();

// Clear session data
$_SESSION = [];
session_destroy();

// Optional: Remove session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect after 3 seconds
$redirectUrl = "login.php";
header("refresh:3;url=$redirectUrl");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging Out...</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
  <div class="text-center">
    <div class="spinner-border text-primary mb-4" role="status"></div>
    <h4>You have been logged out.</h4>
    <p class="text-muted">Redirecting to login page...</p>
    <a href="<?= $redirectUrl ?>" class="btn btn-outline-primary mt-3">Click here if not redirected</a>
  </div>
</body>
</html>

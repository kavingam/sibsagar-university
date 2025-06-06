<?php
// Start the session to access session data
session_start();

// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
require_once 'xyz/bashmodel.php';

// Fetch user data
$userInfo = new UserInfo();
$user = $userInfo->getUserByEmail($_SESSION['user_email']);

include 'includes/header.php';
?>

<div class="container" style="margin-top: 100px">
  <div class="card shadow-lg p-4 rounded-4">
    <h3 class="text-center mb-4">Welcome to Your Profile</h3>
    <div class="row mb-3">
      <div class="col-sm-4 fw-bold">Email:</div>
      <div class="col-sm-8"><?= htmlspecialchars($user['email']) ?></div>
    </div>
    <div class="row mb-3">
      <div class="col-sm-4 fw-bold">Account Created:</div>
      <div class="col-sm-8">
        <?= date('F j, Y, g:i a', strtotime($user['created_at'])) ?>
      </div>
    </div>
    <div class="text-center">
      <a href="logout.php" class="btn btn-red">Logout</a>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

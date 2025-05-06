<?php
session_start();
require_once 'xyz/bashmodel.php';

// Redirect if not logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $userInfo = new UserInfo();
    $user = $userInfo->getUserByEmail($_SESSION['user_email']);

    // Check if current password is correct
    if (password_verify($currentPassword, $user['password_hash'])) {
        // Check if new passwords match
        if ($newPassword === $confirmPassword) {
            // Hash the new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_ARGON2ID);

            // Update the password
            if ($userInfo->updateUserPassword($user['email'], $newPasswordHash)) {
                $message = "Password updated successfully!";
                $messageClass = "alert-success";
            } else {
                $message = "Error updating password. Please try again.";
                $messageClass = "alert-danger";
            }
        } else {
            $message = "New password and confirm password do not match.";
            $messageClass = "alert-danger";
        }
    } else {
        $message = "Current password is incorrect.";
        $messageClass = "alert-danger";
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="card shadow-lg p-4 rounded-4">
        <h3 class="text-center mb-4">Change Password</h3>

        <?php if (isset($message)): ?>
            <div class="alert <?= $messageClass ?>"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action="change-password.php">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password" required>
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php
// Start the session to access session data
session_start();
// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');  // Redirect to login page
    exit;
}
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
// echo $userEmail;
?>
<?php
include 'includes/header.php';
include 'xyz/bashmodel.php';

$stdObj = new Student();
$deptObj = new Department();
$roomObj = new Room();
$userObj = new UserInfo();
?>

<?php include 'includes/footer.php'; ?>
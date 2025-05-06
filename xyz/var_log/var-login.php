<?php
// Start session at the beginning of the script
session_start();

// Include the necessary files for DB connection and methods
require_once '../bashmodel.php';  // Adjust the path if needed

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get form data (email and password)
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Create an instance of UserInfo (assuming UserInfo extends BaseModel)
        $userInfo = new UserInfo();

        // Call the login method to authenticate the user
        if ($userInfo->login($email, $password)) {
            // Store user information in session after successful login
            $_SESSION['user_email'] = $email; // You can store other user details too

            // Send success message
            echo "Login successful!";
        } else {
            // Send failure message if login is not successful
            echo "Invalid email or password.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

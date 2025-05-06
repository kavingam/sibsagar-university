<?php 
// Include the BaseModel (or other necessary files) to ensure DB connection and methods are available
require_once '../bashmodel.php';  // Adjust the path if needed

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get form data
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Create an instance of UserInfo (assuming UserInfo extends BaseModel)
        $userInfo = new UserInfo();

        // Call createUser method to insert a new user
        if ($userInfo->createUser($email, $password)) {
            echo "User created successfully!";
        } else {
            echo "Error creating user.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
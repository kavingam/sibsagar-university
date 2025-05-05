<?php
// Include necessary files
require_once '../bashmodel.php'; // Adjust path as needed


// Set the content type to JSON
header('Content-Type: application/json');

// Instantiate the Department class
$department = new Department();

// Get all departments
$departments = $department->getAllDepartments();

// Output the result as a JSON response
echo json_encode($departments);

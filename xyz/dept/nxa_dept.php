<?php
include ('../bashmodel.php');

if (isset($_POST['department_name'])) {
	$department_name = trim($_POST['department_name']);

    if ($department_name === "") {
        echo json_encode(["status" => "error", "message" => "Department name cannot be empty."]);
        exit();
    }

    try {
    	$department = new Department();
    	$departments = $department->createNewDepartmentJSON($department_name);	
        echo $departments;
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    	
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

?>

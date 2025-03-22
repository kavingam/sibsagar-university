<?php
include ('../bashmodel.php');
	// ✅ API Usage (Handling POST request)
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	    $department_id = $_POST['department_id'] ?? '';
	    $department_name = $_POST['department_name'] ?? '';

	    if (empty($department_id) || empty($department_name)) {
	        echo json_encode(["status" => "error", "message" => "Both department ID and name are required."]);
	        exit();
	    }

	    $department = new Department();
	    $result = $department->updateDepartmentJSON($department_id, $department_name);

	    echo json_encode($result);
	}
?>


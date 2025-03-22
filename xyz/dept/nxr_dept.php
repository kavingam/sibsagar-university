<?php 
include ('../bashmodel.php');

header("Content-Type: application/json"); // ✅ Ensure response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_id = $_POST['department_id'] ?? '';

    if (empty($department_id)) {
        echo json_encode(["status" => "error", "message" => "Department ID is required."]);
        exit();
    }

    try {
        $department = new Department();
        $result = $department->deleteDepartment($department_id);

        // ✅ Ensure deleteDepartment returns a structured response
        if ($result === true) {
            echo json_encode(["status" => "success", "message" => "Department deleted successfully."]);
        } elseif (is_array($result)) {
            echo json_encode($result); // If deleteDepartment returns an error array
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete department."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Exception: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>

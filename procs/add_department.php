<?php
include('../db/pdo_connect.php');

if (isset($_POST['department_name'])) {
    $department_name = trim($_POST['department_name']);

    if ($department_name === "") {
        echo json_encode(["status" => "error", "message" => "Department name cannot be empty."]);
        exit();
    }

    try {
        // Get the next available ID (highest + 1)
        $stmt = $pdo->query("SELECT MAX(department_id) AS max_id FROM departments");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $next_id = ($row['max_id'] !== null) ? $row['max_id'] + 1 : 1;

        // Check for duplicate department name
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM departments WHERE department_name = ?");
        $stmt->execute([$department_name]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(["status" => "exists_name", "message" => "Department name already exists."]);
            exit();
        }

        // Insert new department
        $stmt = $pdo->prepare("INSERT INTO departments (department_id, department_name) VALUES (?, ?)");
        $stmt->execute([$next_id, $department_name]);

        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>



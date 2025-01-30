<?php
include('../db/pdo_connect.php');  
$department_id = $_POST['department_id'];
$department_name = $_POST['department_name'];

try {
    $sql_check_id = "SELECT * FROM departments WHERE department_id = :department_id";
    $stmt_check_id = $pdo->prepare($sql_check_id);
    $stmt_check_id->bindParam(':department_id', $department_id);
    $stmt_check_id->execute();
    $existingDepartment = $stmt_check_id->fetch(PDO::FETCH_ASSOC);

    $sql_check_name = "SELECT * FROM departments WHERE department_name = :department_name";
    $stmt_check_name = $pdo->prepare($sql_check_name);
    $stmt_check_name->bindParam(':department_name', $department_name);
    $stmt_check_name->execute();
    $existingDepartmentName = $stmt_check_name->fetch(PDO::FETCH_ASSOC);

    if ($existingDepartmentName) {
        echo json_encode(['status' => 'exists_name', 'message' => 'Department name already exists.']);
    } else {
        if ($existingDepartment) {
            $sql_update = "UPDATE departments SET department_name = :department_name WHERE department_id = :department_id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(':department_name', $department_name);
            $stmt_update->bindParam(':department_id', $department_id);
            $stmt_update->execute();

            echo json_encode(['status' => 'exists', 'message' => 'Department updated successfully']);
        } else {
            $sql_insert = "INSERT INTO departments (department_id, department_name) VALUES (:department_id, :department_name)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->bindParam(':department_id', $department_id);
            $stmt_insert->bindParam(':department_name', $department_name);
            $stmt_insert->execute();

            echo json_encode(['status' => 'success', 'message' => 'Department added successfully']);
        }
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

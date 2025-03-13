<?php
include('../db/pdo_connect.php');

if (isset($_POST['department_id'])) {
    $department_id = $_POST['department_id'];

    try {
        // Delete the department
        $stmt = $pdo->prepare("DELETE FROM departments WHERE department_id = ?");
        $stmt->execute([$department_id]);

        // Fetch all departments in order
        $stmt = $pdo->query("SELECT department_id FROM departments ORDER BY department_id ASC");
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Reassign IDs sequentially
        $new_id = 1;
        foreach ($departments as $dept) {
            $pdo->prepare("UPDATE departments SET department_id = ? WHERE department_id = ?")->execute([$new_id, $dept['department_id']]);
            $new_id++;
        }

        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>

<?php
// include('../db/pdo_connect.php');

// if (isset($_POST['department_id'])) {
//     $department_id = $_POST['department_id'];

//     try {
//         // Delete the department
//         $stmt = $pdo->prepare("DELETE FROM departments WHERE department_id = ?");
//         $stmt->execute([$department_id]);

//         // Fetch all departments in order
//         $stmt = $pdo->query("SELECT department_id FROM departments ORDER BY department_id ASC");
//         $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         // Reassign IDs sequentially
//         $new_id = 1;
//         foreach ($departments as $dept) {
//             $pdo->prepare("UPDATE departments SET department_id = ? WHERE department_id = ?")->execute([$new_id, $dept['department_id']]);
//             $new_id++;
//         }

//         echo json_encode(["status" => "success"]);
//     } catch (PDOException $e) {
//         echo json_encode(["status" => "error", "message" => $e->getMessage()]);
//     }
// } else {
//     echo json_encode(["status" => "error", "message" => "Invalid request"]);
// }
?>

<?php 
include('../db/pdo_connect.php');

if (isset($_POST['department_id'])) {
    $department_id = $_POST['department_id'];

    try {
        $pdo->beginTransaction(); // Start transaction

        // Delete the department
        $stmt = $pdo->prepare("DELETE FROM departments WHERE department_id = ?");
        $stmt->execute([$department_id]);

        // Create a temporary table to store reassigned IDs
        $pdo->exec("CREATE TEMPORARY TABLE temp_departments AS SELECT department_name FROM departments ORDER BY CAST(department_id AS UNSIGNED) ASC");

        // Clear original table
        $pdo->exec("DELETE FROM departments");

        // Reinsert data with new sequential IDs
        $new_id = 1;
        $stmt = $pdo->prepare("INSERT INTO departments (department_id, department_name) VALUES (?, ?)");
        $tempStmt = $pdo->query("SELECT * FROM temp_departments");

        while ($row = $tempStmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt->execute([$new_id, $row['department_name']]);
            $new_id++;
        }

        // Drop temporary table
        $pdo->exec("DROP TEMPORARY TABLE temp_departments");

        $pdo->commit(); // Commit transaction

        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        $pdo->rollBack(); // Rollback transaction on error
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

?>

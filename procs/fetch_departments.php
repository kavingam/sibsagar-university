<?php
/*
include('../db/pdo_connect.php');

try {
    $stmt = $pdo->query("SELECT * FROM departments ORDER BY department_id ASC");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($departments) {
        foreach ($departments as $row) {
            echo "<tr>
                    <td>{$row['department_id']}</td>
                    <td>{$row['department_name']}</td>
                    <td class='text-center'>
                        <button class='btn btn-sm edit-btn' data-id='{$row['department_id']}' data-name='{$row['department_name']}'><i class='bi bi-pencil-square text-primary'></i></button>
                        <button class='btn btn-sm delete-btn' data-id='{$row['department_id']}'><i class='bi bi-trash-fill text-danger'></i></button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No departments found</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='3' class='text-danger'>Error: " . $e->getMessage() . "</td></tr>";
}
*/

include('../db/pdo_connect.php');

try {
    // Order by department_id as a number
    $stmt = $pdo->query("SELECT * FROM departments ORDER BY CAST(department_id AS UNSIGNED) ASC");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($departments) {
        foreach ($departments as $row) {
            echo "<tr>
                    <td>{$row['department_id']}</td>
                    <td>{$row['department_name']}</td>
                    <td class='text-center'>
                        <button class='btn btn-sm edit-btn' data-id='{$row['department_id']}' data-name='{$row['department_name']}'><i class='bi bi-pencil-square text-primary'></i></button>
                        <button class='btn btn-sm delete-btn' data-id='{$row['department_id']}'><i class='bi bi-trash-fill text-danger'></i></button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No departments found</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='3' class='text-danger'>Error: " . $e->getMessage() . "</td></tr>";
}

?>

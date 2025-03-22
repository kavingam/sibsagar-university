<?php
include ('../bashmodel.php');

try {

	$department = new Department();
	$departments = $department->getAllDepartments();

	if ($departments) {
        foreach ($departments as $row) {
            echo "<tr>
                    <td>{$row['department_id']}</td>
                    <td>{$row['department_name']}</td>
                    <td class='text-center'>
                        <button class='btn btn-sm edit-btn' data-id='{$row['department_id']}' data-name='{$row['department_name']}'><i class='fad fa-edit text-success'></i></button>
                        <button class='btn btn-sm delete-btn' data-id='{$row['department_id']}'><i class='fad fa-trash-alt text-danger'></i></button>
                        
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No departments found</td></tr>";
    }
    
}  catch (PDOException $e) {
    echo "<tr><td colspan='3' class='text-danger'>Error: " . $e->getMessage() . "</td></tr>";
}
?>
<?php

// include('../bashmodel.php');

// header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: *');

// try {
//     $department = new Department();
//     $departments = $department->getAllDepartments();

//     $data = [];

//     if ($departments) {
//         foreach ($departments as $row) {
//             $data[] = [
//                 "department_id" => $row['department_id'],
//                 "department_name" => $row['department_name'],
//                 "actions" => "<button class='btn btn-sm edit-btn' data-id='{$row['department_id']}' data-name='{$row['department_name']}'><i class='bi bi-pencil-square text-primary'></i></button>
//                               <button class='btn btn-sm delete-btn' data-id='{$row['department_id']}'><i class='bi bi-trash-fill text-danger'></i></button>"
//             ];
//         }
//     }

//     echo json_encode(["data" => $data], JSON_UNESCAPED_UNICODE);
// } catch (PDOException $e) {
//     echo json_encode(["error" => "Error: " . $e->getMessage()]);
// }
?>


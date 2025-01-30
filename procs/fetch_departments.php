<?php
include('../db/pdo_connect.php');

try {
    $sql = "SELECT * FROM departments";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $departments = array_reverse($departments);
    if ($departments) {
        $serial_no = 1;
        foreach ($departments as $row) {
            echo "<tr>
                    <td>" . $serial_no++ . "</td>
                    <td>" . $row['department_id'] . "</td>
                    <td>" . $row['department_name'] . "</td>
                  </tr>";
        }
    } else {
        echo "No departments found";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<?php
include('../db/pdo_connect.php');

if (isset($_POST['department_id']) && isset($_POST['department_name'])) {
    $department_id = $_POST['department_id'];
    $department_name = trim($_POST['department_name']);

    if ($department_name === "") {
        echo json_encode(["status" => "error", "message" => "Department name cannot be empty."]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE departments SET department_name = ? WHERE department_id = ?");
        $stmt->execute([$department_name, $department_id]);

        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>

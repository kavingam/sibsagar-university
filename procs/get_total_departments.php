<?php
include('../db/pdo_connect.php');

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM departments");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(["total" => $result['total']]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>

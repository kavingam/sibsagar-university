<?php 
    require_once 'sleekdb.php';
    $departmentsStore = new DepartmentStore();
    $deleted1 = $departmentsStore->deleteById(13);



    if ($deleted1) {
        echo "✅ First record deleted successfully!<br>";
    } else {
        echo "⚠️ First record not found or not deleted.<br>";
    }
?>
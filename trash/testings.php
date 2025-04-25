<?php 
    require_once 'sleekdb.php';
    try {
        // ✅ Create an instance of DepartmentStore
        $departmentsStore = new DepartmentStore();
    
        // ✅ Test fetching all departments
        $allDepartments = $departmentsStore->findAll();
    
        if (!empty($allDepartments)) {
            echo "✅ Connection Successful! Departments found: " . count($allDepartments) . "<br>";
            
            echo "<pre>";
            print_r($allDepartments);
            echo "</pre>";
        } else {
            echo "⚠️ Connection successful, but no department data found.";
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
    }
?>
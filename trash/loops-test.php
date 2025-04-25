<?php 
require_once 'sleekdb.php';

try {
    $departmentsStore = new DepartmentStore();
    echo "✅ Connection Successful!\n";

    $departmentsStore = new AdvancedDepartmentStore(new DepartmentStore($departmentsStore));
    $departmentsStore->insertMany($fetchingSimilarity); 

    $getTotalDepartment = $departmentsStore->findAll();
    $index = count($getTotalDepartment);
    do {
    
        

        if (0 == $index) {
                echo "department not available\n";
            break;
        } else if (0 != $index && 1 <= $index) {
                echo "Total Department -".count($getTotalDepartment)."\n";
            break;
        }
        
        
        $index--; 
    } while (true);
    
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 
?>

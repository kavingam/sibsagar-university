<?php

require_once "config.php"; // Load config settings
require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

use SleekDB\Store;

class CombinationJSON {
    protected $store;

    public function __construct() {
        $config = require "config.php";
        $this->store = new Store("combination_json", $config["dataDir"], $config["storeConfig"]);
    }    

    // Sort by a single field
    public function sortByField($field, $order = 'asc') {
        $validOrders = ['asc', 'desc'];
        
        // Ensure the order is valid
        if (!in_array($order, $validOrders)) {
            $order = 'asc'; // Default to 'asc' if invalid order is passed
        }

        // Fetch all records and use sortBy on the query object, not the array
        $query = $this->store->query(); // Get the query object
        $results = $query->sortBy([$field => $order])->fetch(); // Apply sorting and fetch results
        
        return $results;
    }

    public function departmentExists($dept) {
        return $this->store->findBy([
            ["department", "=", $dept["department"]],
            ["semester", "=", $dept["semester"]],
            ["course", "=", $dept["course"]]
        ]);
    }

    public function insertDepartment($dept) {
        if (empty($this->departmentExists($dept))) {
            $this->store->insert($dept); // Uncomment to insert into DB
            // echo "✅ Successfully seat allocation  {$dept['department']} inserted successfully!<br>";
        } else {
            // echo "⚠️ Failure seat allocation {$dept['department']} already exists. Skipping...<br>";
        }
    }

    public function insertDept($dept) {
        // Directly insert the department without checking for existence
        $this->store->insert($dept);
        // echo "✅ Department '{$dept['department']}' (Semester: {$dept['semester']}, Course: {$dept['course']}) inserted successfully!<br>";
    }
    

    public function findTotal() {
        $totalCount = $this->store->count();
        // echo "Total number of departments: {$totalCount}<br>";
        return $totalCount;
    }
    public function findAll(){
        $allRecords = $this->store->findAll();
        return $allRecords;
    }
    // New Function: Delete All Data
    public function deleteAllData() {
        $this->store->truncate(); // Deletes all records in the store
        echo "⚠️ All seat allocations have been deleted!<br>";
    }
    // Function to delete all records one by one
    public function deleteAllDataX() {
        // Debugging: Check if the store is properly initialized and contains data
        echo "Attempting to delete all data from the store...<br>";
        $totalCount = $this->store->count();
        
        if ($totalCount > 0) {
            // Retrieve all records
            $allRecords = $this->store->findAll();
            
            // Loop through and delete each record
            foreach ($allRecords as $record) {
                $this->store->delete($record); // Delete each record
            }
            
            // echo "⚠️ All seat allocations have been deleted!<br>";
        } else {
            // echo "✅ No data to delete. Store is empty.<br>";
        }
    }
    

}

class NewCombinationJSON extends CombinationJSON  {
    public function bulkInsert($dept) {
            $this->insertDept($dept);
    }
}

?>


<?php

// require_once "config.php";
// require_once __DIR__ . "/SleekDB-master/src/Store.php";
// require_once __DIR__ . "/SleekDB-master/src/Query.php";

// use SleekDB\Store;

// class CombinationJSON {
//     protected $store;

//     public function __construct() {
//         $config = require "config.php";
//         $this->store = new Store("combination_json", $config["dataDir"], $config["storeConfig"]);
//     }    

//     public function departmentExists($dept) {
//         if (!isset($dept["department"], $dept["semester"], $dept["course"])) {
//             echo "⚠️ Invalid department data. Keys missing.<br>";
//             return false;
//         }
    
//         $result = $this->store->findBy([
//             ["department", "=", $dept["department"]],
//             ["semester", "=", $dept["semester"]],
//             ["course", "=", $dept["course"]]
//         ]);
//         return !empty($result);
//     }
    
//     public function insertDepartment($dept) {
//         if (!isset($dept["department"], $dept["semester"], $dept["course"])) {
//             echo "⚠️ Skipping insert. Invalid data format.<br>";
//             return;
//         }
    
//         if (!$this->departmentExists($dept)) {
//             $this->store->insert($dept);
//             echo "✅ Inserted: {$dept['department']}-{$dept['semester']}-{$dept['course']}<br>";
//         } else {
//             echo "⚠️ Skipped duplicate: {$dept['department']}-{$dept['semester']}-{$dept['course']}<br>";
//         }
//     }
    
 
//     public function insertDept($dept) {
//         // Unsafe insert — now unused
//         $this->store->insert($dept);
//     }

//     public function bulkInsert($deptList) {
//         foreach ($deptList as $dept) {
//             $this->insertDepartment($dept); // Safe insert
//         }
//     }

//     public function findTotal() {
//         return $this->store->count();
//     }

//     public function findAll(){
//         return $this->store->findAll();
//     }

//     public function deleteAllData() {
//         $this->store->truncate();
//         echo "⚠️ All combinations deleted!<br>";
//     }

//     public function deleteAllDataX() {
//         $allRecords = $this->store->findAll();
//         foreach ($allRecords as $record) {
//             $this->store->delete($record);
//         }
//     }
// }

// class NewCombinationJSON extends CombinationJSON {
//     // Uses safe bulk insert
//     public function bulkInsert($dept) {
//         $this->insertDepartment($dept);
//     }

// }

?>

<?php

require_once "config.php"; // Load config settings
require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

use SleekDB\Store;

class SeatAllocationList {
    protected $store;

    public function __construct() {
        $config = require "config.php";
        $this->store = new Store("seatAllocationList", $config["dataDir"], $config["storeConfig"]);
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
    public function findTotal() {
        $totalCount = $this->store->count();
        // echo "Total number of departments: {$totalCount}<br>";
        return $totalCount;
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

class CreateSeatAllocation extends SeatAllocationList  {
    public function bulkInsert($deptList) {
        foreach ($deptList as $dept) {
            $this->insertDepartment($dept);
        }
    }
}

?>

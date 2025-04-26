<?php

require_once "config.php"; // Load config settings
require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

use SleekDB\Store;

class SeatallocationJSON {
    protected $store;

    public function __construct() {
        $config = require "config.php";
        $this->store = new Store("seatallocation_json", $config["dataDir"], $config["storeConfig"]);
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
    // Rename the bulkInsert function to insertDepartmentsIfValid
    public function insertDepartmentsIfValid($deptList) {
        foreach ($deptList as $dept) {
            // Check if totalStudent is 0, if so, skip the insert for this department
            if ($dept['totalStudent'] === 0) {
                // echo "⚠️ Skipping department {$dept['department']} because totalStudent is 0.<br>";
                continue; // Skip this department and move to the next one
            }
            // Insert the department if totalStudent is not 0
            $this->insertDept($dept);
        }
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
    // Function to delete based on array data (e.g., $remainderList[1])
    public function deleteByArray($remainderData) {
        // Check if the necessary fields exist in the provided array
        if (isset($remainderData['department'], $remainderData['semester'], $remainderData['course'])) {
            // Get the department, semester, and course from the array
            $department = $remainderData['department'];
            $semester = $remainderData['semester'];
            $course = $remainderData['course'];

            // Use the deleteBy method to delete records that match the criteria
            $deletedCount = $this->store->deleteBy([
                ["department", "=", $department],
                ["semester", "=", $semester],
                ["course", "=", $course]
            ]);

            // Check if any records were deleted
            if ($deletedCount > 0) {
                // echo "✅ Successfully deleted {$deletedCount} records for department {$department}, semester {$semester}, course {$course}.<br>";
            } else {
                // echo "⚠️ No records found for department {$department}, semester {$semester}, course {$course}.<br>";
            }
        } else {
            // echo "⚠️ Invalid data. Please provide a valid array with department, semester, and course.<br>";
        }
    }

}

class NewSeatallocationJSON extends SeatallocationJSON {
    public function bulkInsert($deptList) {
        foreach ($deptList as $dept) {
            // $this->insertDepartment($dept);
            $this->insertDept($dept);
        }
    }
}

?>

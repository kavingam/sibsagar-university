<?php

require_once "config.php"; // Load config settings
require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

use SleekDB\Store;

class SeatAllocationList {
    protected $store;

    public function __construct() {
        $config = require "config.php";
        // Initialize the store with debug output
        echo "Initializing SleekDB Store...<br>";
        $this->store = new Store("seatAllocationList", $config["dataDir"], $config["storeConfig"]);
    }

    // Function to find total number of records
    public function findTotal() {
        $totalCount = $this->store->count();
        echo "Total number of departments: {$totalCount}<br>";
        return $totalCount;
    }

    // Function to delete all records one by one
    public function deleteAllData() {
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
            
            echo "⚠️ All seat allocations have been deleted!<br>";
        } else {
            echo "✅ No data to delete. Store is empty.<br>";
        }
    }
}

// Example of using the functions
$seatAlloc = new SeatAllocationList();

// Find the total count of departments
$seatAlloc->findTotal();

// Attempt to delete all records
$seatAlloc->deleteAllData(); // This will delete all records if any exist
?>

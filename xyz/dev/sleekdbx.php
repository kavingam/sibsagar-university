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
            echo "✅ Successfully seat allocation  {$dept['department']} inserted successfully!<br>";
        } else {
            echo "⚠️ Failure seat allocation {$dept['department']} already exists. Skipping...<br>";
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

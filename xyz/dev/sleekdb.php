<?php

require_once "config.php"; // Load config settings
require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

use SleekDB\Store;

class DepartmentStore {
    protected $store;

    public function __construct() {
        $config = require "config.php";
        $this->store = new Store("departments", $config["dataDir"], $config["storeConfig"]);
    }

    // Fetch all departments
    public function findAll() {
        return $this->store->findAll();
    }

    // Find department by conditions
    public function findBy($conditions) {
        return $this->store->findBy($conditions);
    }

    // Insert a department
    public function insert($data) {
        return $this->store->insert($data);
    }
    // ✅ Insert multiple departments at once
    public function insertMany($departments) {
        return $this->store->insertMany($departments);
    }

    public function deleteById($id) {
        return $this->store->deleteBy([["_id", "=", $id]]);
    }

    // Delete all records
    public function deleteAll() {
        return $this->store->deleteBy(["_id", "!=", 0]);
    }

    // ✅ Encapsulation: Method to check if a department exists
    public function departmentExists($dept) {
        return $this->store->findBy([
            ["department", "=", $dept["department"]],
            ["semester", "=", $dept["semester"]],
            ["course", "=", $dept["course"]]
        ]);
    }

    // ✅ Encapsulation: Insert department if not exists
    public function insertDepartment($dept) {
        if (empty($this->departmentExists($dept))) {
            $this->store->insert($dept); // Uncomment to insert into DB
            // echo "✅ Department {$dept['department']} inserted successfully!<br>";
        } else {
            // echo "⚠️ Department {$dept['department']} already exists. Skipping...<br>";
        }
    }

    public function deleteCache() {
        $cacheDir = $this->store->getStorePath() . "/cache"; // Get the cache directory
    
        if (is_dir($cacheDir)) {
            $files = glob("$cacheDir/*"); // Get all cache files
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // Delete each cache file
                }
            }
            // echo "✅ Cache cleared successfully!<br>";
        } else {
            // echo "⚠️ Cache directory not found!<br>";
        }
    }
    

}

// ✅ Polymorphism: Class extending DepartmentStore (Inheritance Example)
class AdvancedDepartmentStore extends DepartmentStore {
    public function bulkInsert($departments) {
        foreach ($departments as $dept) {
            $this->insertDepartment($dept);
        }
    }
}

?>

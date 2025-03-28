<?php

require_once "config.php";
require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

use SleekDB\Store;

// Load the configuration
$config = require "config.php";
$dataDir = $config["dataDir"];

try {
    // Create a test store
    $testStore = new Store("test_connection", $dataDir, $config["storeConfig"]);

    // Insert a test document
    $testData = ["status" => "Connected Successfully", "timestamp" => date("Y-m-d H:i:s")];
    $testStore->insert($testData);

    // Fetch the test document
    $testResult = $testStore->findAll();

    if (!empty($testResult)) {
        echo "✅ Database Connection Successful!<br>";
        echo "<pre>";
        print_r($testResult);
        echo "</pre>";
    } else {
        echo "⚠️ Connection test failed. No data was inserted.";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

?>

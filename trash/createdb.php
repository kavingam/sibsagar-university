<?php
use SleekDB\Store;
use SleekDB\Query; // Needed for search configuration

require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

// Define the directory where JSON data will be stored
$dataDir = __DIR__ . "/database";
$storeName = "process"; // Change this to your store name

// Path to store folder
$storePath = $dataDir . "/" . $storeName;

// Check if the store already exists
if (is_dir($storePath)) {
    // echo "Store '$storeName' already exists!";
} else {
    // ✅ Create store if it does not exist
    $configuration = [
        "auto_cache" => true,
        "cache_lifetime" => null,
        "timeout" => false, // ✅ Fix deprecated setting
        "primary_key" => "_id",
        "search" => [
            "min_length" => 2,
            "mode" => "or",
            "score_key" => "scoreKey",
            "algorithm" => Query::SEARCH_ALGORITHM["hits"]
        ],
        "folder_permissions" => 0777
    ];

    $processStore = new Store($storeName, $dataDir, $configuration);
    // echo "Store '$storeName' created successfully!";
}

?>
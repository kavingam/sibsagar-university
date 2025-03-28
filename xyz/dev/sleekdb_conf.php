
<?php

use SleekDB\Store;
use SleekDB\Query; // Needed for search configuration

require_once __DIR__ . "/SleekDB-master/src/Store.php";
require_once __DIR__ . "/SleekDB-master/src/Query.php";

$dataDir = __DIR__ . "/database"; 

// ✅ Fixed: Corrected "search" algorithm issue
$configuration = [
    "auto_cache" => true,
    "cache_lifetime" => null,
    "timeout" => false,
    "primary_key" => "_id",
    "search" => [
        "min_length" => 2,
        "mode" => "or",
        "score_key" => "scoreKey",
        // "algorithm" => Query::SEARCH_ALGORITHM_HITS // ✅ Fixed
    ],
    "folder_permissions" => 0777
];

//"algorithm" => ["hits"] // ✅ Fixed: Ensure it's an array if needed
?>


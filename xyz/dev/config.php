<?php

return [
    "dataDir" => __DIR__ . "/database", // Database storage directory
    "storeConfig" => [
        "auto_cache" => false,
        "cache_lifetime" => null,
        "timeout" => false,
        "primary_key" => "_id",
        "search" => [
            "min_length" => 2,
            "mode" => "or",
            "score_key" => "scoreKey"
        ],
        "folder_permissions" => 0777
    ]
];

?>

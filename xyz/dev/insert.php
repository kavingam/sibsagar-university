<?php
use SleekDB\Store;

include 'sleekdb_conf.php'; // ✅ Include configuration

// ✅ Create Store
$departmentsStore = new Store("process", $dataDir, $configuration);

// ✅ Define department data
$departments = [
    [
        "department" => 2,
        "semester" => 1,
        "course" => 3,
        "totalStudent" => 20,
        "students" => [
            [
                "roll_no" => "ASS-UG-SEM01",
                "name" => "AA-01",
                "department" => 1,
                "semester" => 1,
                "course" => 1
            ],
            [
                "roll_no" => "ASS-UG-SEM02",
                "name" => "AA-02",
                "department" => 1,
                "semester" => 1,
                "course" => 1
            ]
        ]
    ]
];

// ✅ Check if department already exists (ignoring `students` subarray)
foreach ($departments as $dept) {
    $existingData = $departmentsStore->findBy([
        ["department", "=", $dept["department"]],
        ["semester", "=", $dept["semester"]],
        ["course", "=", $dept["course"]]
    ]);

    if (empty($existingData)) {
        // ✅ Insert only if no matching department exists
        $departmentsStore->insertMany($dept);
        echo "✅ Department {$dept['department']} inserted successfully!<br>";
    } else {
        echo "⚠️ Department {$dept['department']} already exists. Skipping...<br>";
    }
}
?>
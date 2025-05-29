<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === Paths ===
require_once __DIR__ . '/../bashmodel.php';
require_once __DIR__ . '/../seat_allocation/seat_allocation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo "<p class='text-danger'>Invalid data received!</p>";
        exit;
    }

    $examTime24 = htmlspecialchars($data['startTime']);
    $examTime = date('g:i A', strtotime($examTime24));
    $benchSeat = htmlspecialchars($data['benchSeat']);
    $examName = htmlspecialchars($data['selectedExam']);
    $examDate = htmlspecialchars($data['startDate']);
    $saveData = htmlspecialchars($data['save']);
    $tableData = $data['tableData'];

    usort($tableData, fn($a, $b) => $b['totalStudent'] <=> $a['totalStudent']);

    $students = new Student();
    $fetchStudents = [];

    foreach ($tableData as $data) {
        $similarStudents = $students->findSimilarStudents(
            $data['department'],
            $data['course'],
            $data['semester']
        );

        $fetchStudents[] = [
            'department' => $data['department'],
            'semester' => $data['semester'],
            'course' => $data['course'],
            'subject' => $data['subject'],
            'totalStudent' => $data['totalStudent'],
            'students' => $similarStudents
        ];
    }

    $dept_students = [];

    foreach ($fetchStudents as $entry) {
        if (!isset($entry['students']) || !is_array($entry['students']))
            continue;

        foreach ($entry['students'] as $student) {
            $dept_id = $student['department_id'] ?? null;
            $course = $student['course'] ?? null;
            $semester = $student['semester'] ?? null;

            if ($dept_id !== null && $course !== null && $semester !== null) {
                $key = "{$dept_id}_{$course}_{$semester}";
                $dept_students[$key][] = $student;
            }
        }
    }
    $group_keys = array_keys($dept_students);

    // $flat_students = [];
    // $all_empty = false;

    // while (!$all_empty) {
    //     $all_empty = true;

    //     foreach ($group_keys as $group_key) {
    //         if (!empty($dept_students[$group_key])) {
    //             $flat_students[] = array_shift($dept_students[$group_key]);
    //             $all_empty = false;
    //         }
    //     }
    // }



    // $remaining_departments = $group_keys;
    // $step = 1;
    // $flat_students = [];

    // while (!empty($remaining_departments)) {
    //     $try_count = 4;

    //     while ($try_count >= 1) {
    //         echo "Step $step: Trying with $try_count departments...<br/>";

    //         // Take first $try_count departments
    //         $current_batch = array_slice($remaining_departments, 0, $try_count);

    //         if (count($current_batch) > 0) {
    //             echo "✅ Step $step: Selected departments: " . implode(', ', $current_batch) . "<br/>";

    //             $batch_students = [];
    //             $all_empty = false;

    //             // Round-robin assign students or null for empty seat per department
    //             while (!$all_empty) {
    //                 $all_empty = true;
    //                 foreach ($current_batch as $dept) {
    //                     if (!empty($dept_students[$dept])) {
    //                         $batch_students[] = array_shift($dept_students[$dept]);
    //                         $all_empty = false;
    //                     } else {
    //                         $batch_students[] = null; // empty seat for this department
    //                     }
    //                 }
    //             }

    //             // Count empty seats in current batch
    //             $empty_seats = count(array_filter($batch_students, fn($s) => $s === null));

    //             // Remove used departments from remaining
    //             $remaining_departments = array_slice($remaining_departments, $try_count);

    //             // Fill empty seats with students from next remaining departments
    //             if ($empty_seats > 0) {
    //                 $filled_seats = 0;
    //                 foreach ($remaining_departments as $idx => $dept) {
    //                     while (!empty($dept_students[$dept]) && $filled_seats < $empty_seats) {
    //                         // Replace first null seat with student from this dept
    //                         $null_index = array_search(null, $batch_students);
    //                         if ($null_index === false) break;

    //                         $batch_students[$null_index] = array_shift($dept_students[$dept]);
    //                         $filled_seats++;
    //                     }
    //                     // Remove dept if empty now
    //                     if (empty($dept_students[$dept])) {
    //                         unset($remaining_departments[$idx]);
    //                     }
    //                     if ($filled_seats >= $empty_seats) break;
    //                 }
    //                 $remaining_departments = array_values($remaining_departments);
    //             }

    //             // Add batch students to final list
    //             $flat_students = array_merge($flat_students, $batch_students);

    //             break;
    //         }

    //         $try_count--;
    //     }

    //     $step++;
    // }

    // version 3.0
    // $remaining_departments = $group_keys;
    // $step = 1;
    // $flat_students = [];

    // while (!empty($remaining_departments)) {
    //     $try_count = 4;

    //     while ($try_count >= 1) {
    //         // echo "Step $step: Trying with $try_count departments...<br/>";

    //         // Take first $try_count departments safely
    //         $current_batch = array_slice($remaining_departments, 0, $try_count);

    //         if (count($current_batch) > 0) {
    //             // echo "✅ Step $step: Selected departments: " . implode(', ', $current_batch) . '<br/>';

    //             $batch_students = [];
    //             $all_empty = false;

    //             // Round-robin assign students or null placeholders
    //             while (!$all_empty) {
    //                 $all_empty = true;

    //                 foreach ($current_batch as $dept) {
    //                     if (isset($dept_students[$dept]) && !empty($dept_students[$dept])) {
    //                         $batch_students[] = array_shift($dept_students[$dept]);
    //                         $all_empty = false;
    //                     } else {
    //                         $batch_students[] = null;  // empty seat placeholder
    //                     }
    //                 }
    //             }

    //             // Count empty seats (null placeholders)
    //             $empty_seats = count(array_filter($batch_students, fn($s) => $s === null));

    //             // Remove used departments from remaining list
    //             $remaining_departments = array_slice($remaining_departments, $try_count);

    //             // Fill empty seats from next departments in remaining list
    //             if ($empty_seats > 0) {
    //                 $filled_seats = 0;

    //                 foreach ($remaining_departments as $idx => $dept) {
    //                     while (isset($dept_students[$dept]) && !empty($dept_students[$dept]) && $filled_seats < $empty_seats) {
    //                         // Find first null placeholder index
    //                         $null_index = array_search(null, $batch_students, true);
    //                         if ($null_index === false)
    //                             break;

    //                         $batch_students[$null_index] = array_shift($dept_students[$dept]);
    //                         $filled_seats++;
    //                     }
    //                     // Remove department from remaining if no students left
    //                     if (!isset($dept_students[$dept]) || empty($dept_students[$dept])) {
    //                         unset($remaining_departments[$idx]);
    //                     }
    //                     if ($filled_seats >= $empty_seats)
    //                         break;
    //                 }

    //                 // Reindex the remaining departments array after unset(s)
    //                 $remaining_departments = array_values($remaining_departments);
    //             }

    //             // Append batch students to the final list
    //             $flat_students = array_merge($flat_students, $batch_students);

    //             break;  // exit try_count loop, proceed next step
    //         }

    //         $try_count--;
    //     }

    //     $step++;
    // }

    // version 4.0
    // $remaining_departments = $group_keys;
    // $step = 1;
    // $flat_students = [];
    
    // while (!empty($remaining_departments)) {
    //     // Select up to 4 departments that have students
    //     $current_batch = [];
    //     foreach ($remaining_departments as $key => $dept) {
    //         if (!empty($dept_students[$dept])) {
    //             $current_batch[] = $dept;
    //             if (count($current_batch) === 4) break;
    //         } else {
    //             unset($remaining_departments[$key]); // Remove empty dept
    //         }
    //     }
    
    //     if (empty($current_batch)) break; // No more students to assign
    
    //     // Round robin picking students from the selected departments
    //     $batch_students = [];
    //     $all_empty = false;
    
    //     while (!$all_empty) {
    //         $all_empty = true;
    //         foreach ($current_batch as $dept) {
    //             if (!empty($dept_students[$dept])) {
    //                 $batch_students[] = array_shift($dept_students[$dept]);
    //                 $all_empty = false;
    //             } else {
    //                 $batch_students[] = null;
    //             }
    //         }
    //     }
    
    //     // Count empty seats
    //     $empty_seat_indexes = [];
    //     foreach ($batch_students as $i => $student) {
    //         if ($student === null) {
    //             $empty_seat_indexes[] = $i;
    //         }
    //     }
    
    //     // Fill empty seats from other departments not in current batch (max 4 departments total)
    //     if (!empty($empty_seat_indexes)) {
    //         // Find departments NOT in current batch but with students
    //         $other_depts = array_filter($remaining_departments, fn($dept) => !in_array($dept, $current_batch));
    
    //         // We want to keep max 4 departments per batch
    //         // So, we can add new departments from $other_depts to current batch until we reach 4
    
    //         foreach ($other_depts as $new_dept) {
    //             if (count($current_batch) >= 4) break;
    //             if (!empty($dept_students[$new_dept])) {
    //                 $current_batch[] = $new_dept;
    //             }
    //         }
    
    //         // Now fill the empty seats with students from added departments if possible
    //         foreach ($empty_seat_indexes as $index) {
    //             $filled = false;
    //             foreach ($current_batch as $dept) {
    //                 if (isset($batch_students[$index]) && $batch_students[$index] !== null) {
    //                     // Already filled, skip
    //                     continue;
    //                 }
    //                 if (!empty($dept_students[$dept])) {
    //                     // Check no conflict with existing students in batch (dept/course/semester)
    //                     $conflict = false;
    //                     foreach ($batch_students as $stu) {
    //                         if ($stu === null) continue;
    //                         if (
    //                             $stu['department_id'] === $dept_students[$dept][0]['department_id'] &&
    //                             $stu['course'] === $dept_students[$dept][0]['course'] &&
    //                             $stu['semester'] === $dept_students[$dept][0]['semester']
    //                         ) {
    //                             $conflict = true;
    //                             break;
    //                         }
    //                     }
    //                     if ($conflict) continue;
    
    //                     $batch_students[$index] = array_shift($dept_students[$dept]);
    //                     $filled = true;
    //                     break;
    //                 }
    //             }
    //             if (!$filled) {
    //                 // Could not fill this empty seat, leave as null
    //                 $batch_students[$index] = null;
    //             }
    //         }
    //     }
    
    //     // Remove empty departments from remaining_departments
    //     foreach ($remaining_departments as $key => $dept) {
    //         if (empty($dept_students[$dept])) {
    //             unset($remaining_departments[$key]);
    //         }
    //     }
    
    //     $remaining_departments = array_values($remaining_departments);
    //     $flat_students = array_merge($flat_students, $batch_students);
    //     $step++;
    // }
    



    // $benches = [];
    // $index = 0;
    // $student_count = count($flat_students);

    // while ($index < $student_count) {
    //     $bench = ['A' => null, 'B' => null];
    //     $bench['A'] = $flat_students[$index++];

    //     for ($j = $index; $j < $student_count; $j++) {
    //         $candidate = $flat_students[$j];

    //         // Check if candidate differs in department, course, and semester
    //         if (
    //             $candidate['department_id'] !== $bench['A']['department_id'] ||
    //             $candidate['course'] !== $bench['A']['course'] ||
    //             $candidate['semester'] !== $bench['A']['semester']
    //         ) {
    //             $bench['B'] = $candidate;
    //             array_splice($flat_students, $j, 1);
    //             $student_count--;
    //             break;
    //         }
    //     }

    //     $benches[] = $bench;
    // }
    print_r('<pre>');
    // print_r($flat_students);
    print_r('<pre/>');

    // $benches = [];
    // $index = 0;
    // $student_count = count($flat_students);

    // // while ($index < $student_count) {
    // //     $bench = ['A' => null, 'B' => null];

    // //     // Assign seat A only if student exists (not null)
    // //     if ($flat_students[$index] !== null) {
    // //         $bench['A'] = $flat_students[$index];
    // //     }
    // //     $index++;

    // //     for ($j = $index; $j < $student_count; $j++) {
    // //         $candidate = $flat_students[$j];

    // //         // Skip if candidate is null
    // //         if ($candidate === null) {
    // //             continue;
    // //         }

    // //         // Also skip if seat A is null, since we can't compare
    // //         if ($bench['A'] === null) {
    // //             // Just assign candidate to seat B
    // //             $bench['B'] = $candidate;
    // //             array_splice($flat_students, $j, 1);
    // //             $student_count--;
    // //             break;
    // //         }

    // //         // Check if candidate differs in dept, course, and semester from seat A
    // //         if (
    // //             $candidate['department_id'] !== $bench['A']['department_id'] ||
    // //             $candidate['course'] !== $bench['A']['course'] ||
    // //             $candidate['semester'] !== $bench['A']['semester']
    // //         ) {
    // //             $bench['B'] = $candidate;
    // //             array_splice($flat_students, $j, 1);
    // //             $student_count--;
    // //             break;
    // //         }
    // //     }

    // //     $benches[] = $bench;
    // // }
    // $benches = [];
    // $index = 0;
    // $student_count = count($flat_students);
    
    // while ($index < $student_count) {
    //     $bench = ['A' => null, 'B' => null];
    
    //     // Assign seat A if possible
    //     if ($flat_students[$index] !== null) {
    //         $bench['A'] = $flat_students[$index];
    //     }
    //     $index++;
    
    //     for ($j = $index; $j < $student_count; $j++) {
    //         $candidate = $flat_students[$j];
    
    //         if ($candidate === null) {
    //             continue;
    //         }
    
    //         // If seat A is null, just assign candidate to seat B and remove from list
    //         if ($bench['A'] === null) {
    //             $bench['B'] = $candidate;
    //             array_splice($flat_students, $j, 1);
    //             $student_count--;
    //             break;
    //         }
    
    //         // Check department conflict
    //         if (
    //             $candidate['department_id'] !== $bench['A']['department_id'] ||
    //             $candidate['course'] !== $bench['A']['course'] ||
    //             $candidate['semester'] !== $bench['A']['semester']
    //         ) {
    //             // Assign to seat B and remove from list
    //             $bench['B'] = $candidate;
    //             array_splice($flat_students, $j, 1);
    //             $student_count--;
    //             break;
    //         }
    //         // If conflict, try next candidate
    //     }
    
    //     // If no suitable seat B found, seat B remains null
    //     $benches[] = $bench;
    // }
    


    // $remaining_departments = $group_keys;
    // $flat_students = [];
    
    // while (!empty($remaining_departments)) {
    //     // Step 1: Select up to 4 departments with remaining students
    //     $current_batch = [];
    //     foreach ($remaining_departments as $key => $dept) {
    //         if (!empty($dept_students[$dept])) {
    //             $current_batch[] = $dept;
    //             if (count($current_batch) === 4) break;
    //         } else {
    //             unset($remaining_departments[$key]);
    //         }
    //     }
    //     $remaining_departments = array_values($remaining_departments);
    
    //     if (empty($current_batch)) break; // no students left
    
    //     // Step 2: Round-robin pick students from selected departments
    //     $batch_students = [];
    //     $all_empty = false;
    
    //     while (!$all_empty) {
    //         $all_empty = true;
    //         foreach ($current_batch as $dept) {
    //             if (!empty($dept_students[$dept])) {
    //                 $batch_students[] = array_shift($dept_students[$dept]);
    //                 $all_empty = false;
    //             } else {
    //                 $batch_students[] = null;
    //             }
    //         }
    //     }
    
    //     // Step 3: Fill empty seats (nulls) with students from other departments to keep batch size 4
    //     $empty_seat_indexes = [];
    //     foreach ($batch_students as $i => $stu) {
    //         if ($stu === null) $empty_seat_indexes[] = $i;
    //     }
    
    //     if (!empty($empty_seat_indexes)) {
    //         // Other departments not in current batch with students
    //         $other_depts = array_filter($remaining_departments, fn($dept) => !in_array($dept, $current_batch));
    
    //         // Add other departments to current batch until max 4
    //         foreach ($other_depts as $new_dept) {
    //             if (count($current_batch) >= 4) break;
    //             if (!empty($dept_students[$new_dept])) {
    //                 $current_batch[] = $new_dept;
    //             }
    //         }
    
    //         // Try filling empty seats from current batch (now possibly augmented)
    //         foreach ($empty_seat_indexes as $idx) {
    //             $filled = false;
    //             foreach ($current_batch as $dept) {
    //                 if (isset($batch_students[$idx]) && $batch_students[$idx] !== null) continue;
    
    //                 if (!empty($dept_students[$dept])) {
    //                     $candidate = $dept_students[$dept][0];
    
    //                     // Check for conflicts in batch students
    //                     $conflict = false;
    //                     foreach ($batch_students as $stu) {
    //                         if ($stu === null) continue;
    //                         if (
    //                             $stu['department_id'] === $candidate['department_id'] &&
    //                             $stu['course'] === $candidate['course'] &&
    //                             $stu['semester'] === $candidate['semester']
    //                         ) {
    //                             $conflict = true;
    //                             break;
    //                         }
    //                     }
    //                     if ($conflict) continue;
    
    //                     // Assign and remove candidate
    //                     $batch_students[$idx] = array_shift($dept_students[$dept]);
    //                     $filled = true;
    //                     break;
    //                 }
    //             }
    
    //             if (!$filled) {
    //                 $batch_students[$idx] = null; // couldn't fill
    //             }
    //         }
    //     }
    
    //     // Step 4: Clean empty departments
    //     foreach ($remaining_departments as $key => $dept) {
    //         if (empty($dept_students[$dept])) {
    //             unset($remaining_departments[$key]);
    //         }
    //     }
    //     $remaining_departments = array_values($remaining_departments);
    
    //     // Step 5: Append batch students to flat_students
    //     $flat_students = array_merge($flat_students, $batch_students);
    // }
    // $remaining_departments = $group_keys;
    // $flat_students = [];
    
    // while (!empty($remaining_departments)) {
    //     // Step 1: Select up to 4 departments with students
    //     $current_batch = [];
    //     $empty_depts = [];
    
    //     foreach ($remaining_departments as $key => $dept) {
    //         if (!empty($dept_students[$dept])) {
    //             $current_batch[] = $dept;
    //             if (count($current_batch) === 4) break;
    //         } else {
    //             $empty_depts[] = $key; // mark empty for removal
    //         }
    //     }
    
    //     // Remove empty departments from remaining list
    //     foreach ($empty_depts as $key) {
    //         unset($remaining_departments[$key]);
    //     }
    //     $remaining_departments = array_values($remaining_departments);
    
    //     // If no batch formed, break
    //     if (empty($current_batch)) break;
    
    //     // Step 2: Round-robin assign students for current batch
    //     $batch_students = [];
    //     $all_empty = false;
    
    //     while (!$all_empty) {
    //         $all_empty = true;
    //         foreach ($current_batch as $dept) {
    //             if (!empty($dept_students[$dept])) {
    //                 $batch_students[] = array_shift($dept_students[$dept]);
    //                 $all_empty = false;
    //             } else {
    //                 $batch_students[] = null;
    //             }
    //         }
    //     }
    
    //     // Step 3: Fill empty seats from other departments if possible
    //     $empty_seat_indexes = [];
    //     foreach ($batch_students as $i => $stu) {
    //         if ($stu === null) $empty_seat_indexes[] = $i;
    //     }
    
    //     if (!empty($empty_seat_indexes)) {
    //         // Other departments not in current batch with students
    //         $other_depts = array_filter($remaining_departments, fn($dept) => !in_array($dept, $current_batch));
    
    //         // Add other departments until max 4 in batch
    //         foreach ($other_depts as $new_dept) {
    //             if (count($current_batch) >= 4) break;
    //             if (!empty($dept_students[$new_dept])) {
    //                 $current_batch[] = $new_dept;
    //             }
    //         }
    
    //         // Fill empty seats from current batch including new additions
    //         foreach ($empty_seat_indexes as $idx) {
    //             $filled = false;
    //             foreach ($current_batch as $dept) {
    //                 if (isset($batch_students[$idx]) && $batch_students[$idx] !== null) continue;
    
    //                 if (!empty($dept_students[$dept])) {
    //                     $candidate = $dept_students[$dept][0];
    
    //                     // Conflict check
    //                     $conflict = false;
    //                     foreach ($batch_students as $stu) {
    //                         if ($stu === null) continue;
    //                         if (
    //                             $stu['department_id'] === $candidate['department_id'] &&
    //                             $stu['course'] === $candidate['course'] &&
    //                             $stu['semester'] === $candidate['semester']
    //                         ) {
    //                             $conflict = true;
    //                             break;
    //                         }
    //                     }
    //                     if ($conflict) continue;
    
    //                     $batch_students[$idx] = array_shift($dept_students[$dept]);
    //                     $filled = true;
    //                     break;
    //                 }
    //             }
    //             if (!$filled) {
    //                 $batch_students[$idx] = null;
    //             }
    //         }
    //     }
    
    //     // Step 4: Remove fully empty departments
    //     foreach ($remaining_departments as $key => $dept) {
    //         if (empty($dept_students[$dept])) {
    //             unset($remaining_departments[$key]);
    //         }
    //     }
    //     $remaining_departments = array_values($remaining_departments);
    
    //     // Step 5: Add batch to flat students
    //     $flat_students = array_merge($flat_students, $batch_students);
    // }
$remaining_departments = $group_keys; // all department keys
$flat_students = [];
$leftover_students = [];  // students left from processed batches

while (!empty($remaining_departments)) {
    // Step 1: Select up to 4 departments that have students
    $current_batch = [];
    foreach ($remaining_departments as $key => $dept) {
        if (!empty($dept_students[$dept])) {
            $current_batch[] = $dept;
            if (count($current_batch) === 4) break;
        } else {
            unset($remaining_departments[$key]);
        }
    }
    $remaining_departments = array_values($remaining_departments);

    if (empty($current_batch)) break;

    // Step 2: Round robin pick students from current batch
    $batch_students = [];
    $all_empty = false;

    while (!$all_empty) {
        $all_empty = true;
        foreach ($current_batch as $dept) {
            if (!empty($dept_students[$dept])) {
                $batch_students[] = array_shift($dept_students[$dept]);
                $all_empty = false;
            } else {
                $batch_students[] = null;
            }
        }
    }

    // Step 3: Fill empty seats from leftover students and other departments to maintain 4 departments per batch
    $empty_seat_indexes = [];
    foreach ($batch_students as $i => $stu) {
        if ($stu === null) $empty_seat_indexes[] = $i;
    }

    // Combine leftover students from previous rounds + other departments not in current batch
    $additional_departments = array_filter($remaining_departments, fn($dept) => !in_array($dept, $current_batch));
    $additional_students = [];

    // Collect leftover students (if any) from previous batches
    $additional_students = array_merge($additional_students, $leftover_students);

    // Collect students from other departments too
    foreach ($additional_departments as $dept) {
        if (!empty($dept_students[$dept])) {
            $additional_students = array_merge($additional_students, $dept_students[$dept]);
            $dept_students[$dept] = [];  // Clear these as they'll be used here
            unset($remaining_departments[array_search($dept, $remaining_departments)]);
        }
    }
    $remaining_departments = array_values($remaining_departments);

    // Fill empty seats from additional_students without conflicts
    foreach ($empty_seat_indexes as $idx) {
        $filled = false;
        foreach ($additional_students as $k => $candidate) {
            if ($candidate === null) continue;

            // Check conflict with current batch_students (dept/course/semester)
            $conflict = false;
            foreach ($batch_students as $stu) {
                if ($stu === null) continue;
                if (
                    $stu['department_id'] === $candidate['department_id'] &&
                    $stu['course'] === $candidate['course'] &&
                    $stu['semester'] === $candidate['semester']
                ) {
                    $conflict = true;
                    break;
                }
            }
            if ($conflict) continue;

            $batch_students[$idx] = $candidate;
            unset($additional_students[$k]);
            $filled = true;
            break;
        }
        if (!$filled) {
            $batch_students[$idx] = null;
        }
    }

    // Step 4: Save any leftover students that could not fit here for next batches
    $leftover_students = array_values($additional_students);

    // Step 5: Remove empty departments from remaining list
    foreach ($remaining_departments as $key => $dept) {
        if (empty($dept_students[$dept])) {
            unset($remaining_departments[$key]);
        }
    }
    $remaining_departments = array_values($remaining_departments);

    // Step 6: Append this batch students to flat_students list
    $flat_students = array_merge($flat_students, $batch_students);
}

// After this loop, $flat_students contains all assigned students in balanced batches of 4 departments, 
// merged leftover students and other departments dynamically.

// Now you can proceed to create benches from $flat_students as usual.

    
    // Step 6: Build benches (pairs) with department conflict check
    // $benches = [];
    // $index = 0;
    // $student_count = count($flat_students);
    
    // while ($index < $student_count) {
    //     $bench = ['A' => null, 'B' => null];
    
    //     if ($flat_students[$index] !== null) {
    //         $bench['A'] = $flat_students[$index];
    //     }
    //     $index++;
    
    //     for ($j = $index; $j < $student_count; $j++) {
    //         $candidate = $flat_students[$j];
    //         if ($candidate === null) continue;
    
    //         // If seat A empty, assign candidate directly
    //         if ($bench['A'] === null) {
    //             $bench['B'] = $candidate;
    //             array_splice($flat_students, $j, 1);
    //             $student_count--;
    //             break;
    //         }
    
    //         // Check for dept/course/semester conflict with seat A
    //         if (
    //             $candidate['department_id'] !== $bench['A']['department_id'] ||
    //             $candidate['course'] !== $bench['A']['course'] ||
    //             $candidate['semester'] !== $bench['A']['semester']
    //         ) {
    //             $bench['B'] = $candidate;
    //             array_splice($flat_students, $j, 1);
    //             $student_count--;
    //             break;
    //         }
    //     }
    
    //     $benches[] = $bench;
    // }
    // // 

$benches = [];
$index = 0;
$student_count = count($flat_students);

while ($index < $student_count) {
    $bench = ['A' => null, 'B' => null];

    // Tentatively assign seat A if student exists
    if ($flat_students[$index] !== null) {
        $bench['A'] = $flat_students[$index];
    }
    $index++;

    // Now assign seat B
    $found_b = false;
    for ($j = $index; $j < $student_count; $j++) {
        $candidate = $flat_students[$j];
        if ($candidate === null) continue;

        // If seat A is empty, assign candidate to seat A if no conflict with seat B
        if ($bench['A'] === null) {
            $bench['A'] = $candidate;
            array_splice($flat_students, $j, 1);
            $student_count--;
            $found_b = true;
            break;
        }

        // If seat A is filled, seat B must be different dept/course/semester
        if (
            $candidate['department_id'] !== $bench['A']['department_id'] ||
            $candidate['course'] !== $bench['A']['course'] ||
            $candidate['semester'] !== $bench['A']['semester']
        ) {
            $bench['B'] = $candidate;
            array_splice($flat_students, $j, 1);
            $student_count--;
            $found_b = true;
            break;
        }
    }

    // Special case: If seat A is empty but seat B is filled, try to fill seat A with a different student
    if ($bench['A'] === null && $bench['B'] !== null) {
        for ($k = $index; $k < $student_count; $k++) {
            $candidate = $flat_students[$k];
            if ($candidate === null) continue;

            if (
                $candidate['department_id'] !== $bench['B']['department_id'] ||
                $candidate['course'] !== $bench['B']['course'] ||
                $candidate['semester'] !== $bench['B']['semester']
            ) {
                // Assign candidate to seat A
                $bench['A'] = $candidate;
                array_splice($flat_students, $k, 1);
                $student_count--;
                break;
            }
        }
    }

    $benches[] = $bench;
}
    // === Room Layout: [rows × cols] ===
    $rooms_layout = [
        ['rows' => 8, 'cols' => 3],
        ['rows' => 8, 'cols' => 3],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 15, 'cols' => 3],
        ['rows' => 15, 'cols' => 3],
        ['rows' => 15, 'cols' => 2],
        ['rows' => 5, 'cols' => 2],
        ['rows' => 10, 'cols' => 2],
        ['rows' => 5, 'cols' => 2],
        ['rows' => 6, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 5],
        ['rows' => 20, 'cols' => 3],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2],
        ['rows' => 7, 'cols' => 2]
    ];
    $rooms = [];
    $bench_index = 0;

    foreach ($rooms_layout as $layout) {
        $rows = $layout['rows'];
        $cols = $layout['cols'];
        // Initialize grid with empty benches
        $grid = array_fill(0, $rows, array_fill(0, $cols, ['A' => null, 'B' => null]));

        for ($col = 0; $col < $cols; $col++) {
            for ($row = 0; $row < $rows; $row++) {
                if ($bench_index >= count($benches)) {
                    break 2;  // break both loops, no more benches
                }

                $current_bench = $benches[$bench_index];

                // Get department_ids of current bench seats
                $current_depts = array_filter([
                    $current_bench['A']['department_id'] ?? null,
                    $current_bench['B']['department_id'] ?? null,
                ]);

                // Get departments in previous row, same column
                $prev_depts = [];
                if ($row > 0 && !empty($grid[$row - 1][$col])) {
                    $prev = $grid[$row - 1][$col];
                    $prev_depts = array_filter([
                        $prev['A']['department_id'] ?? null,
                        $prev['B']['department_id'] ?? null,
                    ]);
                }

                // Check for any department clash in column
                $conflict = false;
                foreach ($current_depts as $dept) {
                    if (in_array($dept, $prev_depts)) {
                        $conflict = true;
                        break;
                    }
                }

                if ($conflict) {
                    // Leave bench empty to avoid clash
                    $grid[$row][$col] = ['A' => null, 'B' => null];
                } else {
                    $grid[$row][$col] = $current_bench;
                    $bench_index++;
                }
            }
        }

        $rooms[] = $grid;
    }

    // Collect unseated students from remaining benches
    $unseated_students = [];
    while ($bench_index < count($benches)) {
        $bench = $benches[$bench_index++];
        foreach (['A', 'B'] as $seat) {
            if (!empty($bench[$seat])) {
                $unseated_students[] = $bench[$seat];
            }
        }
    }

    // Simple CSS for display
    echo '<style>
        table { border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 10px; min-width: 200px; vertical-align: top; text-align: left; }
        .empty { color: #888; }
        th { background: #eee; }
        h2 { margin-top: 40px; }
    </style>';

    // Display each room's seating grid
    foreach ($rooms as $room_index => $room_grid) {
        echo '<h2>Room ' . ($room_index + 1) . '</h2>';
        $row_count = count($room_grid);
        $col_count = count($room_grid[0]);

        echo '<table><tr>';
        for ($col = 0; $col < $col_count; $col++) {
            echo "<th>Col $col</th>";
        }
        echo '</tr>';

        foreach ($room_grid as $r_index => $row) {
            echo '<tr>';
            foreach ($row as $c_index => $bench) {
                echo "<td><strong>Bench [$r_index,$c_index]</strong><br>";
                foreach (['A', 'B'] as $seat) {
                    if (!empty($bench[$seat])) {
                        $s = $bench[$seat];
                        echo "Seat $seat: {$s['name']} ({$s['department_name']})<br>";
                        echo "Seat $seat: {$s['roll_no']}<br>";

                    } else {
                        echo "Seat $seat: <span class='empty'>Empty</span><br>";
                    }
                }
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    // Show unseated students if any
    if (!empty($unseated_students)) {
        echo '<h3>Unseated Students (No space available):</h3><ul>';
        foreach ($unseated_students as $s) {
            echo "<li>{$s['name']} ({$s['department_name']})</li>";
            echo "Seat $seat: {$s['roll_no']}<br>";
        }
        echo '</ul>';
    }

    print_r('<pre>');
    // print_r($fetchStudents );
    // print_r( $flat_students);
    // print_r($benches);
    print_r('</pre>');
}

// === Optional Allocation Function (Used elsewhere?) ===
function allocateStudentsToRooms(array $rooms, int $total_students): array
{
    $assigned = [];
    foreach ($rooms as $room) {
        if ($total_students <= 0)
            break;
        $capacity = $room['seat_capacity'] * 2;
        $assign = min($capacity, $total_students);
        $assigned[] = [
            'room_no' => $room['room_no'],
            'room_name' => $room['room_name'],
            'banch_order' => $room['bench_order'],
            'room_capacity' => $room['seat_capacity'],
            'students_assigned' => $assign
        ];
        $total_students -= $assign;
    }
    return $assigned;
}
?>

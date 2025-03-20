<?php
class Database
{
    protected $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=sibsagar_university', 'root', 'password');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

class Room extends Database
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();  // Get PDO connection
    }

    public function getAllRooms()
    {
        $sql = 'SELECT * FROM rooms';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Department Class
class Department extends Database
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();  // Get PDO connection
    }

    public function getAllDepartments()
    {
        $sql = 'SELECT * FROM departments';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartmentById($department_id)
    {
        $sql = 'SELECT * FROM departments WHERE department_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$department_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Student Class
class Student extends Database
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();  // Get PDO connection
    }

    public function getAllStudents()
    {
        $sql = 'SELECT * FROM student';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsByCriteria($department, $semester, $course)
    {
        $sql = 'SELECT * FROM student WHERE department = ? AND semester = ? AND course = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$department, $semester, $course]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentByRollNo($roll_no)
    {
        $sql = 'SELECT * FROM student WHERE roll_no = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roll_no]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalStudentsByCriteria($department, $semester, $course)
    {
        $sql = 'SELECT COUNT(*) as total FROM student WHERE department = ? AND semester = ? AND course = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$department, $semester, $course]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result && isset($result['total'])) ? (int) $result['total'] : 0;
    }
}
?>
<?php
class SeatPlan
{
    protected $startTime;
    protected $benchSeat;
    protected $tableData;

    // Constructor with parameters
    public function __construct($startTime, $benchSeat, $tableData)
    {
        $this->startTime = htmlspecialchars($startTime);
        $this->benchSeat = htmlspecialchars($benchSeat);
        $this->tableData = $tableData;
    }

    public function getSeatInfo()
    {
        return "Seating Plan: Start Time - {$this->startTime}, Seats - {$this->benchSeat}";
    }
}

class BenchSeatPlan extends SeatPlan
{
    public function getSingleBench()
    {
        $roomObj = new Room();
        $studentObj = new Student();
        $allRooms = $roomObj->getAllRooms();

        /*
         * $seat_capacities = array_map(function($room) {
         *     return (int) $room['seat_capacity']; // Convert to integer for safety
         * }, $allRooms);
         */

        $seat_capacities = array_map(function ($room) {
            return [
                'room_name' => $room['room_name'],
                'bench_order' => (int) $room['bench_order'],  // Convert to integer
                'seat_capacity' => (int) $room['seat_capacity'],  // Convert to integer
            ];
        }, $allRooms);

        // print_r($seat_capacities); // Print the array for debugging

        $mergedStudents = [];

        foreach ($this->tableData as $info) {
            $department = htmlspecialchars($info['department']);
            $course = htmlspecialchars($info['course']);
            $semester = htmlspecialchars($info['semester']);

            // Fetch students based on criteria
            $students = $studentObj->getStudentsByCriteria($department, $semester, $course);

            // Merge student data into a single array
            $mergedStudents = array_merge($mergedStudents, $students);
        }

        // Remove duplicate student entries if any
        $mergedStudents = array_unique($mergedStudents, SORT_REGULAR);
        $totalStudents = 0;

        foreach ($this->tableData as $info) {
            $department = htmlspecialchars($info['department']);
            $course = htmlspecialchars($info['course']);
            $semester = htmlspecialchars($info['semester']);
            $count = $studentObj->getTotalStudentsByCriteria($department, $semester, $course);
            $totalStudents += $count;  // Add to total count
            // echo "<h6>Department: $department | Course: $course | Semester: $semester | Students: $count</h6>";
        }
        // echo "<h3>Total Students Across All Batches: $totalStudents</h3>";

        list($rooms, $total_seated, $remaining_students) = knnAllocateRooms($seat_capacities, $this->benchSeat, $totalStudents);

        // echo "<h3>Room Allocation using KNN for $total_students Students</h3>\n";
        // echo "<div class=\"container\">";
        // echo "<div class=\"row g-0\">";
        // foreach ($rooms as $index => $room) {
        //     echo "<div class=\"col-4 border\">ok";
        //         echo "Room " . ($index + 1) . ": Capacity " . $room['room_capacity'] . " | Students Assigned: " . $room['students_assigned'] . "<br>\n";
        //     echo "</div>";
        // }
        // echo "</div>";
        // echo "</div>";

        // Display total students seated
        // echo "<h3>Total Students Seated: $total_seated</h3>\n";
?>
<?php
        $student_count = 0;  // Start from index 0 for array
        $room_number = 1;
        $total_students = count($mergedStudents);
?>

<?php
        echo "<div class='container  p-2'>";
        echo "<div class='container mt-3'>";
        echo "    <div class='row align-items-center justify-content-between'>";
        echo "        <div class='col-12 text-cebrer'>";
        echo "            <h5 class='fw-bold mb-0'>Sibsagar University</h5>";
        echo '        </div>';
        echo "        <div class='col-6 text-start'>";
        echo "            <p class='fw-bold mb-0'>Examination Time: " . htmlspecialchars($this->startTime) . '</p>';
        echo '        </div>';
        echo '    </div>';
        echo "    <div class='text-center mt-2'>";
        echo "        <p class='fw-bold'>Students Examination Seat Plan</p>";
        echo '    </div>';
        echo '</div>';
?>


<?php
        echo '<hr/>';
        echo "<div class='row'>";  // Bootstrap row with gap

        foreach ($rooms as $room) {
            $benches_per_row = ceil($room['benches_used'] / $room['bench_order']);
            if (1 == $room['bench_order']) {
                echo "<div class='col-6'>";  // 3 cols on lg, 2 on md, 1 on sm
                echo "<h5 class='fw-bold p-2 fs-6 text-center bg-dark text-white'>Room " . $room['room_name'] . '</h5>';
                echo "<table style='width:100%; border-collapse: collapse; border: 2px solid black;'>";
                echo '<thead>';
                echo "<tr style='border: 1px solid black;'>";
                echo "<th style='border: 1px solid black; padding: 5px;'>Bench No.</th>";
                echo "<th style='border: 1px solid black; padding: 5px;'>Left Seat</th>";
                echo "<th style='border: 1px solid black; padding: 5px;'>Right Seat</th>";
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                for ($bench = 1; $bench <= $room['benches_used']; $bench++) {
                    echo "<tr style='border: 1px solid black;'>";

                    // Bench Number
                    echo "<td style='border: 1px solid black; padding: 5px;'><strong>Bench $bench</strong></td>";

                    if ($bench % 2 == 1) {  // Odd benches: Assign left, leave right empty
                        // Left Seat
                        if ($student_count < $total_students) {
                            echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($mergedStudents[$student_count]['roll_no']) . '</td>';
                            $student_count++;
                        } else {
                            echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                        }

                        // Right Seat (empty)
                        echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                    } else {  // Even benches: Leave left empty, assign right
                        // Left Seat (empty)
                        echo "<td style='border: 1px solid black; padding: 5px;'></td>";

                        // Right Seat
                        if ($student_count < $total_students) {
                            echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($mergedStudents[$student_count]['roll_no']) . '</td>';
                            $student_count++;
                        } else {
                            echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                        }
                    }

                    echo '</tr>';
                }
            } else {
                echo "<div class='col-12'>";
                echo "<h5 class='fw-bold p-2 fs-6 text-center bg-dark text-white'>Room " . $room['room_name'] . '</h5>';
                echo "<table style='width:100%; border-collapse: collapse; border: 2px solid black;'>";
                echo '<thead>';
                echo "<tr style='border: 1px solid black;'>";

                for ($bench_order = 1; $bench_order <= $room['bench_order']; $bench_order++) {
                    echo "<th style='border: 1px solid black; padding: 5px;'>Bench No." . $bench_order . '</th>';
                    echo "<th style='border: 1px solid black; padding: 5px;'>Left Seat</th>";
                    echo "<th style='border: 1px solid black; padding: 5px;'>Right Seat</th>";
                }

                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                // echo $benches_per_row;
                for ($bench = 1; $bench <= $benches_per_row; $bench++) {
                    echo "<tr style='border: 1px solid black;'>";

                    for ($bench_order = 1; $bench_order <= $room['bench_order']; $bench_order++) {
                        // Bench Number
                        echo "<td style='border: 1px solid black; padding: 5px;'><strong>Bench $bench</strong></td>";

                        if ($bench % 2 == 1) {  // Odd benches: Assign left, leave right empty
                            // Left Seat
                            if ($student_count < $total_students) {
                                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($mergedStudents[$student_count]['roll_no']) . '</td>';
                                $student_count++;
                            } else {
                                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                            }

                            // Right Seat (empty)
                            echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                        } else {  // Even benches: Leave left empty, assign right
                            // Left Seat (empty)
                            echo "<td style='border: 1px solid black; padding: 5px;'></td>";

                            // Right Seat
                            if ($student_count < $total_students) {
                                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($mergedStudents[$student_count]['roll_no']) . '</td>';
                                $student_count++;
                            } else {
                                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                            }
                        }
                    }
                    echo '</tr>';
                }
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            $room_number++;
        }

        echo '</div>';  // Close row
        echo '</div>';  // Close container

?>



<?php

        // If students are still left, show warning
        if ($remaining_students > 0) {
            echo "<h3 style='color: red;'>Warning: $remaining_students students could not be seated! No more available rooms.</h3>\n";
        }
        // return "Single Bench allocated at {$this->startTime}";
        return null;
    }

    public function getDoubleBench()
    {
        $roomObj = new Room();
        $studentObj = new Student();
        $allRooms = $roomObj->getAllRooms();
        $seat_capacities = array_map(function ($room) {
            return [
                'room_name' => $room['room_name'],
                'bench_order' => (int) $room['bench_order'],
                'seat_capacity' => (int) $room['seat_capacity'],
            ];
        }, $allRooms);
        // print_r($seat_capacities);
        $studentGroups = [];  // Renamed from $mergedStudents

        foreach ($this->tableData as $info) {
            $department = htmlspecialchars($info['department']);
            $course = htmlspecialchars($info['course']);
            $semester = htmlspecialchars($info['semester']);

            $students = $studentObj->getStudentsByCriteria($department, $semester, $course);
            $studentGroups[$department][$course][$semester] = $students;  // Updated variable name
        }

        $mergedStudents = [];
        $maxCount = max(array_map('count', array_merge(...array_merge(...array_values($studentGroups)))));

        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($studentGroups as $departmentKey => $departments) {
                foreach ($departments as $courseKey => $courses) {
                    foreach ($courses as $semesterKey => $semesterStudents) {
                        if (isset($semesterStudents[$i])) {
                            // Unique tracking within department, course, and semester
                            $key = $departmentKey . '_' . $courseKey . '_' . $semesterKey;

                            if (!isset($seenStudents[$key])) {
                                $seenStudents[$key] = [];
                            }

                            if (!in_array($semesterStudents[$i], $seenStudents[$key], true)) {
                                $mergedStudents[] = $semesterStudents[$i];
                                $seenStudents[$key][] = $semesterStudents[$i];  // Track student within category
                            }
                        }
                    }
                }
            }
        }

        // print_r($mergedStudents);

        $groupedStudents = [];
        $remainingStudents = [];

        $currentGroup = [];
        $lastIdentifier = null;

        foreach ($mergedStudents as $key => $student) {
            // Create an identifier based on department, semester, and course
            $identifier = $student['department'] . '-' . $student['semester'] . '-' . $student['course'];

            if ($lastIdentifier === null || $lastIdentifier === $identifier) {
                // Continue adding to the current group
                $currentGroup[$key] = $student;
            } else {
                // When a new department-semester-course is found, cut and store the last group
                if (count($currentGroup) > 1) {
                    $groupedStudents[] = $currentGroup;  // Store the full group
                } else {
                    $remainingStudents += $currentGroup;  // Store single entries separately
                }
                $currentGroup = [$key => $student];  // Start a new group
            }

            $lastIdentifier = $identifier;
        }

        // Store the last group
        if (count($currentGroup) > 1) {
            $groupedStudents[] = $currentGroup;
        } else {
            $remainingStudents += $currentGroup;
        }

        // Display the grouped results
        $total_groupedStudents = 0;
        
        foreach ($groupedStudents as $group) {
            $total_groupedStudents += count($group);
        }
        
        /*
        echo "<pre>Grouped Students:\n". $total_groupedStudents;
        echo "</pre>";
        echo "<pre>Remaining Students:\n";
        echo count($remainingStudents);
        echo "</pre>";
        */
?>
<?php
        echo "<div class='container  p-2'>";
        echo "<div class='container mt-3'>";
        echo "    <div class='row align-items-center justify-content-between'>";
        echo "        <div class='col-12 text-cebrer'>";
        echo "            <h5 class='fw-bold mb-0'>Sibsagar University</h5>";
        echo '        </div>';
        echo "        <div class='col-6 text-start'>";
        echo "            <p class='fw-bold mb-0'>Examination Time: " . htmlspecialchars($this->startTime) . '</p>';
        echo '        </div>';
        echo '    </div>';
        echo "    <div class='text-center mt-2'>";
        echo "        <p class='fw-bold'>Students Examination Seat Plan</p>";
        echo '    </div>';
        echo '</div>';
?>
<?php
        list($rooms, $total_seated, $remaining_students) =  knnAllocateRooms($seat_capacities, $this->benchSeat, count($remainingStudents));
        $room_names = array_column($rooms, 'room_name'); // Extract all room names
        $room_counts = array_count_values($room_names); // Count occurrences of each room name
        $rooms_to_remove = $room_names;        
        $filtered_rooms = array_filter($allRooms, function ($room) use ($rooms_to_remove) {
            return isset($room['room_name']) && !in_array($room['room_name'], $rooms_to_remove);
        });
        // Now `$filtered_rooms` contains all rooms except NS 8 C and NS 8 D
        // print_r(array_values($filtered_rooms)); // Re-index array
        list($flexible_rooms, $flexible_total_seated, $flexible_remaining_students) =  knnAllocateRooms(array_values($filtered_rooms), 1, $total_groupedStudents);        
        // Ensure $groupedStudents is properly structured
        $groupedStudents = reset($groupedStudents); // Extracts the inner associative array
        $groupedStudents = array_values($groupedStudents); // Reindex numerically
        $total_students = count($groupedStudents);
        $student_count = 0;
        $lastBenchCourse = null;
        $lastBenchSemester = null;
        // print_r($groupedStudents);
        echo '<hr/>';
        echo "<div class='row'>";
        
        // Iterate over flexible rooms
        foreach ($flexible_rooms as $fxroom) {
            $benches_per_row = ceil($fxroom['benches_used'] / $fxroom['bench_order']);
            if (1 == $fxroom['bench_order']){
                echo 'ok';
            } else {
                echo "<div class='col-12'>";
                echo "<h5 class='fw-bold p-2 fs-6 text-center bg-dark text-white'>Room " . $fxroom['room_name'] . '</h5>';
                echo "<table style='width:100%; border-collapse: collapse; border: 2px solid black;'>";
                echo '<thead>';                
                echo "<tr style='border: 1px solid black;'>";
                for ($bench_order = 1; $bench_order <= $fxroom['bench_order']; $bench_order++) {
                    echo "<th style='border: 1px solid black; padding: 5px;'>Bench No." . $bench_order . '</th>';
                    echo "<th style='border: 1px solid black; padding: 5px;'>Left Seat</th>";
                    echo "<th style='border: 1px solid black; padding: 5px;'>Right Seat</th>";
                }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                for ($bench = 1; $bench <= $benches_per_row; $bench++) {

                    echo "<tr style='border: 1px solid black;'>";
                    for ($bench_order = 1; $bench_order <= $fxroom['bench_order']; $bench_order++) {
                        echo "<td style='border: 1px solid black; padding: 5px;'><strong>Bench $bench</strong></td>";
                        if ($bench % 2 == 1) {  // Odd benches: Assign left, leave right empty
                            // Left Seat
                            if ($student_count < $total_students) {
                                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($groupedStudents[$student_count]['roll_no']) . '</td>';
                                $student_count++;
                            } else {
                                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                            }
                
                            // Right Seat (empty)
                            echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                        } else {  // Even benches: Leave left empty, assign right
                            // Left Seat (empty)
                            echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                
                            // Right Seat
                            if ($student_count < $total_students) {
                                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($groupedStudents[$student_count]['roll_no']) . '</td>';
                                $student_count++;
                            } else {
                                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
                            }
                        }
                    }
                    echo '</tr>';
                }

            }
            /*         
            for ($bench = 1; $bench <= $fxroom['benches_used']; $bench++) {
                
               
        
                // Bench Number
                
        

        
                
            }
            // }
            */
            echo '</tbody>';
            echo '</table>';
            echo '</div>'; // Close col div
        }
        
        echo '</div>'; // Close row div
        // echo '</div>'; // Close container
?>
<?php

        /*
        $totalStudents = 0;
        foreach ($this->tableData as $info) {
            $department = htmlspecialchars($info['department']);
            $course = htmlspecialchars($info['course']);
            $semester = htmlspecialchars($info['semester']);
            $count = $studentObj->getTotalStudentsByCriteria($department, $semester, $course);
            $totalStudents += $count;
             echo "<h6>Department: $department | Course: $course | Semester: $semester | Students: $count</h6>";
        }
        echo "<h3>Total Students Across All Batches: $totalStudents</h3>";
        */

?>

<?php        
        // print_r($remainingStudents);
        $student_count = 0;  // Start from index 0 for array
        $room_number = 1;
        $total_students = count($remainingStudents);
        echo '<hr/>';
        echo "<div class='row'>";

        foreach ($rooms as $room) {
            $benches_per_row = ceil($room['benches_used'] / $room['bench_order']);

            echo "<div class='col-12'>";
            echo "<h5 class='fw-bold p-2 fs-6 text-center bg-dark text-white'>Room " . htmlspecialchars($room['room_name']) . '</h5>';
            echo "<table style='width:100%; border-collapse: collapse; border: 2px solid black;'>";
            echo '<thead>';
            echo "<tr style='border: 1px solid black;'>";

            for ($bench_order = 1; $bench_order <= $room['bench_order']; $bench_order++) {
                echo "<th style='border: 1px solid black; padding: 5px;'>Bench No.</th>";
                echo "<th style='border: 1px solid black; padding: 5px;'>Left Seat</th>";
                echo "<th style='border: 1px solid black; padding: 5px;'>Right Seat</th>";
            }

            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            for ($bench = 1; $bench <= $benches_per_row; $bench++) {
                echo "<tr style='border: 1px solid black;'>";

                for ($bench_order = 1; $bench_order <= $room['bench_order']; $bench_order++) {
                    echo "<td style='border: 1px solid black; padding: 5px;'><strong>Bench " . ($bench + ($bench_order - 1) * $benches_per_row) . '</strong></td>';

                    $lastBenchCourse = '';
                    $lastBenchSemester = '';
                    $rowHasConflict = false;

                    switch ($room['bench_order']) {
                        case 1:
                            // One Seat Per Bench (Alternating Zigzag)
                            if ($bench % 2 == 1) {
                                $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count]['roll_no']) : '';
                                $right_seat = '';  // Empty right seat
                            } else {
                                $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count]['roll_no']) : '';
                                $left_seat = '';  // Empty left seat
                            }
                            break;

                        case 2:
                            $currentCourse = $remainingStudents[$student_count]['course'] ?? '';
                            $currentSemester = $remainingStudents[$student_count]['semester'] ?? '';

                            // Check if the last assigned student had the same course & semester
                            if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                // Enforce Zigzag by assigning only one student per row
                                if ($bench % 2 == 1) {
                                    // Assign student to left seat, keep right seat empty
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';  // No student on right seat
                                } else {
                                    // Assign student to right seat, keep left seat empty
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';  // No student on left seat
                                }
                            } else {
                                // Normal Zigzag Assignment
                                if ($bench % 2 == 1) {
                                    // Assign left seat first, then right seat
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                } else {
                                    // Assign right seat first, then left seat
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                }
                            }

                            // Store assigned values to check for the next student
                            $lastBenchCourse = $currentCourse;
                            $lastBenchSemester = $currentSemester;
                            break;

                        case 3:
                            // Three Benches per row with Conflict Prevention
                            $currentCourse = $remainingStudents[$student_count]['course'] ?? '';
                            $currentSemester = $remainingStudents[$student_count]['semester'] ?? '';

                            if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                // Conflict detected: Assign only one seat in zigzag
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';
                                }
                            } else {
                                // Normal zigzag assignment
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                }
                            }

                            // Store assigned values
                            $lastBenchCourse = $currentCourse;
                            $lastBenchSemester = $currentSemester;
                            break;

                        case 4:
                            // Full Zigzag with Conflict Prevention
                            $currentCourse = $remainingStudents[$student_count]['course'] ?? '';
                            $currentSemester = $remainingStudents[$student_count]['semester'] ?? '';

                            if ($bench % 4 == 1 || $bench % 4 == 2) {
                                // First 2 Benches → Normal Order
                                if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';
                                } else {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                }
                            } else {
                                // Next 2 Benches → Reverse Order
                                if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                }
                            }

                            // Store assigned values
                            $lastBenchCourse = $currentCourse;
                            $lastBenchSemester = $currentSemester;
                            break;
                        case 5:
                            // Get current student course & semester
                            $currentCourse = $remainingStudents[$student_count]['course'] ?? '';
                            $currentSemester = $remainingStudents[$student_count]['semester'] ?? '';

                            // Check if all benches in this row have the same course & semester
                            if ($bench % 4 == 1) {  // New row starts (assuming 4 benches per row)
                                $lastRowCourse = $currentCourse;
                                $lastRowSemester = $currentSemester;
                                $rowHasConflict = false;
                            } else {
                                if ($currentCourse === $lastRowCourse && $currentSemester === $lastRowSemester) {
                                    $rowHasConflict = true;  // Mark row as conflict row
                                }
                            }

                            if ($rowHasConflict) {
                                // If row has a conflict, assign only one seat per bench in zigzag format
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';
                                }
                            } else {
                                // Normal zigzag assignment
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                }
                            }

                            break;

                        default:
                            // Default seating with Conflict Prevention
  
                            $currentCourse = $remainingStudents[$student_count]['course'] ?? '';
                            $currentSemester = $remainingStudents[$student_count]['semester'] ?? '';

                            if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                $right_seat = '';
                            } else {
                                $left_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                                $right_seat = ($student_count < $total_students) ? htmlspecialchars($remainingStudents[$student_count++]['roll_no']) : '';
                            }

                            // Store assigned values
                            $lastBenchCourse = $currentCourse;
                            $lastBenchSemester = $currentSemester;
                            break;

                    }

                    echo "<td style='border: 1px solid black; padding: 5px;'><strong>$left_seat</strong></td>";
                    echo "<td style='border: 1px solid black; padding: 5px;'><strong>$right_seat</strong></td>";
                }

                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';  // Close col-12 div
        }

        echo '</div>';  // Close row div
        echo '</div>';
        return null;
    }
    
}
?>
<?php
function knnAllocateRooms($room_capacities, $bench_seat, $total_students)
{
    // Sort rooms by seat capacity in ascending order
    usort($room_capacities, function ($a, $b) {
        return $a['seat_capacity'] - $b['seat_capacity'];
    });

    $allocated_rooms = [];
    $remaining_students = $total_students;
    $total_seated = 0;
    $total_benches_used = 0;

    while ($remaining_students > 0 && !empty($room_capacities)) {
        // Find the closest room capacity using KNN logic
        $nearest_room = null;
        $min_difference = PHP_INT_MAX;
        $room_index = -1;

        foreach ($room_capacities as $index => $room) {
            $capacity = $room['seat_capacity'];
            $actual_capacity = $capacity * $bench_seat;
            $difference = abs($actual_capacity - $remaining_students);

            if ($difference < $min_difference) {
                $min_difference = $difference;
                $nearest_room = $room;
                $room_index = $index;
            }
        }

        // If no suitable room is found, break
        if ($nearest_room === null) {
            break;
        }

        // Calculate actual seating capacity
        $actual_capacity = $nearest_room['seat_capacity'] * $bench_seat;
        $students_assigned = min($actual_capacity, $remaining_students);
        $benches_used = ceil($students_assigned / $bench_seat);  // Calculate benches used

        // Store allocated room details including name and bench order
        $allocated_rooms[] = [
            'room_name' => $nearest_room['room_name'],
            'bench_order' => $nearest_room['bench_order'],
            'room_capacity' => $nearest_room['seat_capacity'],
            'bench_seat' => $bench_seat,
            'actual_seating_capacity' => $actual_capacity,
            'students_assigned' => $students_assigned,
            'benches_used' => $benches_used
        ];

        // Reduce remaining students
        $remaining_students -= $students_assigned;
        $total_seated += $students_assigned;
        $total_benches_used += $benches_used;

        // Remove used room
        unset($room_capacities[$room_index]);
        $room_capacities = array_values($room_capacities);  // Re-index array
    }

    return [$allocated_rooms, $total_seated, $remaining_students];
}



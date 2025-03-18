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
        // print_r($rooms);
        echo "<div class='container  p-2'>";

?>

<?php
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
                // echo "<h5 class='fw-bold p-2 fs-6 text-center bg-dark text-white'>Room $room_number</h5>";
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
                                $seenStudents[$key][] = $semesterStudents[$i]; // Track student within category
                            }
                        }
                    }
                }
            }
        }
        
        $uniqueStudents = [];
        $duplicateStudents = [];
        
        // Track seen department-semester-course combinations
        $seenCombinations = [];
        
        foreach ($mergedStudents as $key => $student) {
            // Unique identifier based on department, semester, and course
            $identifier = $student['department'] . '-' . $student['semester'] . '-' . $student['course'];
        
            if (!isset($seenCombinations[$identifier])) {
                // If this combination is not seen before, add it to unique students
                $seenCombinations[$identifier] = true;
                $uniqueStudents[$key] = $student;
            } else {
                // If it's repeated, move it to duplicate students
                $duplicateStudents[$key] = $student;
            }
        }
        
// Display Results
echo "<pre>Unique Students:\n";
print_r($uniqueStudents);
echo "</pre>";

echo "<pre>Duplicate Students:\n";
print_r($duplicateStudents);
echo "</pre>";
        /*
        // Flattening the array while merging alternately
        $mergedStudents = [];  // Renamed from $finalStudentList
        $maxCount = max(array_map('count', array_merge(...array_merge(...array_values($studentGroups)))));

        

        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($studentGroups as $departments) {
                foreach ($departments as $courses) {
                    foreach ($courses as $semesterStudents) {
                        if (isset($semesterStudents[$i])) {
                            $mergedStudents[] = $semesterStudents[$i];  // Updated variable name
                        }
                    }
                }
            }
        }
        

        // Remove duplicates
        $mergedStudents = array_unique($mergedStudents, SORT_REGULAR);

        // Print merged student list
        // print_r($mergedStudents);
        */
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

        // list($rooms, $total_seated, $remaining_students) = allocateFlexibleRooms($seat_capacities, $this->benchSeat, $totalStudents);
        list($rooms, $total_seated, $remaining_students) = allocateFlexibleRooms($seat_capacities, $this->benchSeat, $totalStudents);
        $student_count = 0;  // Start from index 0 for array
        $room_number = 1;
        $total_students = count($mergedStudents);
        echo "<div class='container  p-2'>";
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
                                $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count]['roll_no']) : '';
                                $right_seat = '';  // Empty right seat
                            } else {
                                $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count]['roll_no']) : '';
                                $left_seat = '';  // Empty left seat
                            }
                            break;

                        case 2:
                            // Zigzag with Conflict Prevention (Same Course/Semester Avoidance)
                            $currentCourse = $mergedStudents[$student_count]['course'] ?? '';
                            $currentSemester = $mergedStudents[$student_count]['semester'] ?? '';

                            if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                // If same course & semester, assign only one seat in zigzag
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';
                                }
                            } else {
                                // Normal allocation
                                $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                            }

                            // Store assigned values
                            $lastBenchCourse = $currentCourse;
                            $lastBenchSemester = $currentSemester;
                            break;

                        case 3:
                            // Three Benches per row with Conflict Prevention
                            $currentCourse = $mergedStudents[$student_count]['course'] ?? '';
                            $currentSemester = $mergedStudents[$student_count]['semester'] ?? '';

                            if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                // Conflict detected: Assign only one seat in zigzag
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';
                                }
                            } else {
                                // Normal zigzag assignment
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                }
                            }

                            // Store assigned values
                            $lastBenchCourse = $currentCourse;
                            $lastBenchSemester = $currentSemester;
                            break;

                        case 4:
                            // Full Zigzag with Conflict Prevention
                            $currentCourse = $mergedStudents[$student_count]['course'] ?? '';
                            $currentSemester = $mergedStudents[$student_count]['semester'] ?? '';

                            if ($bench % 4 == 1 || $bench % 4 == 2) {
                                // First 2 Benches → Normal Order
                                if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';
                                } else {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                }
                            } else {
                                // Next 2 Benches → Reverse Order
                                if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                }
                            }

                            // Store assigned values
                            $lastBenchCourse = $currentCourse;
                            $lastBenchSemester = $currentSemester;
                            break;
                        case 5:
                            // Get current student course & semester
                            $currentCourse = $mergedStudents[$student_count]['course'] ?? '';
                            $currentSemester = $mergedStudents[$student_count]['semester'] ?? '';

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
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = '';
                                }
                            } else {
                                // Normal zigzag assignment
                                if ($bench % 2 == 1) {
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                } else {
                                    $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                    $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                }
                            }

                            break;

                        default:
                            // Default seating with Conflict Prevention
                            $currentCourse = $mergedStudents[$student_count]['course'] ?? '';
                            $currentSemester = $mergedStudents[$student_count]['semester'] ?? '';

                            if ($currentCourse === $lastBenchCourse && $currentSemester === $lastBenchSemester) {
                                $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                $right_seat = '';
                            } else {
                                $left_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
                                $right_seat = ($student_count < $total_students) ? htmlspecialchars($mergedStudents[$student_count++]['roll_no']) : '';
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

function allocateFlexibleRooms($room_capacities, $bench_seat, $total_students)
{
    // Sort rooms by seat capacity in ascending order
    usort($room_capacities, function ($a, $b) {
        return $a['seat_capacity'] - $b['seat_capacity'];
    });

    $allocated_rooms = [];
    $remaining_students = $total_students;
    $total_seated = 0;
    $total_benches_used = 0;
    $bench_allocation = []; // Track which students are seated on which bench

    while ($remaining_students > 0 && !empty($room_capacities)) {
        // Find the closest room capacity using KNN logic
        $nearest_room = null;
        $min_difference = PHP_INT_MAX;
        $room_index = -1;

        foreach ($room_capacities as $index => $room) {
            $actual_capacity = $room['seat_capacity'] * $bench_seat;
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
        $benches_used = ceil($students_assigned / $bench_seat);

        // Track assigned students
        $bench_counter = 1;
        for ($i = 0; $i < $students_assigned; $i++) {
            $bench_key = "Bench " . $bench_counter;

            if (!isset($bench_allocation[$bench_key])) {
                $bench_allocation[$bench_key] = [];
            }

            $bench_allocation[$bench_key][] = "Student " . ($total_seated + 1);

            // Prevent conflicts by moving to the next bench
            $bench_counter++;
            if ($bench_counter > $benches_used) {
                $bench_counter = 1;
            }

            $total_seated++;
        }

        // Store allocated room details
        $allocated_rooms[] = [
            'room_name' => $nearest_room['room_name'],
            'bench_order' => $nearest_room['bench_order'],
            'room_capacity' => $nearest_room['seat_capacity'],
            'bench_seat' => $bench_seat,
            'actual_seating_capacity' => $actual_capacity,
            'students_assigned' => $students_assigned,
            'benches_used' => $benches_used,
            'bench_allocation' => $bench_allocation
        ];

        // Reduce remaining students
        $remaining_students -= $students_assigned;

        // Remove used room
        unset($room_capacities[$room_index]);
        $room_capacities = array_values($room_capacities);
    }

    return [$allocated_rooms, $total_seated, $remaining_students];
}
?>
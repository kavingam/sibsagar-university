<?php
class Database {
    protected $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=sibsagar_university", "root", "password");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    public function getConnection() {
        return $this->conn;
    }
}
class Room extends Database {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection(); // Get PDO connection
    }

    public function getAllRooms() {
        $sql = "SELECT * FROM rooms";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Department Class
class Department extends Database {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection(); // Get PDO connection
    }
    public function getAllDepartments() {
        $sql = "SELECT * FROM departments";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDepartmentById($department_id) {
        $sql = "SELECT * FROM departments WHERE department_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$department_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Student Class
class Student extends Database {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection(); // Get PDO connection
    }
    public function getAllStudents() {
        $sql = "SELECT * FROM student";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getStudentsByCriteria($department, $semester, $course) {
        $sql = "SELECT * FROM student WHERE department = ? AND semester = ? AND course = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$department, $semester, $course]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getStudentByRollNo($roll_no) {
        $sql = "SELECT * FROM student WHERE roll_no = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roll_no]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getTotalStudentsByCriteria($department, $semester, $course) {
        $sql = "SELECT COUNT(*) as total FROM student WHERE department = ? AND semester = ? AND course = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$department, $semester, $course]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result && isset($result['total'])) ? (int)$result['total'] : 0;
    }
    
}
?>
<?php 
class SeatPlan {
    protected $startTime;
    protected $benchSeat;
    protected $tableData;

    // Constructor with parameters
    public function __construct($startTime, $benchSeat, $tableData) {
        $this->startTime = htmlspecialchars($startTime);
        $this->benchSeat = htmlspecialchars($benchSeat);
        $this->tableData = $tableData;
    }

    public function getSeatInfo() {
        return "Seating Plan: Start Time - {$this->startTime}, Seats - {$this->benchSeat}";
    }

}

class BenchSeatPlan extends SeatPlan {
    public function getSingleBench() {
        $roomObj = new Room();
        $studentObj = new Student();
        $allRooms = $roomObj->getAllRooms();

        $seat_capacities = array_map(function($room) {
            return (int) $room['seat_capacity']; // Convert to integer for safety
        }, $allRooms);

        // foreach ($this->tableData as $info) {}

        /*
        print_r($roomObj->getAllRooms());
        
        foreach ($this->tableData as $info) {
            $department = htmlspecialchars($info['department']);
            $course = htmlspecialchars($info['course']);
            $semester = htmlspecialchars($info['semester']);
            $students = $studentObj->getStudentsByCriteria($department, $semester, $course);
        
            // Display results
            if (!empty($students)) {
                echo "<h6>Students in $department - $course (Semester: $semester)</h6 >";
                echo "<table border='1'>";
                echo "<tr><th>Roll No</th><th>Name</th><th>Department</th><th>Course</th><th>Semester</th></tr>";
        
                foreach ($students as $student) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($student['roll_no']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['department']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['course']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['semester']) . "</td>";
                    echo "</tr>";
                }
                echo "</table><br>";
            } else {
                echo "<p>No students found for $department - $course (Semester: $semester).</p><hr>";
            }
        }
        */

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
        
        // Display results if students exist
        /*
        if (!empty($mergedStudents)) {
            echo "<h4 class='text-center'>All Students List</h4>";
            echo "<table border='1'>";
            echo "<tr><th>Roll No</th><th>Name</th><th>Department</th><th>Course</th><th>Semester</th></tr>";
        
            foreach ($mergedStudents as $student) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($student['roll_no']) . "</td>";
                echo "<td>" . htmlspecialchars($student['name']) . "</td>";
                echo "<td>" . htmlspecialchars($student['department']) . "</td>";
                echo "<td>" . htmlspecialchars($student['course']) . "</td>";
                echo "<td>" . htmlspecialchars($student['semester']) . "</td>";
                echo "</tr>";
            }
        
            echo "</table>";
        } else {
            echo "<p class='text-danger'>No students found.</p>";
        }
        */
        
        

        // print_r($roomObj->getAllRooms());

        $totalStudents = 0;
        foreach ($this->tableData as $info) {
            $department = htmlspecialchars($info['department']);
            $course = htmlspecialchars($info['course']);
            $semester = htmlspecialchars($info['semester']);
            $count = $studentObj->getTotalStudentsByCriteria($department, $semester, $course);
            $totalStudents += $count; // Add to total count
            // echo "<h6>Department: $department | Course: $course | Semester: $semester | Students: $count</h6>";
        }
        // echo "<h3>Total Students Across All Batches: $totalStudents</h3>";

        list($rooms,$total_seated,$remaining_students) = knnAllocateRooms($seat_capacities,$this->benchSeat,$totalStudents);

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
$student_count = 0; // Start from index 0 for array
$room_number = 1;
$total_students = count($mergedStudents);

echo "<div class='container  p-2'>";

?>

<?php
echo "<div class='container mt-3'>";
echo "    <div class='row align-items-center justify-content-between'>";
echo "        <div class='col-12 text-cebrer'>";
echo "            <h5 class='fw-bold mb-0'>Sibsagar University</h5>";
echo "        </div>";
echo "        <div class='col-6 text-start'>";
echo "            <p class='fw-bold mb-0'>Examination Date: " . htmlspecialchars($this->startTime) . "</p>";
echo "        </div>";
echo "    </div>";
echo "    <div class='text-center mt-2'>";
echo "        <p class='fw-bold'>Student Seat Plan</p>";
echo "    </div>";
echo "</div>";
?>


<?php
echo "<hr/>";
echo "<div class='row'>"; // Bootstrap row with gap

foreach ($rooms as $room) {
    echo "<div class='col-6'>"; // 3 cols on lg, 2 on md, 1 on sm
    echo "<h5 class='fw-bold p-2 fs-6 text-center bg-dark text-white'>Room $room_number</h5>";               
    echo "<table style='width:100%; border-collapse: collapse; border: 2px solid black;'>"; 
    echo "<thead>";
    echo "<tr style='border: 1px solid black;'>";
    echo "<th style='border: 1px solid black; padding: 5px;'>Bench No.</th>";
    echo "<th style='border: 1px solid black; padding: 5px;'>Left Seat</th>";
    echo "<th style='border: 1px solid black; padding: 5px;'>Right Seat</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    for ($bench = 1; $bench <= $room['benches_used']; $bench++) {
        echo "<tr style='border: 1px solid black;'>";

        // Bench Number
        echo "<td style='border: 1px solid black; padding: 5px;'><strong>Bench $bench</strong></td>";

        if ($bench % 2 == 1) { // Odd benches: Assign left, leave right empty
            // Left Seat
            if ($student_count < $total_students) {
                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($mergedStudents[$student_count]['roll_no']) . "</td>";
                $student_count++;
            } else {
                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
            }

            // Right Seat (empty)
            echo "<td style='border: 1px solid black; padding: 5px;'></td>";

        } else { // Even benches: Leave left empty, assign right
            // Left Seat (empty)
            echo "<td style='border: 1px solid black; padding: 5px;'></td>";

            // Right Seat
            if ($student_count < $total_students) {
                echo "<td style='border: 1px solid black; padding: 5px;'>" . htmlspecialchars($mergedStudents[$student_count]['roll_no']) . "</td>";
                $student_count++;
            } else {
                echo "<td style='border: 1px solid black; padding: 5px;'></td>";
            }
        }

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";        
    $room_number++;
}

echo "</div>"; // Close row
echo "</div>"; // Close container
?>



<?php                
        

        // If students are still left, show warning
        if ($remaining_students > 0) {
            echo "<h3 style='color: red;'>Warning: $remaining_students students could not be seated! No more available rooms.</h3>\n";
        }
        // return "Single Bench allocated at {$this->startTime}";
        return null;
    }
    
}


// Create an instance of BenchSeatPlan
// $benchPlan = new BenchSeatPlan();

// Call the getSingleBench() method and display the result
// echo $benchPlan->getSingleBench();
?>

<?php
// Create an instance of Room class
$roomObj = new Room();
$allRooms = $roomObj->getAllRooms();

// Extract seat capacities into an array
$seat_capacities = array_map(function($room) {
    return (int) $room['seat_capacity']; // Convert to integer for safety
}, $allRooms);

// Output the seat capacities array
// echo "<pre>";
// print_r($seat_capacities);
// echo "</pre>";


// Output Room Details
// echo "<h2>All Room Details</h2>";
// foreach ($allRooms as $room) {
//     echo "Room No: " . htmlspecialchars($room['room_no']) . "<br>";
//     echo "Room Name: " . htmlspecialchars($room['room_name']) . "<br>";
//     echo "Bench Order: " . htmlspecialchars($room['bench_order']) . "<br>";
//     echo "Seat Capacity: " . htmlspecialchars($room['seat_capacity']) . "<br><br>";
// }



function knnAllocateRooms($room_capacities, $bench_seat, $total_students) {
    // Sort room capacities in ascending order
    sort($room_capacities);

    $allocated_rooms = [];
    $remaining_students = $total_students;
    $total_seated = 0;
    $total_benches_used = 0;

    while ($remaining_students > 0 && !empty($room_capacities)) {
        // Find the closest room capacity using KNN logic
        $nearest_room = null;
        $min_difference = PHP_INT_MAX;
        $room_index = -1;

        foreach ($room_capacities as $index => $capacity) {
            // Actual seating capacity considering bench_seat
            $actual_capacity = $capacity * $bench_seat;
            $difference = abs($actual_capacity - $remaining_students);

            if ($difference < $min_difference) {
                $min_difference = $difference;
                $nearest_room = $capacity;
                $room_index = $index;
            }
        }

        // If no suitable room is found, break
        if ($nearest_room === null) {
            break;
        }

        // Calculate actual capacity of the chosen room
        $actual_capacity = $nearest_room * $bench_seat;
        $students_assigned = min($actual_capacity, $remaining_students);
        $benches_used = ceil($students_assigned / $bench_seat); // Calculate benches used

        $allocated_rooms[] = [
            'room_capacity' => $nearest_room,
            'bench_seat' => $bench_seat,
            'actual_seating_capacity' => $actual_capacity,
            'students_assigned' => $students_assigned,
            'benches_used' => $benches_used
        ];

        // Reduce remaining students
        $remaining_students -= $students_assigned;
        $total_seated += $students_assigned;
        $total_benches_used += $benches_used;

        // Remove the used room
        unset($room_capacities[$room_index]);
        $room_capacities = array_values($room_capacities); // Re-index array
    }

    return [$allocated_rooms, $total_seated, $remaining_students, $total_benches_used];
}

// Example Usage:
// $room_capacities = [10, 20, 15]; // Number of benches in each room
// $bench_seat = 2; // Two seats per bench
// $total_students = 35;

// list($allocated_rooms, $total_seated, $remaining_students, $total_benches_used) = knnAllocateRooms($room_capacities, $bench_seat, $total_students);

// print_r($allocated_rooms);
// echo "Total Seated: $total_seated\n";
// echo "Remaining Students: $remaining_students\n";
// echo "Total Benches Used: $total_benches_used\n";
?>

<?php

?>
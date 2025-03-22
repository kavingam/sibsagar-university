<?php
require_once 'Database.php';

class BaseModel {
    protected $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // 🔥 Fetch all records
    public function getAll($table) {
        $allowedTables = ['student', 'rooms', 'departments'];

        if (!in_array($table, $allowedTables)) {
            die("Invalid table name!");
        }

        $sql = "SELECT * FROM `$table`";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🛑 Delete all records
    public function deleteAll($table) {
        $allowedTables = ['student', 'rooms', 'departments'];

        if (!in_array($table, $allowedTables)) {
            die("Invalid table name!");
        }

        $sql = "DELETE FROM `$table`";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }

    // 🔢 Get total count
    public function getCount($table) {
        $allowedTables = ['student', 'rooms', 'departments'];

        if (!in_array($table, $allowedTables)) {
            die("Invalid table name!");
        }

        $sql = "SELECT COUNT(*) AS total FROM `$table`";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}

// ✅ Student CRUD
class Student extends BaseModel {
    public function createStudent($roll_no, $name, $department, $semester, $course) {
        $sql = "INSERT INTO student (roll_no, name, department, semester, course) VALUES (:roll_no, :name, :department, :semester, :course)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':roll_no' => $roll_no,
            ':name' => $name,
            ':department' => $department,
            ':semester' => $semester,
            ':course' => $course
        ]);
    }

    public function getAllStudents() {
        return $this->getAll('student');
    }

    public function updateStudent($roll_no, $name, $department, $semester, $course) {
        $sql = "UPDATE student SET name = :name, department = :department, semester = :semester, course = :course WHERE roll_no = :roll_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':roll_no' => $roll_no,
            ':name' => $name,
            ':department' => $department,
            ':semester' => $semester,
            ':course' => $course
        ]);
    }

    public function deleteStudent($roll_no) {
        $sql = "DELETE FROM student WHERE roll_no = :roll_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':roll_no' => $roll_no]);
    }

    public function getStudentCount() {
        return $this->getCount('student');
    }
}

// ✅ Room CRUD
class Room extends BaseModel {
    public function createRoom($room_no, $room_name, $bench_order, $seat_capacity) {
        $sql = "INSERT INTO rooms (room_no, room_name, bench_order, seat_capacity) VALUES (:room_no, :room_name, :bench_order, :seat_capacity)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':room_no' => $room_no,
            ':room_name' => $room_name,
            ':bench_order' => $bench_order,
            ':seat_capacity' => $seat_capacity
        ]);
    }

    public function getAllRooms() {
        return $this->getAll('rooms');
    }

    public function updateRoom($room_no, $room_name, $bench_order, $seat_capacity) {
        $sql = "UPDATE rooms SET room_name = :room_name, bench_order = :bench_order, seat_capacity = :seat_capacity WHERE room_no = :room_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':room_no' => $room_no,
            ':room_name' => $room_name,
            ':bench_order' => $bench_order,
            ':seat_capacity' => $seat_capacity
        ]);
    }

    public function deleteRoom($room_no) {
        $sql = "DELETE FROM rooms WHERE room_no = :room_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':room_no' => $room_no]);
    }

    public function getRoomCount() {
        return $this->getCount('rooms');
    }
}

// ✅ Department CRUD
class Department extends BaseModel {
    public function createDepartment($department_id, $department_name) {
        $sql = "INSERT INTO departments (department_id, department_name) VALUES (:department_id, :department_name)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':department_id' => $department_id,
            ':department_name' => $department_name
        ]);
    }

    public function getAllDepartments() {
        return $this->getAll('departments');
    }

    public function updateDepartment($department_id, $department_name) {
        $sql = "UPDATE departments SET department_name = :department_name WHERE department_id = :department_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':department_id' => $department_id,
            ':department_name' => $department_name
        ]);
    }

    public function deleteDepartment($department_id) {
        $sql = "DELETE FROM departments WHERE department_id = :department_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':department_id' => $department_id]);
    }

    public function getDepartmentCount() {
        return $this->getCount('departments');
    }
}
/*
// ✅ Usage Example
$student = new Student();
$student->createStudent('S103', 'Alice Brown', '1', '3', '5');
$students = $student->getAllStudents();
$studentCount = $student->getStudentCount();

$room = new Room();
$room->createRoom('R003', 'Physics Lab', 2, 50);
$rooms = $room->getAllRooms();
$roomCount = $room->getRoomCount();

$department = new Department();
$department->createDepartment('D03', 'Physics');
$departments = $department->getAllDepartments();
$departmentCount = $department->getDepartmentCount();

// ✅ Output Data
echo "<h2>Students ({$studentCount})</h2>";
foreach ($students as $s) {
    echo "Roll No: {$s['roll_no']}, Name: {$s['name']}, Department: {$s['department']}<br>";
}

echo "<h2>Rooms ({$roomCount})</h2>";
foreach ($rooms as $r) {
    echo "Room No: {$r['room_no']}, Name: {$r['room_name']}, Seat Capacity: {$r['seat_capacity']}<br>";
}

echo "<h2>Departments ({$departmentCount})</h2>";
foreach ($departments as $d) {
    echo "Dept ID: {$d['department_id']}, Name: {$d['department_name']}<br>";
}
*/
?>

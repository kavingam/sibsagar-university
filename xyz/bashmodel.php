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

    public function createRoomJSON($roomNo, $roomName, $benchOrder, $seatCapacity) {
        // Trim and validate input
        $roomNo = trim($roomNo);
        $roomName = trim($roomName);

        if (empty($roomNo) || empty($roomName) || empty($benchOrder) || empty($seatCapacity)) {
            return ["success" => false, "message" => "All fields are required!"];
        }

        if (!is_numeric($benchOrder) || !is_numeric($seatCapacity)) {
            return ["success" => false, "message" => "Bench Order and Seat Capacity must be numbers."];
        }

        try {
            // Check if room exists
            $stmt = $this->conn->prepare("SELECT room_name FROM rooms WHERE room_no = :roomNo");
            $stmt->execute([":roomNo" => $roomNo]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return ["success" => false, "message" => "Room No already exists."];
            }

            // Insert the new room
            $stmt = $this->conn->prepare(
                "INSERT INTO rooms (room_no, room_name, bench_order, seat_capacity) 
                VALUES (:roomNo, :roomName, :benchOrder, :seatCapacity)"
            );
            $stmt->execute([
                ":roomNo" => $roomNo,
                ":roomName" => $roomName,
                ":benchOrder" => $benchOrder,
                ":seatCapacity" => $seatCapacity
            ]);

            return ["success" => true, "message" => "Room added successfully!"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Database Error: " . $e->getMessage()];
        }
    }
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
    // Fetch all rooms with JSONS
    public function getAllRoomsJSONS() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM rooms ORDER BY room_no ASC");
            $stmt->execute();
            return ["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
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

    // 🔥 Generate Next Department ID
    private function generateNextId() {
        $stmt = $this->conn->query("SELECT MAX(CAST(department_id AS UNSIGNED)) AS max_id FROM departments");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['max_id'] !== null) ? $row['max_id'] + 1 : 1;
    }
    // 🔍 Check if Department Name Exists
    private function departmentExists($department_name) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM departments WHERE department_name = ?");
        $stmt->execute([$department_name]);
        return $stmt->fetchColumn() > 0;
    }
    // ✅ Create New Department Using JSON
    public function createNewDepartmentJSON($department_name) {
        if ($this->departmentExists($department_name)) {
            return json_encode(["status" => "exists_name", "message" => "Department name already exists."]);
        }

        $next_id = $this->generateNextId();
        $stmt = $this->conn->prepare("INSERT INTO departments (department_id, department_name) VALUES (?, ?)");
        $stmt->execute([$next_id, $department_name]);

        return json_encode(["status" => "success", "message" => "Department created successfully!", "department_id" => $next_id]);
    }
    
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
   // ✅ Update Department Name by ID with JSON Method
    public function updateDepartmentJSON($department_id, $department_name) {
        try {
            $stmt = $this->conn->prepare("UPDATE departments SET department_name = ? WHERE department_id = ?");
            $stmt->execute([$department_name, $department_id]);

            return ["status" => "success", "message" => "Department updated successfully."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    // ✅ Delete Department and Reassign IDs
    public function deleteDepartmentJSON($department_id) {
        try {
            $this->conn->beginTransaction(); // Start transaction

            // 🔥 Delete the department
            $stmt = $this->conn->prepare("DELETE FROM departments WHERE department_id = ?");
            $stmt->execute([$department_id]);

            // 🔥 Create a temporary table to store remaining departments in sorted order
            $this->conn->exec("CREATE TEMPORARY TABLE temp_departments AS SELECT department_name FROM departments ORDER BY CAST(department_id AS UNSIGNED) ASC");

            // 🔥 Clear the original table
            $this->conn->exec("DELETE FROM departments");

            // 🔥 Reinsert data with new sequential IDs
            $new_id = 1;
            $stmt = $this->conn->prepare("INSERT INTO departments (department_id, department_name) VALUES (?, ?)");
            $tempStmt = $this->conn->query("SELECT * FROM temp_departments");

            while ($row = $tempStmt->fetch(PDO::FETCH_ASSOC)) {
                $stmt->execute([$new_id, $row['department_name']]);
                $new_id++;
            }

            // 🔥 Drop the temporary table
            $this->conn->exec("DROP TEMPORARY TABLE temp_departments");

            $this->conn->commit(); // ✅ Commit transaction
            return ["status" => "success", "message" => "Department deleted and IDs reassigned."];
        } catch (PDOException $e) {
            $this->conn->rollBack(); // ❌ Rollback on error
            return ["status" => "error", "message" => $e->getMessage()];
        }
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

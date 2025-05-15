<?php
$databasePath = __DIR__ . '/Database.php';

if (!file_exists($databasePath)) {
    die("Error: Database.php not found at: $databasePath");
}
require_once $databasePath;


class BaseModel {
    protected $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // // 🔥 Fetch all records
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

    // // 🛑 Delete all records
    // public function deleteAll($table) {
    //     $allowedTables = ['student', 'rooms', 'departments'];

    //     if (!in_array($table, $allowedTables)) {
    //         die("Invalid table name!");
    //     }

    //     $sql = "DELETE FROM `$table`";
    //     $stmt = $this->conn->prepare($sql);
    //     return $stmt->execute();
    // }

    // // 🔢 Get total count
    // public function getCount($table) {
    //     $allowedTables = ['student', 'rooms', 'departments'];

    //     if (!in_array($table, $allowedTables)) {
    //         die("Invalid table name!");
    //     }

    //     $sql = "SELECT COUNT(*) AS total FROM `$table`";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return $result['total'];
    // }

    // 🔥 Fetch all records
    // public function getAll($table) {
    //     $allowedTables = ['student', 'rooms', 'departments'];

    //     if (!in_array($table, $allowedTables)) {
    //         die("Invalid table name!");
    //     }

    //     $sql = "SELECT * FROM `$table`";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

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

    // ✅ Execute any query with parameters (This is the missing method)
    public function executeQuery($sql, $params) {
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }    
}

class UserInfo extends BaseModel {

    /**
     * Create a new user in the `users_info` table.
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function createUser($email, $password) {
        // Hash the password (password_hash generates the salt for you)
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

        // SQL to insert a new user record
        $sql = "INSERT INTO users_info (email, password_hash) VALUES (:email, :password_hash)";
        $params = [
            ':email' => $email,
            ':password_hash' => $passwordHash
        ];

        return $this->executeQuery($sql, $params);
    }

    /**
     * Login function to authenticate the user by checking email and password.
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        // Fetch user from the database by email
        $user = $this->getUserByEmail($email);

        if ($user) {
            // Check if the password matches the stored hash
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, authentication successful
                return true;
            }
        }

        // Either user doesn't exist or password is incorrect
        return false;
    }

    /**
     * Get user by email
     * 
     * @param string $email
     * @return array
     */
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users_info WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update user information
     * 
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function updateUser($email, $password) {
        // Hash the new password
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

        $sql = "UPDATE users_info SET password_hash = :password_hash WHERE email = :email";
        $params = [
            ':email' => $email,
            ':password_hash' => $passwordHash
        ];

        return $this->executeQuery($sql, $params);
    }


    /**
     * Update user's password.
     *
     * @param string $email
     * @param string $passwordHash
     * @return bool
     */
    public function updateUserPassword($email, $passwordHash) {
        $sql = "UPDATE users_info SET password_hash = :password_hash WHERE email = :email";
        $params = [
            ':email' => $email,
            ':password_hash' => $passwordHash
        ];

        return $this->executeQuery($sql, $params);
    }


    /**
     * Delete user by email
     * 
     * @param string $email
     * @return bool
     */
    public function deleteUser($email) {
        $sql = "DELETE FROM users_info WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':email' => $email]);
    }

    /**
     * Get user count (for example, for admin dashboard)
     * 
     * @return int
     */
    public function getUserCount() {
        return $this->getCount('users_info');
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
    /**
     * Find students who have the same department, course, and semester.
     * 
     * @param string $department
     * @param string $course
     * @param int $semester
     * @return array
     */
    // public function findSimilarStudents($department, $course, $semester) {
    //     $sql = "SELECT * FROM student WHERE department = :department AND course = :course AND semester = :semester";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute([
    //         ':department' => $department,
    //         ':course' => $course,
    //         ':semester' => $semester
    //     ]);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

//     public function findSimilarStudents($department, $course, $semester) {
//     $sql = "SELECT roll_no, reg_no, name, department, semester, course 
//             FROM student_info
//             WHERE department = :department 
//               AND course = :course 
//               AND semester = :semester";
    
//     $stmt = $this->conn->prepare($sql);
//     $stmt->execute([
//         ':department' => $department,
//         ':course' => $course,
//         ':semester' => $semester
//     ]);
    
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }
public function findSimilarStudents($department, $course, $semester) {
    $sql = "SELECT 
                s.roll_no, 
                s.reg_no, 
                s.name, 
                s.department AS department_id, 
                d.department_name, 
                s.semester, 
                s.course 
            FROM student_info s
            JOIN departments d ON s.department = d.department_id
            WHERE s.department = :department 
              AND s.course = :course 
              AND s.semester = :semester";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':department' => $department,
        ':course' => $course,
        ':semester' => $semester
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    
    public function getAllStudents() {
        return $this->getAll('student_info');
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
        return $this->getCount('student_info');
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
    public function createRoomJSON($room_no, $room_name, $bench_order, $seat_capacity) {
        try {
            $sql = "INSERT INTO rooms (room_no, room_name, bench_order, seat_capacity) 
                    VALUES (:room_no, :room_name, :bench_order, :seat_capacity)";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->execute([
                ':room_no' => $room_no,
                ':room_name' => $room_name,
                ':bench_order' => $bench_order,
                ':seat_capacity' => $seat_capacity
            ]);
    
            return ["success" => true, "message" => "✅ Room added successfully!"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "⚠️ Error: " . $e->getMessage()];
        }
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
        try {
            $sql = "UPDATE rooms SET room_name = :room_name, bench_order = :bench_order, seat_capacity = :seat_capacity WHERE room_no = :room_no";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':room_no' => $room_no,
                ':room_name' => $room_name,
                ':bench_order' => $bench_order,
                ':seat_capacity' => $seat_capacity
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function deleteRoom($room_no) {
        $sql = "DELETE FROM rooms WHERE room_no = :room_no";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':room_no' => $room_no]);
    }
    public function deleteRoomJSON($room_no) {
        try {
            $sql = "DELETE FROM rooms WHERE room_no = :room_no";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':room_no', $room_no, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to delete room.");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
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
        public function getDepartmentName($department_id) {
            $sql = "SELECT department_name FROM departments WHERE department_id = :department_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':department_id' => $department_id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['department_name'] : null;
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

    // public function getDepartmentsByDateTime($date, $time) {
    //     // Assuming you have a table like "exams" that stores department info for specific dates and times.
    //     $sql = "SELECT d.department_id, d.department_name
    //             FROM departments d
    //             JOIN attendance_sheet e ON e.department_id = d.department_id
    //             WHERE e.exam_date = :date AND e.exam_time = :time";
        
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute([':date' => $date, ':time' => $time]);

    //     // Fetch departments
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

}

class AttendanceSheet extends BaseModel {
    public function insertAttendance(
        $date, $time, $roll_no, $name, $department, 
        $semester, $course, $room_no, $room_name, 
        $bench_order, $student_status
    ) {
        $sql = "INSERT INTO attendance_sheet 
            (date, time, roll_no, name, department, semester, course, room_no, room_name, bench_order, student_status)
            VALUES 
            (:date, :time, :roll_no, :name, :department, :semester, :course, :room_no, :room_name, :bench_order, :student_status)";
        
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':date' => $date,
            ':time' => $time,
            ':roll_no' => $roll_no,
            ':name' => $name,
            ':department' => $department,
            ':semester' => $semester,
            ':course' => $course,
            ':room_no' => $room_no,
            ':room_name' => $room_name,
            ':bench_order' => $bench_order,
            ':student_status' => $student_status
        ]);
    }
    public function getAllAttendanceDateTime() {
        $sql = "SELECT DISTINCT date, time FROM attendance_sheet ORDER BY date DESC, time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionLabel($time) {
        $hour = date('H', strtotime($time));
        if ($hour < 12) {
            return 'Morning';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'Afternoon';
        } else {
            return 'Evening';
        }
    }

    public function checkExamTimeConflict($examDate, $examTime24) {
        $dateTimes = $this->getAllAttendanceDateTime();
        $inputTime = date('H:i', strtotime($examTime24));

        foreach ($dateTimes as $dt) {
            $existingDate = $dt['date'];
            $existingTime = date('H:i', strtotime($dt['time']));

            if ($existingDate === $examDate && $existingTime === $inputTime) {
                $session = $this->getSessionLabel($existingTime);
                echo "<div class='alert alert-danger'>Already have examinations on $examDate at $existingTime ($session)</div>";
                exit;
            }
        }

        // Optional: log all existing records for debug
        foreach ($dateTimes as $dt) {
            $session = $this->getSessionLabel($dt['time']);
            echo '<pre>';
            // echo "Date: " . $dt['date'] . " | Time: " . $dt['time'] . " | Session: " . $session . "<br>";
            echo '</pre>';
        }
    }

    // ✅ Get all unique rooms (room_no + room_name)
    public function getAllRooms() {
        $sql = "SELECT DISTINCT room_no, room_name FROM attendance_sheet ORDER BY room_no ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Get room by room number
    public function getRoomByNumber($room_no) {
        $sql = "SELECT DISTINCT room_no, room_name FROM attendance_sheet WHERE room_no = :room_no LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':room_no' => $room_no]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Check if a room exists in attendance records
    public function roomExists($room_no) {
        $sql = "SELECT COUNT(*) FROM attendance_sheet WHERE room_no = :room_no";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':room_no' => $room_no]);
        return $stmt->fetchColumn() > 0;
    }
    public function getAllExamDates() {
        $sql = "SELECT DISTINCT date FROM attendance_sheet ORDER BY date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEventsByDate($date) {
        $sql = "SELECT DISTINCT time FROM attendance_sheet WHERE date = :date ORDER BY time";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRoomsByDateTime($date, $time) {
        $sql = "SELECT DISTINCT room_no, room_name FROM attendance_sheet WHERE date = :date AND time = :time ORDER BY room_no";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':date' => $date, ':time' => $time]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function getStudentsByRoom($date, $time, $room_no) {
    //     $query = "SELECT id, roll_no, name FROM attendance_sheet 
    //               WHERE date = :date AND time = :time AND room_no = :room_no";
    
    //     // Prepare the statement using PDO
    //     $stmt = $this->conn->prepare($query);
    
    //     // Bind the parameters using bindParam
    //     $stmt->bindParam(':date', $date);
    //     $stmt->bindParam(':time', $time);
    //     $stmt->bindParam(':room_no', $room_no);
    
    //     // Execute the query
    //     $stmt->execute();
    
    //     // Fetch all the students
    //     $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //     return $students;
    // }
    
    public function getStudentsByRoom($date, $time, $room_no) {
        // Update the query to order by department and roll number
        $query = "SELECT id, roll_no, name, department FROM attendance_sheet 
                  WHERE date = :date AND time = :time AND room_no = :room_no 
                  ORDER BY department, roll_no";  // Ensure sorting by department and roll_no
        
        // Prepare the statement using PDO
        $stmt = $this->conn->prepare($query);
        
        // Bind the parameters using bindParam
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':room_no', $room_no);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch all the students
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $students;
    }

    public function getDepartmentsByDateTime($date, $time) {
        // SQL query to get distinct departments based on the date and time
        $sql = "SELECT DISTINCT d.department_id, d.department_name
                FROM attendance_sheet a
                JOIN departments d ON a.department = d.department_id
                WHERE a.date = :date AND a.time = :time";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':date' => $date, ':time' => $time]);

        // Fetch departments
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsByExam($date, $time, $department_id = null) {
        // SQL query to get students based on the exam date, time, and optional department filter
        $sql = "SELECT a.roll_no, a.name, a.department, a.semester, a.course, a.room_no, a.room_name,a.student_status
                FROM attendance_sheet a
                WHERE a.date = :date AND a.time = :time";
    
        // If department_id is provided, filter by department
        if ($department_id !== null) {
            $sql .= " AND a.department = :department_id";
        }
    
        // Prepare and execute the query
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':date' => $date, ':time' => $time, ':department_id' => $department_id ?? '']);
    
        // Fetch and return students
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function updateAttendanceStatus($roll_no, $student_status, $date, $time, $course, $semester) {
    //     // SQL query to update the student's attendance status
    //     $sql = "UPDATE attendance_sheet
    //             SET student_status = :student_status
    //             WHERE roll_no = :roll_no AND date = :date AND time = :time AND course = :course AND semester = :semester";
    
    //     // Prepare the statement
    //     $stmt = $this->conn->prepare($sql);
    
    //     // Execute the statement with bound parameters
    //     return $stmt->execute([
    //         ':student_status' => $student_status,
    //         ':roll_no' => $roll_no,
    //         ':date' => $date,
    //         ':time' => $time,
    //         ':course' => $course,
    //         ':semester' => $semester
    //     ]);
    // }
    
    public function updateAttendanceStatus($roll_no, $student_status, $date, $time, $course, $semester) {
        $sql = "UPDATE attendance_sheet
                SET student_status = :student_status
                WHERE roll_no = :roll_no AND date = :date AND time = :time
                  AND course = :course AND semester = :semester";
    
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':student_status' => $student_status,
            ':roll_no' => $roll_no,
            ':date' => $date,
            ':time' => $time,
            ':course' => $course,
            ':semester' => $semester
        ]);
    }
    
    
        
}

// ✅ Usage Example
// $student = new Student();
// $students = $student->getAllStudents();
// $studentCount = $student->getStudentCount();
// // echo $studentCount;
// echo "<h2>Students ({$studentCount})</h2>";
// foreach ($students as $s) {
//     echo "Roll No: {$s['roll_no']}, Name: {$s['name']}, Department: {$s['department']}<br>";
// }
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
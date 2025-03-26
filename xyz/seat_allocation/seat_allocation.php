<?php

class AssignSeatsStudent extends BaseModel {
    private $students;

    public function __construct($students) {
        $this->students = $students;
    }

    // public function findSimilarStudents($department, $semester, $course, $range = 10) {
    public function findSimilarStudents($department, $semester, $course, $range){
    
        // Filter students based on department, semester, and course
        $filtered = array_filter($this->students, function($student) use ($department, $semester, $course) {
            return $student['department'] == $department && 
                   $student['semester'] == $semester && 
                   $student['course'] == $course;
        });

        // Convert filtered result to indexed array
        $filtered = array_values($filtered);

        // Get the specified range
        $selected = array_slice($filtered, 0, $range);
        
        // Get the remainder
        $remainder = array_slice($filtered, $range);

        // Return selected range and remainder
        return [
            'selected' => $selected,
            'remainder' => $remainder
        ];
    }
}
// Create Student object
// $students = new Student($studentsArray);

// // Get specific range (first 5 students) and remainder
// $result = $students->findSimilarStudents(1, 1, 1, 5);

// // Print results
// echo "<h4>Selected Students:</h4>";
// echo "<pre>" . print_r($result['selected'], true) . "</pre>";

// echo "<h4>Remainder Students:</h4>";
// echo "<pre>" . print_r($result['remainder'], true) . "</pre>";

class SeatAllocation extends BaseModel {
    
    // Function to get total students
    public function getTotalStudents($tableData) {
        $totalStudents = 0;
        foreach ($tableData as $row) {
            $totalStudents += $row['totalStudent'];
        }
        return $totalStudents;
    }

    // Function to get total unique departments
    public function getTotalDepartments($tableData) {
        $departments = [];
        foreach ($tableData as $row) {
            $department = $row['department'];
            $departments[$department] = true;
        }
        return count($departments);
    }

    // Function to display the total students and unique departments
    public function displaySummary($tableData) {
        $totalStudents = $this->getTotalStudents($tableData);
        $totalDepartments = $this->getTotalDepartments($tableData);

        echo "<br><strong>Total Students:</strong> " . $totalStudents . "<br>";
        echo "<strong>Total Unique Departments:</strong> " . $totalDepartments . "<br>";
    }
}

?>

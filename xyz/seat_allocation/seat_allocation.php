<?php
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

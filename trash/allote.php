<?php
class Department
{
    private $department;
    private $semester;
    private $course;
    private $totalStudent;
    private $students;

    // Constructor to initialize the department details
    public function __construct($department, $semester, $course, $totalStudent, $students)
    {
        $this->department = $department;
        $this->semester = $semester;
        $this->course = $course;
        $this->totalStudent = $totalStudent;
        $this->students = $students;
    }

    // Getter methods to access department details
    public function getDepartment()
    {
        return $this->department;
    }

    public function getSemester()
    {
        return $this->semester;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getTotalStudent()
    {
        return $this->totalStudent;
    }

    public function getStudents()
    {
        return $this->students;
    }

    // Method to generate the department key
    public function getDeptKey()
    {
        return $this->department . "-" . $this->semester . "-" . $this->course;
    }

    // Method to slice the student data based on the total students in another department
    public function getDeptStudentSlice(Department $secondDept)
    {
        return array_slice($this->students, 0, $secondDept->getTotalStudent());
    }

    // Method to build department information for each student
    public function buildDeptArray($studentSlice = null)
    {
        $students = array_map(function ($student) {
            return [
                "roll_no" => $student["roll_no"],
                "name" => $student["name"],
                "department" => $this->department,
                "semester" => $this->semester,
                "course" => $this->course,
            ];
        }, $studentSlice ?? $this->students);

        return [
            "department" => $this->department,
            "semester" => $this->semester,
            "course" => $this->course,
            "totalStudent" => $this->totalStudent,
            "students" => $students,
        ];
    }
}

class University
{
    private $departments;

    public function __construct($departments)
    {
        // Initialize departments as objects
        foreach ($departments as $dept) {
            $this->departments[] = new Department(
                $dept["department"],
                $dept["semester"],
                $dept["course"],
                $dept["totalStudent"],
                $dept["students"]
            );
        }
    }

    // Method to build the final array for the departments
    public function buildFinalArray()
    {
        $finalArray = [];
        $firstDept = $this->departments[0];
        $secondDept = $this->departments[1];

        // Get the student slice for the first department
        $varBiggestDeptSlice = $firstDept->getDeptStudentSlice($secondDept);

        // Build the final array for the first department
        $finalArray[] = $firstDept->buildDeptArray($varBiggestDeptSlice);

        // Build the final array for the second department
        $finalArray[] = $secondDept->buildDeptArray();

        return $finalArray;
    }
}
?>
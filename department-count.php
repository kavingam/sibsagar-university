<?php
include 'xyz/bashmodel.php';
$deptObj = new Department();
echo count($deptObj->getAllDepartments());

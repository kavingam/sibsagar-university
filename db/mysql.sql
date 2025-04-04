-- create `room version 0.0`
CREATE TABLE `rooms` (
    `room_no` VARCHAR(50) NOT NULL,
    `room_name` VARCHAR(100) NOT NULL,
    `bench_order` INT NOT NULL,
    `seat_capacity` INT(11) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- update `room version 0.1`
CREATE TABLE `rooms` (
    `room_no` VARCHAR(50) NOT NULL PRIMARY KEY,  -- Unique Room ID (Primary Key)
    `room_name` VARCHAR(100) NOT NULL,
    `bench_order` INT NOT NULL,
    `seat_capacity` INT(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


  CREATE TABLE `departments` (
    `department_id` varchar(50) NOT NULL,
    `department_name` varchar(100) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  


  CREATE TABLE `student` (
    `roll_no` varchar(50) NOT NULL,
    `name` varchar(255) NOT NULL,
    `department` int(11) NOT NULL,
    `semester` int(11) NOT NULL,
    `course` int(11) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci


    -- Adding MBA, MCA (Data Science), and LLM
INSERT INTO `departments` (`department_id`, `department_name`)
SELECT IFNULL(MAX(CAST(department_id AS UNSIGNED)), 0) + 1, 'MBA' FROM `departments`;
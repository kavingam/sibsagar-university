DROP TABLE IF EXISTS `student_info`;

CREATE TABLE IF NOT EXISTS `student_info` (
  `roll_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reg_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `department` int NOT NULL,
  `semester` int NOT NULL,
  `course` int NOT NULL,
  PRIMARY KEY (`reg_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

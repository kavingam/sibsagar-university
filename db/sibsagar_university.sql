-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 28, 2025 at 05:42 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sibsagar_university`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` varchar(50) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
('4', 'Electronics'),
('3', 'English'),
('2', 'assamese'),
('1', 'computer science'),
('5', 'History'),
('6', 'Zoology '),
('7', 'MCA'),
('11', 'Life Sciences'),
('9', 'MBA');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `room_no` varchar(50) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `size` enum('small','medium','large') NOT NULL,
  `seat_capacity` int NOT NULL,
  UNIQUE KEY `room_no` (`room_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_no`, `room_name`, `size`, `seat_capacity`) VALUES
('1', 'NS - 9', 'small', 32),
('4', 'Arts - I', 'medium', 45),
('10', 'GU - A', 'large', 98),
('11', 'UG - B', 'large', 87),
('5', 'Arts - II', 'medium', 68);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `roll_no` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `department` int NOT NULL,
  `semester` int NOT NULL,
  `course` int NOT NULL,
  PRIMARY KEY (`roll_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`roll_no`, `name`, `department`, `semester`, `course`) VALUES
('ELE-001', 'AAA', 4, 3, 1),
('ENG-009', 'AII', 3, 1, 1),
('ENG-008', 'AHH', 3, 1, 1),
('ENG-007', 'AGG', 3, 1, 1),
('ENG-006', 'AFF', 3, 1, 1),
('ENG-005', 'AEE', 3, 1, 1),
('ENG-004', 'ADD', 3, 1, 1),
('ENG-003', 'ACC', 3, 1, 1),
('ENG-002', 'ABB', 3, 1, 1),
('ENG-001', 'AAA', 3, 1, 1),
('ASS-009', 'AII', 2, 2, 1),
('ASS-008', 'AHH', 2, 2, 1),
('ASS-007', 'AGG', 2, 2, 1),
('ASS-006', 'AFF', 2, 2, 1),
('ASS-005', 'AEE', 2, 2, 1),
('ASS-004', 'ADD', 2, 2, 1),
('ASS-003', 'ACC', 2, 2, 1),
('ASS-002', 'ABB', 2, 2, 1),
('ASS-001', 'AAA', 2, 2, 1),
('CS-009', 'AII', 1, 1, 1),
('CS-008', 'AHH', 1, 1, 1),
('CS-007', 'AGG', 1, 1, 1),
('CS-006', 'AFF', 1, 1, 1),
('CS-005', 'AEE', 1, 1, 1),
('CS-004', 'ADD', 1, 1, 1),
('CS-003', 'ACC', 1, 1, 1),
('CS-002', 'ABB', 1, 1, 1),
('CS-001', 'AAA', 1, 1, 1),
('ELE-002', 'ABB', 4, 3, 1),
('ELE-003', 'ACC', 4, 3, 1),
('ELE-004', 'ADD', 4, 3, 1),
('ELE-005', 'AEE', 4, 3, 1),
('ELE-006', 'AFF', 4, 3, 1),
('ELE-007', 'AGG', 4, 3, 1),
('ELE-008', 'AHH', 4, 3, 1),
('ELE-009', 'AII', 4, 3, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

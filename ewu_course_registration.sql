-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2026 at 08:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ewu_course_registration`
--
CREATE DATABASE IF NOT EXISTS `ewu_course_registration` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ewu_course_registration`;

-- --------------------------------------------------------

--
-- Table structure for table `advisor`
--

DROP TABLE IF EXISTS `advisor`;
CREATE TABLE `advisor` (
  `AdvisorID` int(11) NOT NULL,
  `FacultyID` int(11) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisor`
--

INSERT INTO `advisor` (`AdvisorID`, `FacultyID`, `StartDate`, `Status`) VALUES
(1, 101, '2025-01-01', 'Active'),
(2, 102, '2025-01-01', 'Active'),
(3, 103, '2026-01-01', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `approval`
--

DROP TABLE IF EXISTS `approval`;
CREATE TABLE `approval` (
  `ApprovalID` int(11) NOT NULL,
  `RegistrationID` int(11) NOT NULL,
  `AdvisorID` int(11) NOT NULL,
  `ApprovalStatus` varchar(20) DEFAULT NULL,
  `ApprovalDate` date DEFAULT NULL,
  `Remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval`
--

INSERT INTO `approval` (`ApprovalID`, `RegistrationID`, `AdvisorID`, `ApprovalStatus`, `ApprovalDate`, `Remarks`) VALUES
(90001, 50001, 1, 'Approved', '2026-08-11', 'Registration approved'),
(90002, 50002, 1, 'Approved', '2026-08-11', 'Registration approved'),
(90003, 50003, 1, 'Approved', '2026-08-11', 'Registration approved'),
(90004, 50004, 1, 'Approved', '2026-08-12', 'Registration approved'),
(90005, 50006, 2, 'Approved', '2026-08-12', 'Registration approved'),
(90006, 50007, 2, 'Pending', NULL, 'Waiting for advisor review');

-- --------------------------------------------------------

--
-- Table structure for table `classroom`
--

DROP TABLE IF EXISTS `classroom`;
CREATE TABLE `classroom` (
  `RoomID` int(11) NOT NULL,
  `RoomNumber` varchar(20) NOT NULL,
  `Building` varchar(50) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `RoomType` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classroom`
--

INSERT INTO `classroom` (`RoomID`, `RoomNumber`, `Building`, `Capacity`, `RoomType`) VALUES
(101, '301', 'A-Block', 40, 'Classroom'),
(102, '302', 'A-Block', 40, 'Classroom'),
(103, '401', 'A-Block', 50, 'Classroom'),
(104, '402', 'A-Block', 50, 'Lab'),
(201, '201', 'B-Block', 35, 'Classroom');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `CourseID` int(11) NOT NULL,
  `CourseCode` varchar(20) NOT NULL,
  `CourseName` varchar(100) NOT NULL,
  `Credit` decimal(3,1) NOT NULL,
  `CourseDescription` text DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`CourseID`, `CourseCode`, `CourseName`, `Credit`, `CourseDescription`, `DepartmentID`) VALUES
(101, 'BBA101', 'Principles of Management', 3.0, 'Introduction to management concepts', 3),
(201, 'EEE201', 'Digital Electronics', 3.0, 'Digital logic and electronic circuits', 2),
(207, 'CSE207', 'Data Structures', 3.0, 'Fundamental data structures and algorithms', 1),
(301, 'EEE301', 'Microprocessors', 3.0, 'Microprocessor architecture and programming', 2),
(302, 'CSE302', 'Database Systems', 3.0, 'Database concepts, ER modeling, SQL and normalization', 1),
(303, 'CSE303', 'Operating Systems', 3.0, 'Operating system concepts and process management', 1),
(305, 'CSE305', 'Computer Networks', 3.0, 'Fundamentals of computer networking', 1),
(306, 'CSE306', 'Software Engineering', 3.0, 'Software development principles and methodologies', 1);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL,
  `OfficeLocation` varchar(100) DEFAULT NULL,
  `OfficePhone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepartmentID`, `DepartmentName`, `OfficeLocation`, `OfficePhone`) VALUES
(1, 'Computer Science and Engineering', 'A-Block, 4th Floor', '09666775577'),
(2, 'Electrical and Electronic Engineering', 'A-Block, 5th Floor', '09666775578'),
(3, 'Business Administration', 'B-Block, 3rd Floor', '09666775579');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE `faculty` (
  `FacultyID` int(11) NOT NULL,
  `FacultyName` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Designation` varchar(50) DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`FacultyID`, `FacultyName`, `Email`, `Phone`, `Designation`, `DepartmentID`) VALUES
(101, 'Dr. Rahman', 'rahman@ewu.edu.bd', '01710000001', 'Professor', 1),
(102, 'Dr. Karim', 'karim@ewu.edu.bd', '01710000002', 'Associate Professor', 1),
(103, 'Dr. Ahmed', 'ahmed@ewu.edu.bd', '01710000003', 'Assistant Professor', 1),
(201, 'Dr. Hasan', 'hasan@ewu.edu.bd', '01710000004', 'Professor', 2),
(301, 'Dr. Chowdhury', 'chowdhury@ewu.edu.bd', '01710000005', 'Professor', 3);

-- --------------------------------------------------------

--
-- Table structure for table `grade`
--

DROP TABLE IF EXISTS `grade`;
CREATE TABLE `grade` (
  `GradeID` int(11) NOT NULL,
  `RegistrationID` int(11) NOT NULL,
  `QuizMark` decimal(5,2) DEFAULT NULL,
  `MidMark` decimal(5,2) DEFAULT NULL,
  `FinalMark` decimal(5,2) DEFAULT NULL,
  `GradeLetter` varchar(3) DEFAULT NULL,
  `GradePoint` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade`
--

INSERT INTO `grade` (`GradeID`, `RegistrationID`, `QuizMark`, `MidMark`, `FinalMark`, `GradeLetter`, `GradePoint`) VALUES
(70001, 50001, 18.00, 24.00, 42.00, 'A-', 3.50),
(70002, 50002, 17.00, 22.00, 40.00, 'B+', 3.25),
(70003, 50003, 19.00, 25.00, 44.00, 'A', 4.00),
(70004, 50004, 16.00, 21.00, 38.00, 'B', 3.00),
(70005, 50005, 18.00, 23.00, 41.00, 'A-', 3.50),
(70006, 50008, 15.00, 20.00, 35.00, 'B-', 2.75);

-- --------------------------------------------------------

--
-- Table structure for table `prerequisite`
--

DROP TABLE IF EXISTS `prerequisite`;
CREATE TABLE `prerequisite` (
  `CourseID` int(11) NOT NULL,
  `PrerequisiteCourseID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prerequisite`
--

INSERT INTO `prerequisite` (`CourseID`, `PrerequisiteCourseID`) VALUES
(301, 201),
(302, 207),
(303, 207),
(305, 207),
(306, 302);

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

DROP TABLE IF EXISTS `registration`;
CREATE TABLE `registration` (
  `RegistrationID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `SectionID` int(11) NOT NULL,
  `RegistrationDate` date DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL,
  `DropDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`RegistrationID`, `StudentID`, `SectionID`, `RegistrationDate`, `Status`, `DropDate`) VALUES
(50001, 1001, 10001, '2026-08-10', 'Registered', NULL),
(50002, 1001, 10002, '2026-08-10', 'Registered', NULL),
(50003, 1001, 10003, '2026-08-10', 'Registered', NULL),
(50004, 1002, 10001, '2026-08-11', 'Registered', NULL),
(50005, 1002, 10002, '2026-08-11', 'Registered', NULL),
(50006, 1003, 10002, '2026-08-11', 'Registered', NULL),
(50007, 1003, 10004, '2026-08-11', 'Registered', NULL),
(50008, 1004, 10001, '2026-08-12', 'Registered', NULL),
(50009, 1004, 10005, '2026-08-12', 'Registered', NULL),
(50010, 1005, 10003, '2026-08-12', 'Registered', NULL),
(50011, 1005, 10004, '2026-08-12', 'Dropped', '2026-08-13');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
CREATE TABLE `section` (
  `SectionID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `FacultyID` int(11) NOT NULL,
  `SemesterID` int(11) NOT NULL,
  `RoomID` int(11) NOT NULL,
  `SectionNumber` varchar(10) DEFAULT NULL,
  `ScheduleDay` varchar(20) DEFAULT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`SectionID`, `CourseID`, `FacultyID`, `SemesterID`, `RoomID`, `SectionNumber`, `ScheduleDay`, `StartTime`, `EndTime`) VALUES
(10001, 207, 101, 3, 101, '1', 'Sun-Tue', '08:30:00', '10:00:00'),
(10002, 302, 102, 3, 102, '1', 'Sun-Tue', '10:10:00', '11:40:00'),
(10003, 303, 103, 3, 103, '1', 'Mon-Wed', '08:30:00', '10:00:00'),
(10004, 305, 101, 3, 104, '1', 'Mon-Wed', '10:10:00', '11:40:00'),
(10005, 306, 102, 3, 101, '1', 'Thu-Sat', '08:30:00', '10:00:00'),
(20001, 201, 201, 3, 201, '1', 'Sun-Tue', '12:00:00', '13:30:00'),
(20002, 301, 201, 3, 201, '1', 'Mon-Wed', '12:00:00', '13:30:00'),
(30001, 101, 301, 3, 201, '1', 'Thu-Sat', '10:10:00', '11:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

DROP TABLE IF EXISTS `semester`;
CREATE TABLE `semester` (
  `SemesterID` int(11) NOT NULL,
  `SemesterName` varchar(30) NOT NULL,
  `Year` int(11) NOT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `RegistrationStart` date DEFAULT NULL,
  `RegistrationEnd` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`SemesterID`, `SemesterName`, `Year`, `StartDate`, `EndDate`, `RegistrationStart`, `RegistrationEnd`) VALUES
(1, 'Spring', 2026, '2026-01-15', '2026-05-15', '2025-12-15', '2026-01-10'),
(2, 'Summer', 2026, '2026-05-20', '2026-08-20', '2026-04-20', '2026-05-15'),
(3, 'Fall', 2026, '2026-09-01', '2026-12-20', '2026-08-01', '2026-08-25');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `StudentID` int(11) NOT NULL,
  `StudentName` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `AdvisorID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentID`, `StudentName`, `Email`, `Phone`, `DOB`, `DepartmentID`, `AdvisorID`) VALUES
(1001, 'Ayesha Rahman', 'ayesha1001@ewu.edu.bd', '01810000001', '2004-05-12', 1, 1),
(1002, 'Nafis Ahmed', 'nafis1002@ewu.edu.bd', '01810000002', '2003-08-21', 1, 1),
(1003, 'Sadia Islam', 'sadia1003@ewu.edu.bd', '01810000003', '2004-02-15', 1, 2),
(1004, 'Tanvir Hasan', 'tanvir1004@ewu.edu.bd', '01810000004', '2003-11-09', 1, 2),
(1005, 'Mim Akter', 'mim1005@ewu.edu.bd', '01810000005', '2004-07-18', 1, 3),
(2001, 'Fahim Khan', 'fahim2001@ewu.edu.bd', '01810000006', '2003-03-25', 2, NULL),
(2002, 'Nusrat Jahan', 'nusrat2002@ewu.edu.bd', '01810000007', '2004-09-30', 2, NULL),
(3001, 'Rafi Hossain', 'rafi3001@ewu.edu.bd', '01810000008', '2003-12-11', 3, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advisor`
--
ALTER TABLE `advisor`
  ADD PRIMARY KEY (`AdvisorID`),
  ADD UNIQUE KEY `FacultyID` (`FacultyID`);

--
-- Indexes for table `approval`
--
ALTER TABLE `approval`
  ADD PRIMARY KEY (`ApprovalID`),
  ADD UNIQUE KEY `RegistrationID` (`RegistrationID`),
  ADD KEY `AdvisorID` (`AdvisorID`);

--
-- Indexes for table `classroom`
--
ALTER TABLE `classroom`
  ADD PRIMARY KEY (`RoomID`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`CourseID`),
  ADD UNIQUE KEY `CourseCode` (`CourseCode`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`FacultyID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`GradeID`),
  ADD UNIQUE KEY `RegistrationID` (`RegistrationID`);

--
-- Indexes for table `prerequisite`
--
ALTER TABLE `prerequisite`
  ADD PRIMARY KEY (`CourseID`,`PrerequisiteCourseID`),
  ADD KEY `PrerequisiteCourseID` (`PrerequisiteCourseID`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`RegistrationID`),
  ADD UNIQUE KEY `StudentID` (`StudentID`,`SectionID`),
  ADD KEY `SectionID` (`SectionID`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`SectionID`),
  ADD KEY `CourseID` (`CourseID`),
  ADD KEY `FacultyID` (`FacultyID`),
  ADD KEY `SemesterID` (`SemesterID`),
  ADD KEY `RoomID` (`RoomID`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`SemesterID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `AdvisorID` (`AdvisorID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advisor`
--
ALTER TABLE `advisor`
  ADD CONSTRAINT `advisor_ibfk_1` FOREIGN KEY (`FacultyID`) REFERENCES `faculty` (`FacultyID`);

--
-- Constraints for table `approval`
--
ALTER TABLE `approval`
  ADD CONSTRAINT `approval_ibfk_1` FOREIGN KEY (`RegistrationID`) REFERENCES `registration` (`RegistrationID`),
  ADD CONSTRAINT `approval_ibfk_2` FOREIGN KEY (`AdvisorID`) REFERENCES `advisor` (`AdvisorID`);

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

--
-- Constraints for table `grade`
--
ALTER TABLE `grade`
  ADD CONSTRAINT `grade_ibfk_1` FOREIGN KEY (`RegistrationID`) REFERENCES `registration` (`RegistrationID`);

--
-- Constraints for table `prerequisite`
--
ALTER TABLE `prerequisite`
  ADD CONSTRAINT `prerequisite_ibfk_1` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`),
  ADD CONSTRAINT `prerequisite_ibfk_2` FOREIGN KEY (`PrerequisiteCourseID`) REFERENCES `course` (`CourseID`);

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`),
  ADD CONSTRAINT `registration_ibfk_2` FOREIGN KEY (`SectionID`) REFERENCES `section` (`SectionID`);

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`),
  ADD CONSTRAINT `section_ibfk_2` FOREIGN KEY (`FacultyID`) REFERENCES `faculty` (`FacultyID`),
  ADD CONSTRAINT `section_ibfk_3` FOREIGN KEY (`SemesterID`) REFERENCES `semester` (`SemesterID`),
  ADD CONSTRAINT `section_ibfk_4` FOREIGN KEY (`RoomID`) REFERENCES `classroom` (`RoomID`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`AdvisorID`) REFERENCES `advisor` (`AdvisorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

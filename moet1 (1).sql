-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2025 at 03:43 PM
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
-- Database: `moet1`
--

-- --------------------------------------------------------

--
-- Table structure for table `additionalclassrooms`
--

CREATE TABLE `additionalclassrooms` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `region` varchar(255) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `cluster` varchar(255) DEFAULT NULL,
  `centre` varchar(255) DEFAULT NULL,
  `current_enrolment` int(11) NOT NULL,
  `require_classrooms` enum('yes','no') NOT NULL,
  `infrastructure_summary` text DEFAULT NULL,
  `requests_made` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `grades` varchar(255) DEFAULT NULL,
  `classroom_counts` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `additionalclassrooms`
--

INSERT INTO `additionalclassrooms` (`id`, `school_id`, `region`, `school_name`, `cluster`, `centre`, `current_enrolment`, `require_classrooms`, `infrastructure_summary`, `requests_made`, `created_at`, `updated_at`, `grades`, `classroom_counts`) VALUES
(1, 51, 'Peka', 'B&B High School', '0', NULL, 45, 'yes', 'bkbs', 'Japan', '2025-08-11 08:22:56', '2025-08-11 08:22:56', NULL, NULL),
(2, 51, 'Peka', 'B&B High School', '0', NULL, 23, 'yes', 'nop', 'Japan', '2025-08-11 08:29:32', '2025-08-11 08:29:32', NULL, NULL),
(3, 51, 'Peka', 'B&B High School', '0', NULL, 23, 'yes', 'nop', 'Japan', '2025-08-11 08:56:39', '2025-08-11 08:56:39', '1, 3, 7', 3),
(4, 51, 'Peka', 'B&B High School', '0', NULL, 234, 'yes', 'rew', 'Japan', '2025-08-11 08:57:27', '2025-08-11 08:57:27', '4,5, 1, ', 2),
(5, 51, 'Peka', 'B&B High School', '0', NULL, 234, 'yes', 'rew', 'Japan', '2025-08-11 09:03:20', '2025-08-11 09:03:20', '4,5, 1, ', 2),
(6, 51, 'Peka', 'B&B High School', '0', NULL, 234, 'yes', 'rew', 'Japan', '2025-08-11 09:03:25', '2025-08-11 09:03:25', '4,5, 1, ', 2),
(7, 51, 'Pekak', 'B&B High School', '0', NULL, 122, 'yes', 'nopk', 'Amerika', '2025-08-11 09:04:19', '2025-08-11 09:04:19', '3, 1, 7', 2),
(8, 51, 'Pekak', 'B&B High School', '0', NULL, 122, 'yes', 'nopk', 'Amerika', '2025-08-11 09:59:02', '2025-08-11 09:59:02', '3, 1, 7', 2),
(9, 51, 'Pekak', 'B&B High School', 'ST SAVIOUS', NULL, 34, 'yes', '455g', 'Amerika', '2025-08-12 09:28:44', '2025-08-12 09:28:44', '1, 1', 2),
(18, 48, 'jkl', 'Likoena Primary School', '', 'Holy-Trinity\r\n', 1, '', '', '', '2025-08-18 08:08:30', '2025-08-20 08:11:02', '', 0),
(19, 49, 'ui', 'mositi', 'ST ROSE', NULL, 10, 'yes', '45', 'America', '2025-08-18 08:09:30', '2025-08-27 07:11:01', '4', 5),
(25, 48, 'Loti', 'Likoena Primary School', NULL, 'Holy-Trinity', 21, 'yes', 'fyu', 'America', '2025-08-20 07:41:46', '2025-08-29 08:52:25', '6&7', 7);

-- --------------------------------------------------------

--
-- Table structure for table `additionaltoilets`
--

CREATE TABLE `additionaltoilets` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `cluster` varchar(255) DEFAULT NULL,
  `centre` varchar(50) NOT NULL,
  `current_enrolment` int(11) DEFAULT NULL,
  `additional_latrines_needed` tinyint(1) DEFAULT NULL,
  `latrine_groups` varchar(255) DEFAULT NULL,
  `number_of_latrines` int(11) DEFAULT NULL,
  `requests_made` text DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `additionaltoilets`
--

INSERT INTO `additionaltoilets` (`id`, `school_id`, `school_name`, `cluster`, `centre`, `current_enrolment`, `additional_latrines_needed`, `latrine_groups`, `number_of_latrines`, `requests_made`, `summary`, `created_at`) VALUES
(1, 51, 'B&B High School', 'ST SAVIOUS', '', 12, 0, '0', 0, '', 'sasadss', '2025-08-12 10:41:58'),
(2, 51, 'B&B High School', 'ST SAVIOUS', '', 12, 0, '0', 0, '', 'sasadss', '2025-08-12 10:42:53'),
(3, 51, 'B&B High School', 'ST SAVIOUS', '', 12, 1, '0', 2, 'World Vision', 'ds', '2025-08-12 11:03:32'),
(4, 51, 'B&B High School', 'ST SAVIOUS', '', 12, 1, '0', 3, 'Other', 'qwert', '2025-08-12 11:04:29'),
(5, 51, 'B&B High School', 'ST SAVIOUS', '', 12, 1, '0', 2, 'American Embassy', 'wereq', '2025-08-12 11:18:11'),
(6, 51, 'B&B High School', 'ST SAVIOUS', '', 12, 1, 'Teachers, Girls', 4, 'Japan', 'rt1', '2025-08-12 11:23:24'),
(7, 49, 'mositi', 'ST ROSE', '', 2, 0, '', 0, '', '', '2025-08-14 10:59:56'),
(8, 49, 'mositi', 'ST ROSE', '', 4, 4, 'girls', 4, 'japan', 'fjdfdgddfdgfdgf', '2025-08-14 11:02:17'),
(9, 48, 'Likoena Primary School', NULL, 'Holy-Trinity', 0, 1, 'Teachers, Boys', 2, 'Japan', 'ndknvkx zknk', '2025-08-20 14:03:38'),
(10, 48, 'Likoena Primary School', NULL, 'Holy-Trinity', 0, 1, 'Teachers, Boys', 3, 'Japan', '4boys', '2025-08-21 08:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `electricity_infrastructure`
--

CREATE TABLE `electricity_infrastructure` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `cluster` varchar(255) DEFAULT NULL,
  `centre` varchar(50) DEFAULT NULL,
  `has_electricity` enum('yes','no') NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `challenges` text DEFAULT NULL,
  `mitigations` text DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `electricity_infrastructure`
--

INSERT INTO `electricity_infrastructure` (`id`, `school_id`, `school_name`, `cluster`, `centre`, `has_electricity`, `source`, `challenges`, `mitigations`, `additional_info`, `created_at`) VALUES
(1, 51, 'B&B High School', 'ST SAVIOUS', '', 'yes', 'grid', 'jm mn ', 'mklm', 'kk', '2025-08-12 10:31:12'),
(2, 48, 'Likoena Primary School', '', 'Holy-Trinity', 'no', '', '', '', '', '2025-08-14 08:21:38'),
(3, 48, 'Likoena Primary School', '', 'Holy-Trinity', 'no', '', '', '', '', '2025-08-14 08:34:46'),
(4, 48, 'Likoena Primary School', '', 'Holy-Trinity', 'no', '', '', '', 'dfgfddf', '2025-08-14 08:35:14'),
(5, 49, 'mositi', 'ST ROSE\r\n', '', 'yes', 'LEC', 'FDDDDFD', 'FDGF', 'DFG', '2025-08-14 08:40:30'),
(6, 48, 'Likoena Primary School', '', 'Holy-Trinity', 'yes', 'battery', 'hjbjj', 'ikhh', 'jk', '2025-08-18 07:00:30');

-- --------------------------------------------------------

--
-- Table structure for table `high_school_enrollment`
--

CREATE TABLE `high_school_enrollment` (
  `id` int(11) NOT NULL,
  `female_reception` int(11) DEFAULT 0,
  `male_reception` int(11) DEFAULT 0,
  `reception_total` int(11) GENERATED ALWAYS AS (`female_reception` + `male_reception`) STORED,
  `grade_8_girls` int(11) DEFAULT 0,
  `grade_8_boys` int(11) DEFAULT 0,
  `grade_8_total` int(11) GENERATED ALWAYS AS (`grade_8_girls` + `grade_8_boys`) STORED,
  `grade_9_girls` int(11) DEFAULT 0,
  `grade_9_boys` int(11) DEFAULT 0,
  `grade_9_total` int(11) GENERATED ALWAYS AS (`grade_9_girls` + `grade_9_boys`) STORED,
  `grade_10_girls` int(11) DEFAULT 0,
  `grade_10_boys` int(11) DEFAULT 0,
  `grade_10_total` int(11) GENERATED ALWAYS AS (`grade_10_girls` + `grade_10_boys`) STORED,
  `grade_11_girls` int(11) DEFAULT 0,
  `grade_11_boys` int(11) DEFAULT 0,
  `grade_11_total` int(11) GENERATED ALWAYS AS (`grade_11_girls` + `grade_11_boys`) STORED,
  `grants_girls` int(11) DEFAULT 0,
  `grants_boys` int(11) DEFAULT 0,
  `grants_total` int(11) GENERATED ALWAYS AS (`grants_girls` + `grants_boys`) STORED,
  `total_students` int(11) GENERATED ALWAYS AS (`grade_8_total` + `grade_9_total` + `grade_10_total` + `grade_11_total`) STORED,
  `entry_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `high_school_enrollment`
--

INSERT INTO `high_school_enrollment` (`id`, `female_reception`, `male_reception`, `grade_8_girls`, `grade_8_boys`, `grade_9_girls`, `grade_9_boys`, `grade_10_girls`, `grade_10_boys`, `grade_11_girls`, `grade_11_boys`, `grants_girls`, `grants_boys`, `entry_date`, `school_id`) VALUES
(1, 2, 1, 1, 1, 16, 1, 0, 12, 13, 2, 12, 14, '2025-08-08 07:19:43', 49),
(2, 5, 5, 3, 5, 5, 45, 4, 3, 1, 3, 34, 4, '2025-08-14 13:39:31', 49),
(6, 1, 2, 18, 1, 5, 22, 1, 11, 10, 11, 10, 11, '2025-08-25 07:38:01', 49),
(8, 2, 1, 12, 11, 23, 11, 23, 12, 13, 12, 13, 2, '2025-09-01 09:11:53', 59);

-- --------------------------------------------------------

--
-- Table structure for table `infrastructure`
--

CREATE TABLE `infrastructure` (
  `infrastructure_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `classrooms` int(11) DEFAULT NULL,
  `toilets` int(11) DEFAULT NULL,
  `kitchen` enum('Yes','No') DEFAULT NULL,
  `store` enum('Yes','No') DEFAULT NULL,
  `staffroom` enum('Yes','No') DEFAULT NULL,
  `office` int(11) DEFAULT NULL,
  `library` int(11) DEFAULT NULL,
  `laboratory` int(11) DEFAULT NULL,
  `hall` int(11) DEFAULT NULL,
  `playgrounds` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `infrastructure`
--

INSERT INTO `infrastructure` (`infrastructure_id`, `school_id`, `classrooms`, `toilets`, `kitchen`, `store`, `staffroom`, `office`, `library`, `laboratory`, `hall`, `playgrounds`, `created_at`) VALUES
(1, 51, 23, 12, 'Yes', '', 'Yes', 1, 1, 1, 1, '', '2025-08-11 10:22:47'),
(2, 51, 23, 12, 'Yes', '', 'Yes', 1, 1, 1, 1, '', '2025-08-12 07:28:31'),
(3, 51, 21, 23, 'Yes', 'Yes', 'Yes', 1, 1, 0, 0, '', '2025-08-13 06:35:57'),
(4, 51, 21, 23, 'Yes', 'Yes', 'Yes', 1, 1, 0, 0, '', '2025-08-13 06:45:44'),
(5, 51, 32, 53, 'Yes', 'Yes', 'Yes', 1, 1, 1, 0, '', '2025-08-13 06:49:26'),
(6, 50, 12, 8, '', '', 'Yes', 1, 0, 1, 1, '', '2025-08-13 13:41:45'),
(7, 49, 4, 3, 'No', 'Yes', 'Yes', 1, 1, 1, 1, 'soccer and netball', '2025-08-14 08:47:57'),
(8, 102, 0, 2, '', '', '', 0, 0, 0, 0, '', '2025-08-25 12:40:02'),
(9, 48, 3, 2, 'Yes', 'Yes', 'Yes', 1, 1, 1, 1, '', '2025-08-25 12:53:01'),
(10, 48, 13, 7, 'Yes', '', 'Yes', 1, 1, 1, 1, 'Valleyball', '2025-08-27 06:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `internet_infrastructure`
--

CREATE TABLE `internet_infrastructure` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `cluster` varchar(50) NOT NULL,
  `has_internet` enum('yes','no') NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `reliable_network` enum('EEC','VCL') DEFAULT NULL,
  `challenges` text DEFAULT NULL,
  `mitigations` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internet_infrastructure`
--

INSERT INTO `internet_infrastructure` (`id`, `school_id`, `cluster`, `has_internet`, `source`, `reliable_network`, `challenges`, `mitigations`, `created_at`) VALUES
(1, 51, '', 'yes', 'xjxkzjx', NULL, 'jzxkjz', 'kzjkz', '2025-08-12 13:02:16'),
(2, 51, '', 'no', '', 'VCL', 'kxkjc', 'kkdk', '2025-08-12 13:07:35'),
(3, 51, 'ST SAVIOUS', 'no', '', 'VCL', 'kxkjc', 'kkdk', '2025-08-12 13:10:55'),
(4, 51, 'ST SAVIOUS', 'no', '', 'VCL', 'kxkjc', 'kkdk', '2025-08-12 13:37:33'),
(5, 48, '', 'no', 'No Source', 'VCL', 'fghg', 'gfg', '2025-08-14 08:39:53'),
(6, 49, 'ST ROSE', 'yes', 'vodacom', 'VCL', 'ghhbh', 'hbhbhbkh', '2025-08-27 07:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `preschool_enrollment`
--

CREATE TABLE `preschool_enrollment` (
  `id` int(11) NOT NULL,
  `female_reception` int(11) DEFAULT 0,
  `male_reception` int(11) NOT NULL,
  `age3_girls` int(11) DEFAULT 0,
  `age3_boys` int(11) DEFAULT 0,
  `age3_total` int(11) GENERATED ALWAYS AS (`age3_girls` + `age3_boys`) STORED,
  `age4_girls` int(11) DEFAULT 0,
  `age4_boys` int(11) DEFAULT 0,
  `age4_total` int(11) GENERATED ALWAYS AS (`age4_girls` + `age4_boys`) STORED,
  `age5_girls` int(11) DEFAULT 0,
  `age5_boys` int(11) DEFAULT 0,
  `age5_total` int(11) GENERATED ALWAYS AS (`age5_girls` + `age5_boys`) STORED,
  `overall_total` int(11) GENERATED ALWAYS AS (`age3_total` + `age4_total` + `age5_total`) STORED,
  `entry_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preschool_enrollment`
--

INSERT INTO `preschool_enrollment` (`id`, `female_reception`, `male_reception`, `age3_girls`, `age3_boys`, `age4_girls`, `age4_boys`, `age5_girls`, `age5_boys`, `entry_date`, `school_id`) VALUES
(12, 2, 2, 2, 2, 2, 2, 2, 2, '2025-08-08 08:33:24', 49),
(16, 3, 3, 4, 2, 2, 4, 5, 6, '2025-08-13 10:52:55', 50),
(17, 1, 3, 23, 12, 12, 13, 4, 2, '2025-08-13 13:39:38', 50);

-- --------------------------------------------------------

--
-- Table structure for table `primary_enrollment`
--

CREATE TABLE `primary_enrollment` (
  `P_id` int(11) NOT NULL,
  `female_reception` int(11) NOT NULL,
  `male_reception` int(11) NOT NULL,
  `reception_total` int(11) GENERATED ALWAYS AS (`female_reception` + `male_reception`) STORED,
  `grade1_girls` int(11) NOT NULL,
  `grade1_boys` int(11) NOT NULL,
  `grade1_total` int(11) GENERATED ALWAYS AS (`grade1_girls` + `grade1_boys`) STORED,
  `grade2_girls` int(11) NOT NULL,
  `grade2_boys` int(11) NOT NULL,
  `grade2_total` int(11) GENERATED ALWAYS AS (`grade2_girls` + `grade2_boys`) STORED,
  `grade3_girls` int(11) NOT NULL,
  `grade3_boys` int(11) NOT NULL,
  `grade3_total` int(11) GENERATED ALWAYS AS (`grade3_girls` + `grade3_boys`) STORED,
  `grade4_girls` int(11) NOT NULL,
  `grade4_boys` int(11) NOT NULL,
  `grade4_total` int(11) GENERATED ALWAYS AS (`grade4_girls` + `grade4_boys`) STORED,
  `grade5_girls` int(11) NOT NULL,
  `grade5_boys` int(11) NOT NULL,
  `grade5_total` int(11) GENERATED ALWAYS AS (`grade5_girls` + `grade5_boys`) STORED,
  `grade6_girls` int(11) NOT NULL,
  `grade6_boys` int(11) NOT NULL,
  `grade6_total` int(11) GENERATED ALWAYS AS (`grade6_girls` + `grade6_boys`) STORED,
  `grade7_girls` int(11) NOT NULL,
  `grade7_boys` int(11) NOT NULL,
  `grade7_total` int(11) GENERATED ALWAYS AS (`grade7_girls` + `grade7_boys`) STORED,
  `repeaters_girls` int(11) NOT NULL,
  `repeaters_boys` int(11) NOT NULL,
  `repeaters_total` int(11) GENERATED ALWAYS AS (`repeaters_girls` + `repeaters_boys`) STORED,
  `overall_total` int(11) GENERATED ALWAYS AS (`reception_total` + `grade1_girls` + `grade1_boys` + `grade2_girls` + `grade2_boys` + `grade3_girls` + `grade3_boys` + `grade4_girls` + `grade4_boys` + `grade5_girls` + `grade5_boys` + `grade6_girls` + `grade6_boys` + `grade7_girls` + `grade7_boys` + `repeaters_girls` + `repeaters_boys`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `primary_enrollment`
--

INSERT INTO `primary_enrollment` (`P_id`, `female_reception`, `male_reception`, `grade1_girls`, `grade1_boys`, `grade2_girls`, `grade2_boys`, `grade3_girls`, `grade3_boys`, `grade4_girls`, `grade4_boys`, `grade5_girls`, `grade5_boys`, `grade6_girls`, `grade6_boys`, `grade7_girls`, `grade7_boys`, `repeaters_girls`, `repeaters_boys`, `created_at`, `updated_at`, `school_id`) VALUES
(12, 3, 4, 5, 5, 6, 7, 7, 5, 4, 5, 4, 5, 4, 6, 6, 8, 5, 4, '2025-08-14 13:10:25', '2025-08-25 10:45:21', 48),
(14, 2, 2, 3, 4, 5, 6, 76, 7, 4, 4, 5, 5, 5, 4, 4, 4, 4, 4, '2025-08-25 09:10:43', '2025-08-25 10:44:13', 48);

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `school_id` int(11) NOT NULL,
  `principal_name` varchar(30) NOT NULL,
  `principal_surname` varchar(30) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `phone_number` int(15) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `constituency` varchar(100) DEFAULT NULL,
  `centre` varchar(100) DEFAULT NULL,
  `cluster` varchar(50) DEFAULT NULL,
  `typeofschool` varchar(50) DEFAULT NULL,
  `school_name` varchar(100) NOT NULL,
  `registration_number` varchar(50) NOT NULL,
  `male_teachers` int(11) NOT NULL,
  `female_teachers` int(11) NOT NULL,
  `total_teachers` int(11) DEFAULT 0,
  `council` varchar(50) NOT NULL,
  `village` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`school_id`, `principal_name`, `principal_surname`, `gender`, `phone_number`, `email_address`, `constituency`, `centre`, `cluster`, `typeofschool`, `school_name`, `registration_number`, `male_teachers`, `female_teachers`, `total_teachers`, `council`, `village`, `user_id`, `created_at`) VALUES
(40, 'hfdffgt', 'vccgcg', '', 93849382, 'thope@gmail.com', 'khanyane', 'Pitseng', '', 'Special&Inclusive', 'Likoena', '1111', 0, 0, 0, 'Hlotse', 'Hlotse', 3, '2025-08-06 13:22:46'),
(48, 'Tau', 'Khalala', 'Male', 2147483647, 'tau.khalala@bothouniversity.com', 'khanyane', 'Holy-Trinity', '', 'Public', 'Likoena Primary School', '22302', 7, 15, 22, 'Hlotse', 'Hlotse', 8, '2025-08-07 07:20:00'),
(49, 'Motseko', 'Mabohla', '', 56391932, 'mt@gmail.com', 'mathokwane', '', 'ST ROSE', 'Public', 'mositi', '22303', 4, 6, 10, 'mathokwane43', 'mositi', 9, '2025-08-07 07:26:48'),
(50, 'TJ', 'Mohlominyane', '', 56424224, 'tj@gmail.com', 'hlotse24', NULL, '', 'Government', 'hlotse', '22301', 5, 7, 12, 'hlotse73', 'America', 7, '2025-08-07 07:32:03'),
(51, 'Mosi', 'Metsi', '', 52787892, 'B@gmail.com', 'khanyane', NULL, 'ST SAVIOUS', 'Public', 'B&B High School', '22304', 7, 12, 19, 'Peka', 'Hlotse', 10, '2025-08-11 07:35:40'),
(52, 'Bale', 'Mpolo', '', 1234333, 'hd@gmail.com', 'tsehlo', NULL, 'ST LUKE', 'Public', 'letsheng High School', '22306', 7, 21, 28, 'Peka', 'Peka', 14, '2025-08-14 07:35:41'),
(58, 'Tanki', 'Khetsi', '', 6333333, 'tanki@gmail.com', 'mahobong', 'ST Luke', NULL, 'RCC', 'St Luka', '0909', 7, 8, 15, 'Mahobong', 'Mahobong', 17, '2025-09-01 09:04:40'),
(59, 'Lesiba', 'Nku', '', 87654323, 'lesiba@gmail.com', 'Khethisa', NULL, 'ST ROSE', 'Gorvenment', 'Likhomong High School', '2323', 4, 12, 16, 'Pitseng', 'Pitseng', 19, '2025-09-01 09:10:33');

--
-- Triggers `schools`
--
DELIMITER $$
CREATE TRIGGER `update_number_of_teachers` BEFORE INSERT ON `schools` FOR EACH ROW BEGIN
    SET NEW.total_teachers = COALESCE(NEW.female_teachers, 0) + COALESCE(NEW.male_teachers, 0);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `RegNo` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','principal') NOT NULL,
  `level` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `RegNo`, `password`, `role`, `level`, `created_at`, `status`) VALUES
(1, '2230469', '$2y$10$9/4mnUYxLJjY4upRmx24JuyLgpVirY9hi9j4/ByymYCewJHNX54Da', 'principal', 'High', '2025-08-01 06:39:21', 0),
(2, '2230468', '$2y$10$sMusPVB15i30iRyBexeO2ud7UdsJt87hAki7BZTTWhE85/B8i2OMm', 'principal', 'High', '2025-08-01 07:02:13', 0),
(3, '1111', '$2y$10$0XV2SOP4qQp.H6LJxQ5YD.STjUACx/CwNkeWt7zh9naL7JfdPYk4m', 'principal', 'Primary', '2025-08-04 07:58:16', 1),
(6, '0000', '$2y$10$IF2xrgE7Jkd.MHrQD30fOOhWDz85RKjL/GoTw.xTkhxSln8/2qQzq', 'principal', 'High', '2025-08-05 12:42:38', 1),
(7, '22301', '$2y$10$w9PkU71j0Jjozh55EhCdr.W768Bxqfm2wcjn25DW9Q9x36kpjopMK', 'principal', 'Pre', '2025-08-07 06:52:29', 1),
(8, '22302', '$2y$10$.nnzM9soHQrJwsbvlqTTTO0sF6hmcqQsFPyCIU.lLgNTUDQFWns1O', 'principal', 'Primary', '2025-08-07 06:53:03', 1),
(9, '22303', '$2y$10$P4IbT34t5FtzxT9T3t3fIO5EC.WzFt9uoXQGpT376t8O.qUWqxGFa', 'principal', 'High', '2025-08-07 06:53:34', 1),
(10, '22304', '$2y$10$pVb/aruKMw0XfJD.wcaa9eW2kSqM4qgxSIbmN9YuK9/fCIOnYq6VK', 'principal', 'High', '2025-08-11 07:33:54', 1),
(12, '22305', '$2y$10$lT7esLu.gyzZIED3riE3L.mUpSX4v9XuySoAOuiMwKWWHnxt3PBtq', 'principal', 'High', '2025-08-14 07:31:24', 0),
(14, '22306', '$2y$10$k1GvIUHCTBOeC4BN9hKUWuZ4VGkP2sCO7cFUIlQMXsnh7/SmRm/HG', 'principal', 'High', '2025-08-14 07:33:12', 1),
(15, '0101', '$2y$10$h50hgSXLu3TA.wsDQs1kmu6WLzP9dMvKROBtZaEavbyPxWfZFsAY.', 'admin', 'Admin', '2025-08-14 09:46:04', 0),
(17, '0909', '$2y$10$pCMO22TKR1927/0cQX7sfeV4ttGcKwKHUjQNSejTdPT3FsmXSpphi', 'principal', 'Primary', '2025-08-29 09:02:46', 1),
(18, '0808', '$2y$10$RmGoIDh2wHNi83egxsp./uMy53f./elH7LYfjRy.Nr.4G1ZgV.Z1S', 'principal', 'High', '2025-08-29 09:23:48', 1),
(19, '2323', '$2y$10$qfmoE2px.WEeH6bOOlVXzul9cb/diq.u5JDsG7OREkPFQfEpf0t9O', 'principal', 'High', '2025-09-01 09:08:38', 0);

-- --------------------------------------------------------

--
-- Table structure for table `utilities`
--

CREATE TABLE `utilities` (
  `utility_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `tap_water` enum('Available','Not Available') DEFAULT NULL,
  `electricity` enum('Available','Not Available') DEFAULT NULL,
  `accessibility` enum('Accessible','Not Accessible') DEFAULT NULL,
  `playgrounds` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_infrastructure`
--

CREATE TABLE `water_infrastructure` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `cluster` varchar(255) NOT NULL,
  `centre` varchar(255) DEFAULT NULL,
  `water_source` enum('yes','no') NOT NULL,
  `water_type` varchar(255) DEFAULT NULL,
  `distance` int(11) DEFAULT NULL,
  `challenges` text DEFAULT NULL,
  `mitigations` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `water_infrastructure`
--

INSERT INTO `water_infrastructure` (`id`, `school_id`, `cluster`, `centre`, `water_source`, `water_type`, `distance`, `challenges`, `mitigations`, `created_at`) VALUES
(1, 51, 'ST SAVIOUS', NULL, 'no', '', 0, '', '', '2025-08-12 13:39:09'),
(2, 51, 'ST SAVIOUS', NULL, 'yes', '', 432, 'hgvvhv', ' f f', '2025-08-12 13:39:36'),
(3, 51, 'ST SAVIOUS', NULL, 'yes', 'Rainwater', 655, 'ewse', 'ds', '2025-08-12 13:40:21'),
(4, 51, 'ST SAVIOUS', NULL, 'yes', 'Rainwater', 655, 'ewse', 'ds', '2025-08-13 06:20:03'),
(5, 51, 'ST SAVIOUS', NULL, 'yes', 'Rainwater', 655, 'ewse', 'ds', '2025-08-13 06:20:31'),
(6, 51, 'ST SAVIOUS', NULL, 'yes', '', 432, 'hgvvhv', ' f f', '2025-08-13 06:21:09'),
(10, 48, '', 'Holy-Trinity', 'yes', 'Well', 3, 'ffff', 'gg', '2025-08-15 09:48:02'),
(11, 49, 'ST ROSE', NULL, 'yes', 'Piped', 2, 'ubu', 'uyuy', '2025-08-15 09:59:26'),
(19, 48, '', 'Holy-Trinity', 'no', '', 0, '', '', '2025-08-20 07:35:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `additionalclassrooms`
--
ALTER TABLE `additionalclassrooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `additionaltoilets`
--
ALTER TABLE `additionaltoilets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_school` (`school_id`);

--
-- Indexes for table `electricity_infrastructure`
--
ALTER TABLE `electricity_infrastructure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `high_school_enrollment`
--
ALTER TABLE `high_school_enrollment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_high_school_enrollment_schools` (`school_id`);

--
-- Indexes for table `infrastructure`
--
ALTER TABLE `infrastructure`
  ADD PRIMARY KEY (`infrastructure_id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `internet_infrastructure`
--
ALTER TABLE `internet_infrastructure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `preschool_enrollment`
--
ALTER TABLE `preschool_enrollment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_preschool_enrollment_schools` (`school_id`);

--
-- Indexes for table `primary_enrollment`
--
ALTER TABLE `primary_enrollment`
  ADD PRIMARY KEY (`P_id`),
  ADD KEY `fk_primary_enrollment_schools` (`school_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`school_id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`RegNo`),
  ADD UNIQUE KEY `RegNo` (`RegNo`);

--
-- Indexes for table `utilities`
--
ALTER TABLE `utilities`
  ADD PRIMARY KEY (`utility_id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `water_infrastructure`
--
ALTER TABLE `water_infrastructure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_id` (`school_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `additionalclassrooms`
--
ALTER TABLE `additionalclassrooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `additionaltoilets`
--
ALTER TABLE `additionaltoilets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `electricity_infrastructure`
--
ALTER TABLE `electricity_infrastructure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `high_school_enrollment`
--
ALTER TABLE `high_school_enrollment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `infrastructure`
--
ALTER TABLE `infrastructure`
  MODIFY `infrastructure_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `internet_infrastructure`
--
ALTER TABLE `internet_infrastructure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `preschool_enrollment`
--
ALTER TABLE `preschool_enrollment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `primary_enrollment`
--
ALTER TABLE `primary_enrollment`
  MODIFY `P_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `school_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `utilities`
--
ALTER TABLE `utilities`
  MODIFY `utility_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `water_infrastructure`
--
ALTER TABLE `water_infrastructure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `additionalclassrooms`
--
ALTER TABLE `additionalclassrooms`
  ADD CONSTRAINT `additionalclassrooms_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE;

--
-- Constraints for table `additionaltoilets`
--
ALTER TABLE `additionaltoilets`
  ADD CONSTRAINT `fk_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `electricity_infrastructure`
--
ALTER TABLE `electricity_infrastructure`
  ADD CONSTRAINT `electricity_infrastructure_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `high_school_enrollment`
--
ALTER TABLE `high_school_enrollment`
  ADD CONSTRAINT `fk_high_school_enrollment_schools` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE;

--
-- Constraints for table `internet_infrastructure`
--
ALTER TABLE `internet_infrastructure`
  ADD CONSTRAINT `internet_infrastructure_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `preschool_enrollment`
--
ALTER TABLE `preschool_enrollment`
  ADD CONSTRAINT `fk_preschool_enrollment_schools` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE;

--
-- Constraints for table `primary_enrollment`
--
ALTER TABLE `primary_enrollment`
  ADD CONSTRAINT `fk_primary_enrollment_schools` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE;

--
-- Constraints for table `schools`
--
ALTER TABLE `schools`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `water_infrastructure`
--
ALTER TABLE `water_infrastructure`
  ADD CONSTRAINT `water_infrastructure_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

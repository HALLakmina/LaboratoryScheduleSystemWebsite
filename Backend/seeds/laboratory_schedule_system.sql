-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2026 at 04:01 PM
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
-- Database: `laboratory_schedule_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `database_modification_logs`
--

CREATE TABLE `database_modification_logs` (
  `log_id` int(11) NOT NULL,
  `action_type` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `id` int(11) NOT NULL,
  `lab_name` varchar(100) NOT NULL,
  `lab_location` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_requests`
--

CREATE TABLE `lecturer_requests` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `subject_id` varchar(200) NOT NULL,
  `lecture_group_id` int(11) NOT NULL,
  `year_id` int(11) NOT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `timetable_time_slot_id` int(11) NOT NULL,
  `timetable_column_heading_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `lecturer_request` longtext DEFAULT NULL,
  `admin_message` varchar(255) DEFAULT NULL,
  `action` enum('requested','confirmed','canceled') NOT NULL DEFAULT 'requested',
  `send_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecture_groups`
--

CREATE TABLE `lecture_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_at` time DEFAULT NULL,
  `end_at` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `practical_subjects`
--

CREATE TABLE `practical_subjects` (
  `id` int(11) NOT NULL,
  `subject_cord` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `year_id` int(11) NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_group_relations`
--

CREATE TABLE `subject_group_relations` (
  `id` int(11) NOT NULL,
  `subject_cord` varchar(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `assigned_by` varchar(50) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_lecture_relations`
--

CREATE TABLE `subject_lecture_relations` (
  `id` int(11) NOT NULL,
  `subject_cord` varchar(20) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `assigned_by` varchar(50) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temporary_timetable`
--

CREATE TABLE `temporary_timetable` (
  `id` int(11) NOT NULL,
  `time_slot_id` int(11) DEFAULT NULL,
  `column_heading_id` int(11) DEFAULT NULL,
  `lecture_group_id` int(11) NOT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `subject_cord` varchar(200) DEFAULT NULL,
  `action` varchar(50) DEFAULT 'pending',
  `lecturer_date` date DEFAULT NULL,
  `created_by` varchar(100) NOT NULL,
  `updated_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `time_slot_id` int(11) DEFAULT NULL,
  `column_heading_id` int(11) DEFAULT NULL,
  `lecture_group_id` int(11) NOT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `subject_cord` varchar(20) DEFAULT NULL,
  `action` enum('active','free','cancel') DEFAULT 'free',
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timetable_cells`
--

CREATE TABLE `timetable_cells` (
  `id` int(11) NOT NULL,
  `time_slot_id` int(11) DEFAULT NULL,
  `column_heading_id` int(11) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timetable_column_headings`
--

CREATE TABLE `timetable_column_headings` (
  `id` int(11) NOT NULL,
  `column_heading_number` int(11) NOT NULL,
  `column_heading` varchar(100) NOT NULL,
  `status` enum('active','deactive') NOT NULL DEFAULT 'active',
  `column_number` int(11) NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timetable_settings`
--

CREATE TABLE `timetable_settings` (
  `id` int(11) NOT NULL,
  `table_cell_count` int(11) NOT NULL,
  `table_row_count` int(11) NOT NULL,
  `table_column_count` int(11) NOT NULL,
  `break_row_number` int(11) NOT NULL,
  `break_cell_ids` varchar(255) NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table setting data for table `timetable_settings`
--

INSERT INTO `timetable_settings` (`id`, `table_cell_count`, `table_row_count`, `table_column_count`, `break_row_number`, `break_cell_ids`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 56, 9, 7, 5, '', 'seed', 'lahirulakmina1999@gmail.com', '2026-03-13 16:34:02', '2026-04-05 12:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `timetable_time_slots`
--

CREATE TABLE `timetable_time_slots` (
  `id` int(11) NOT NULL,
  `time_slot_number` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `initials` varchar(10) DEFAULT NULL,
  `initials_stand_for` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `honorifics` varchar(10) DEFAULT NULL,
  `nic` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','lecturer') NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `years`
--

CREATE TABLE `years` (
  `id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `database_modification_logs`
--
ALTER TABLE `database_modification_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_image_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecturer_requests`
--
ALTER TABLE `lecturer_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_request_lecturer` (`lecturer_id`),
  ADD KEY `fk_request_subject` (`subject_id`),
  ADD KEY `fk_request_year` (`year_id`),
  ADD KEY `fk_request_lab` (`lab_id`),
  ADD KEY `fk_request_timeslot` (`timetable_time_slot_id`),
  ADD KEY `fk_request_day` (`timetable_column_heading_id`),
  ADD KEY `fk_request_lecture_group` (`lecture_group_id`);

--
-- Indexes for table `lecture_groups`
--
ALTER TABLE `lecture_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_news_image` (`image_id`),
  ADD KEY `fk_news_created_by` (`created_by`),
  ADD KEY `fk_news_updated_by` (`updated_by`);

--
-- Indexes for table `practical_subjects`
--
ALTER TABLE `practical_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_cord` (`subject_cord`),
  ADD KEY `year_id` (`year_id`);

--
-- Indexes for table `subject_group_relations`
--
ALTER TABLE `subject_group_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_cord` (`subject_cord`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `subject_lecture_relations`
--
ALTER TABLE `subject_lecture_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_cord` (`subject_cord`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `temporary_timetable`
--
ALTER TABLE `temporary_timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_temp_group` (`lecture_group_id`),
  ADD KEY `fk_temp_lab` (`lab_id`),
  ADD KEY `fk_temp_subject` (`subject_cord`),
  ADD KEY `column_heading_id` (`column_heading_id`),
  ADD KEY `fk_temporary_timetable_time_slot` (`time_slot_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture_group_id` (`lecture_group_id`),
  ADD KEY `subject_cord` (`subject_cord`),
  ADD KEY `fk_timetable_lab` (`lab_id`),
  ADD KEY `time_slot_id` (`time_slot_id`,`column_heading_id`),
  ADD KEY `fk_column_heading_id` (`column_heading_id`);

--
-- Indexes for table `timetable_cells`
--
ALTER TABLE `timetable_cells`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_timetable_cell_time_slot_id` (`time_slot_id`),
  ADD KEY `fk_cell_column_heading` (`column_heading_id`);

--
-- Indexes for table `timetable_column_headings`
--
ALTER TABLE `timetable_column_headings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_column_heading_number` (`column_heading_number`);

--
-- Indexes for table `timetable_settings`
--
ALTER TABLE `timetable_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetable_time_slots`
--
ALTER TABLE `timetable_time_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_time_slot_number` (`time_slot_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nic` (`nic`);

--
-- Indexes for table `years`
--
ALTER TABLE `years`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `database_modification_logs`
--
ALTER TABLE `database_modification_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lecturer_requests`
--
ALTER TABLE `lecturer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lecture_groups`
--
ALTER TABLE `lecture_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `practical_subjects`
--
ALTER TABLE `practical_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `subject_group_relations`
--
ALTER TABLE `subject_group_relations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `subject_lecture_relations`
--
ALTER TABLE `subject_lecture_relations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `temporary_timetable`
--
ALTER TABLE `temporary_timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `timetable_cells`
--
ALTER TABLE `timetable_cells`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `timetable_column_headings`
--
ALTER TABLE `timetable_column_headings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `timetable_settings`
--
ALTER TABLE `timetable_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `timetable_time_slots`
--
ALTER TABLE `timetable_time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `years`
--
ALTER TABLE `years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `database_modification_logs`
--
ALTER TABLE `database_modification_logs`
  ADD CONSTRAINT `database_modification_logs_ibfk_1` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_image_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturer_requests`
--
ALTER TABLE `lecturer_requests`
  ADD CONSTRAINT `fk_request_lab` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_request_day` FOREIGN KEY (`timetable_column_heading_id`) REFERENCES `timetable_column_headings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_request_lecture_group` FOREIGN KEY (`lecture_group_id`) REFERENCES `lecture_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_request_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_request_subject` FOREIGN KEY (`subject_id`) REFERENCES `practical_subjects` (`subject_cord`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_request_timeslot` FOREIGN KEY (`timetable_time_slot_id`) REFERENCES `timetable_time_slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_request_year` FOREIGN KEY (`year_id`) REFERENCES `years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_news_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_news_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `practical_subjects`
--
ALTER TABLE `practical_subjects`
  ADD CONSTRAINT `practical_subjects_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_group_relations`
--
ALTER TABLE `subject_group_relations`
  ADD CONSTRAINT `subject_group_relations_ibfk_1` FOREIGN KEY (`subject_cord`) REFERENCES `practical_subjects` (`subject_cord`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_group_relations_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `lecture_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_lecture_relations`
--
ALTER TABLE `subject_lecture_relations`
  ADD CONSTRAINT `subject_lecture_relations_ibfk_1` FOREIGN KEY (`subject_cord`) REFERENCES `practical_subjects` (`subject_cord`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_lecture_relations_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `temporary_timetable`
--
ALTER TABLE `temporary_timetable`
  ADD CONSTRAINT `fk_temp_group` FOREIGN KEY (`lecture_group_id`) REFERENCES `lecture_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_temp_lab` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_temp_subject` FOREIGN KEY (`subject_cord`) REFERENCES `practical_subjects` (`subject_cord`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_temporary_timetable_column_heading_id` FOREIGN KEY (`column_heading_id`) REFERENCES `timetable_column_headings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_temporary_timetable_time_slot` FOREIGN KEY (`time_slot_id`) REFERENCES `timetable_time_slots` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `fk_column_heading_id` FOREIGN KEY (`column_heading_id`) REFERENCES `timetable_column_headings` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_time_slot_id` FOREIGN KEY (`column_heading_id`) REFERENCES `timetable_time_slots` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_timetable_lab` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`lecture_group_id`) REFERENCES `lecture_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_3` FOREIGN KEY (`subject_cord`) REFERENCES `practical_subjects` (`subject_cord`) ON DELETE SET NULL;

--
-- Constraints for table `timetable_cells`
--
ALTER TABLE `timetable_cells`
  ADD CONSTRAINT `fk_cell_column_heading` FOREIGN KEY (`column_heading_id`) REFERENCES `timetable_column_headings` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_timetablecell_timeslot` FOREIGN KEY (`time_slot_id`) REFERENCES `timetable_time_slots` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

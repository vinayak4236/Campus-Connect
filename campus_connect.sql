-- phpMyAdmin SQL Dump
-- Campus Connect Database
-- version 5.2.0
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `campus_connect`
--
CREATE DATABASE IF NOT EXISTS `campus_connect` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `campus_connect`;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `category_class` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `content` text NOT NULL,
  `author` varchar(100) NOT NULL,
  `priority` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `category`, `category_class`, `date`, `content`, `author`, `priority`, `created_at`) VALUES
(1, 'Campus Closure Due to Weather Conditions', 'Important', 'danger', '2023-05-10', 'Due to severe weather conditions forecasted for tomorrow, the campus will remain closed. All classes, exams, and events are postponed. Students are advised to stay indoors and follow safety guidelines. Updates will be provided through the campus portal and email.', 'Campus Administration', 'high', current_timestamp()),
(2, 'Summer Registration Open', 'Academic', 'success', '2023-05-08', 'Registration for summer courses is now open. Please check the academic portal for available courses. Early registration is encouraged as popular courses fill up quickly. The deadline for registration is May 25, 2023. For any queries, contact the registrar\'s office.', 'Registrar\'s Office', 'medium', current_timestamp()),
(3, 'Library Hours Extended', 'General', 'info', '2023-05-04', 'The campus library will extend its hours during finals week. New hours: 7 AM - 2 AM. Additional study spaces will be available, and the quiet zones will be strictly enforced. Librarians will be available for extended hours to assist with research and reference questions.', 'Library Services', 'medium', current_timestamp()),
(4, 'New Campus WiFi Network', 'General', 'info', '2023-05-02', 'A new WiFi network \'Campus-Secure\' has been deployed across the campus. Students and faculty are encouraged to switch to this new network for better security and speed. The old network will be phased out by the end of the month.', 'IT Department', 'medium', current_timestamp()),
(5, 'Annual Sports Meet Postponed', 'Event', 'warning', '2023-04-28', 'The Annual Sports Meet scheduled for May 15-20 has been postponed to June 10-15 due to renovation work at the sports complex. All registered participants will automatically be registered for the new dates. For any concerns, please contact the Sports Department.', 'Sports Committee', 'medium', current_timestamp()),
(6, 'Scholarship Applications Due', 'Important', 'danger', '2023-04-25', 'The deadline for submitting scholarship applications for the next academic year is May 15, 2023. All required documents must be uploaded to the student portal by 11:59 PM on the due date. Late applications will not be considered.', 'Financial Aid Office', 'high', current_timestamp());

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

DROP TABLE IF EXISTS `clubs`;
CREATE TABLE `clubs` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `category_class` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `meeting_days` varchar(100) NOT NULL,
  `meeting_time` varchar(50) NOT NULL,
  `meeting_location` varchar(255) NOT NULL,
  `members` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `category`, `category_class`, `description`, `meeting_days`, `meeting_time`, `meeting_location`, `members`, `image`, `created_at`) VALUES
('robotics', 'Robotics Club', 'Technology', 'warning', 'Design, build, and program robots for competitions and exhibitions. Learn about mechanical engineering, electronics, and programming in a hands-on environment.', 'Every Tuesday and Thursday', '4:00 PM - 6:00 PM', 'Engineering Building, Room 305', 45, 'IMG/poster for Robotics Club.png', current_timestamp()),
('debate', 'Debate Club', 'Academic', 'primary', 'Enhance your public speaking and critical thinking skills through debates on various topics. Participate in inter-college debate competitions and host debate events.', 'Every Monday', '5:00 PM - 7:00 PM', 'Liberal Arts Building, Room 201', 32, 'IMG/981da523-6566-4b29-a928-b4abcd322892 (3).jpg', current_timestamp()),
('photography', 'Photography Club', 'Cultural', 'info', 'Explore the art of photography through workshops, photo walks, and exhibitions. Learn about composition, lighting, editing, and various photography techniques.', 'Every Wednesday', '3:30 PM - 5:30 PM', 'Arts Building, Room 102', 38, 'IMG/981da523-6566-4b29-a928-b4abcd322892 (2).jpg', current_timestamp()),
('sports', 'Sports Club', 'Sports', 'danger', 'Participate in various sports activities including basketball, football, volleyball, and more. Join teams for inter-college tournaments and friendly matches.', 'Every Friday', '4:00 PM - 6:00 PM', 'Sports Complex', 60, 'IMG/981da523-6566-4b29-a928-b4abcd322892 (4).jpg', current_timestamp()),
('social', 'Social Service Club', 'Social Service', 'success', 'Engage in community service activities such as teaching underprivileged children, organizing blood donation camps, and environmental awareness campaigns.', 'Every Saturday', '10:00 AM - 12:00 PM', 'Student Center, Room 105', 42, 'IMG/981da523-6566-4b29-a928-b4abcd322892 (1).jpg', current_timestamp());

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `category_class` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_class` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `date` varchar(50) NOT NULL,
  `time` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `organizer` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `registration_deadline` varchar(50) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_phone` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `category`, `category_class`, `status`, `status_class`, `description`, `date`, `time`, `location`, `organizer`, `image`, `registration_deadline`, `available_seats`, `contact_email`, `contact_phone`, `created_at`) VALUES
(1, 'Annual Music Festival', 'Cultural', 'info', 'Registration Open', 'success', 'Join us for a night of amazing performances by talented students and guest artists. The Annual Music Festival is one of the most anticipated events of the year, featuring performances across various genres including classical, rock, pop, and jazz. This year\'s festival will also feature special guest performances by renowned artists from the music industry.', 'May 15, 2023', '6:00 PM - 10:00 PM', 'Campus Auditorium', 'Student Cultural Committee', 'IMG/poster for an annual music festival.png', 'May 10, 2023', 150, 'music@campusconnect.edu', '(123) 456-7890', current_timestamp()),
(2, 'Tech Hackathon 2023', 'Technology', 'warning', 'Registration Open', 'success', 'A 48-hour coding competition to solve real-world problems with innovative solutions. The Tech Hackathon brings together students from various disciplines to collaborate and create technological solutions for challenges faced by our community. Participants will have access to mentors from leading tech companies and resources to bring their ideas to life.', 'June 10-12, 2023', 'Starts at 9:00 AM on June 10', 'Tech Building', 'Computer Science Department', 'IMG/poster for Tech Hackathon 2023.png', 'June 5, 2023', 100, 'hackathon@campusconnect.edu', '(123) 456-7891', current_timestamp()),
(3, 'Career Development Seminar', 'Career', 'success', 'Registration Open', 'success', 'Learn from industry experts about career opportunities and professional development. This seminar will feature speakers from various industries who will share insights on career paths, job market trends, and skills required for success in today\'s competitive environment. There will also be networking opportunities with potential employers and resume review sessions.', 'May 25, 2023', '10:00 AM - 4:00 PM', 'Business School', 'Career Services Center', 'IMG/poster for Career Development Seminar.png', 'May 20, 2023', 200, 'careers@campusconnect.edu', '(123) 456-7892', current_timestamp()),
(4, 'Inter-College Sports Meet', 'Sports', 'danger', 'Registration Open', 'success', 'Annual sports competition featuring various games and athletic events between colleges. The Inter-College Sports Meet is a three-day event that brings together athletes from various colleges to compete in sports like basketball, football, volleyball, athletics, swimming, and more. Come support your college teams and witness exciting competitions.', 'May 20-22, 2023', '8:00 AM - 6:00 PM daily', 'Sports Complex', 'Sports Department', 'IMG/poster for Inter-College Sports Meet.png', 'May 15, 2023', 500, 'sports@campusconnect.edu', '(123) 456-7893', current_timestamp()),
(5, 'Research Methodology Workshop', 'Academic', 'primary', 'Registration Open', 'success', 'Learn advanced research methodologies and tools for academic research projects. This workshop is designed for graduate students and faculty members interested in enhancing their research skills. Topics covered include research design, data collection methods, statistical analysis, qualitative research techniques, and research ethics. Participants will also get hands-on experience with research tools and software.', 'June 5, 2023', '9:00 AM - 5:00 PM', 'Science Building', 'Research Department', 'IMG/poster for Research Methodology Workshop.png', 'June 1, 2023', 80, 'research@campusconnect.edu', '(123) 456-7894', current_timestamp()),
(6, 'Annual Dance Competition', 'Cultural', 'info', 'Registration Open', 'success', 'Showcase your dancing talent in various styles including contemporary, classical, and folk. The Annual Dance Competition is open to all students who are passionate about dance. Participants can compete in solo, duet, or group categories across different dance styles. The event will be judged by professional dancers and choreographers, with exciting prizes for the winners.', 'June 15, 2023', '5:00 PM - 9:00 PM', 'Cultural Center', 'Dance Club', 'IMG/poster for Annual Dance Competition (1).png', 'June 10, 2023', 120, 'dance@campusconnect.edu', '(123) 456-7895', current_timestamp());

-- --------------------------------------------------------

--
-- Table structure for table `event_related`
--

DROP TABLE IF EXISTS `event_related`;
CREATE TABLE `event_related` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `related_event_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_related`
--

INSERT INTO `event_related` (`id`, `event_id`, `related_event_id`) VALUES
(1, 1, 6),
(2, 1, 3),
(3, 2, 5),
(4, 2, 4),
(5, 3, 5),
(6, 3, 1),
(7, 4, 2),
(8, 4, 6),
(9, 5, 3),
(10, 5, 2),
(11, 6, 1),
(12, 6, 4);

-- --------------------------------------------------------

--
-- Table structure for table `event_schedule`
--

DROP TABLE IF EXISTS `event_schedule`;
CREATE TABLE `event_schedule` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `time` varchar(100) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_schedule`
--

INSERT INTO `event_schedule` (`id`, `event_id`, `time`, `activity`, `location`) VALUES
(1, 1, '6:00 PM - 6:30 PM', 'Opening Ceremony', 'Main Stage'),
(2, 1, '6:30 PM - 7:30 PM', 'Classical Music Performances', 'Main Stage'),
(3, 1, '7:30 PM - 8:30 PM', 'Band Performances', 'Main Stage'),
(4, 1, '8:30 PM - 9:30 PM', 'Guest Artist Performance', 'Main Stage'),
(5, 1, '9:30 PM - 10:00 PM', 'Closing Ceremony & Awards', 'Main Stage'),
(6, 2, 'June 10, 9:00 AM - 10:00 AM', 'Registration & Team Formation', 'Tech Building Lobby'),
(7, 2, 'June 10, 10:00 AM - 11:00 AM', 'Opening Ceremony & Problem Statements', 'Lecture Hall 1'),
(8, 2, 'June 10, 11:00 AM - June 12, 9:00 AM', 'Hackathon in Progress', 'Tech Building Labs'),
(9, 2, 'June 12, 9:00 AM - 12:00 PM', 'Project Presentations', 'Lecture Hall 1'),
(10, 2, 'June 12, 2:00 PM - 3:00 PM', 'Awards Ceremony', 'Lecture Hall 1'),
(11, 3, '10:00 AM - 10:30 AM', 'Welcome & Introduction', 'Business School Auditorium'),
(12, 3, '10:30 AM - 12:00 PM', 'Industry Panel Discussion', 'Business School Auditorium'),
(13, 3, '12:00 PM - 1:00 PM', 'Lunch Break & Networking', 'Business School Lobby'),
(14, 3, '1:00 PM - 2:30 PM', 'Workshop: Resume Building & Interview Skills', 'Seminar Rooms'),
(15, 3, '2:30 PM - 4:00 PM', 'Career Fair & Networking', 'Business School Lobby'),
(16, 4, 'May 20, 8:00 AM - 9:00 AM', 'Opening Ceremony', 'Main Stadium'),
(17, 4, 'May 20, 9:00 AM - 6:00 PM', 'Preliminary Rounds', 'Various Venues'),
(18, 4, 'May 21, 8:00 AM - 6:00 PM', 'Quarter & Semi Finals', 'Various Venues'),
(19, 4, 'May 22, 8:00 AM - 4:00 PM', 'Finals', 'Various Venues'),
(20, 4, 'May 22, 4:00 PM - 6:00 PM', 'Closing Ceremony & Prize Distribution', 'Main Stadium'),
(21, 5, '9:00 AM - 9:30 AM', 'Registration & Welcome', 'Science Building, Room 101'),
(22, 5, '9:30 AM - 11:00 AM', 'Session 1: Research Design & Methodology', 'Science Building, Room 101'),
(23, 5, '11:00 AM - 12:30 PM', 'Session 2: Data Collection Methods', 'Science Building, Room 101'),
(24, 5, '12:30 PM - 1:30 PM', 'Lunch Break', 'Science Building Cafeteria'),
(25, 5, '1:30 PM - 3:00 PM', 'Session 3: Data Analysis Techniques', 'Computer Lab'),
(26, 5, '3:00 PM - 4:30 PM', 'Session 4: Research Ethics & Publication', 'Science Building, Room 101'),
(27, 5, '4:30 PM - 5:00 PM', 'Q&A and Closing', 'Science Building, Room 101'),
(28, 6, '5:00 PM - 5:30 PM', 'Opening Ceremony', 'Cultural Center Auditorium'),
(29, 6, '5:30 PM - 6:30 PM', 'Solo Performances', 'Cultural Center Auditorium'),
(30, 6, '6:30 PM - 7:30 PM', 'Duet Performances', 'Cultural Center Auditorium'),
(31, 6, '7:30 PM - 8:30 PM', 'Group Performances', 'Cultural Center Auditorium'),
(32, 6, '8:30 PM - 9:00 PM', 'Results & Prize Distribution', 'Cultural Center Auditorium');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$8WxmVHxS.AXLgvT1VpJPn.YUvgfBm.R7QzkYIQTmOUV9H6mGh0jMu', 'admin@campusconnect.edu', current_timestamp());

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_related`
--
ALTER TABLE `event_related`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `related_event_id` (`related_event_id`);

--
-- Indexes for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_related`
--
ALTER TABLE `event_related`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `event_schedule`
--
ALTER TABLE `event_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_related`
--
ALTER TABLE `event_related`
  ADD CONSTRAINT `event_related_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_related_ibfk_2` FOREIGN KEY (`related_event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD CONSTRAINT `event_schedule_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
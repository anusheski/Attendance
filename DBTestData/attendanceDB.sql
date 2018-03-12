-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 12. mar 2018 ob 17.55
-- Različica strežnika: 5.5.57-0ubuntu0.14.04.1
-- Različica PHP: 5.5.9-1ubuntu4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Zbirka podatkov: `attendanceDB`
--

-- --------------------------------------------------------

--
-- Struktura tabele `attended`
--

CREATE TABLE IF NOT EXISTS `attended` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `device_id` varchar(25) NOT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Odloži podatke za tabelo `attended`
--

INSERT INTO `attended` (`entry_id`, `user_id`, `lesson_id`, `device_id`) VALUES
(1, 2, 1, ''),
(2, 2, 2, ''),
(3, 2, 4, ''),
(4, 2, 8, ''),
(5, 2, 15, ''),
(6, 2, 16, ''),
(7, 2, 17, ''),
(8, 3, 5, ''),
(9, 3, 18, ''),
(10, 3, 19, ''),
(11, 2, 13, ''),
(12, 2, 12, ''),
(15, 2, 14, ''),
(16, 2, 36, ''),
(20, 2, 10, ''),
(22, 2, 11, ''),
(26, 2, 25, ''),
(27, 2, 47, ''),
(28, 2, 48, ''),
(29, 2, 49, ''),
(30, 2, 50, ''),
(31, 11, 50, ''),
(32, 11, 51, ''),
(33, 11, 20, ''),
(34, 11, 49, ''),
(35, 2, 54, ''),
(36, 2, 58, ''),
(37, 2, 59, ''),
(38, 2, 60, ''),
(45, 2, 61, 'a2pjmVzKYKtSE8x'),
(46, 2, 62, 'a2pjmVzKYKtSE8x'),
(47, 2, 63, 'a2pjmVzKYKtSE8x'),
(48, 2, 64, 'a2pjmVzKYKtSE8x'),
(49, 2, 65, 'a2pjmVzKYKtSE8x'),
(50, 2, 67, 'a2pjmVzKYKtSE8x');

-- --------------------------------------------------------

--
-- Struktura tabele `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `course_id` int(6) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(50) NOT NULL,
  `prof_id` int(6) NOT NULL,
  `required_attendance` int(3) NOT NULL,
  PRIMARY KEY (`course_id`,`prof_id`),
  UNIQUE KEY `course_name` (`course_name`),
  UNIQUE KEY `course_id` (`course_id`),
  KEY `prof_id` (`prof_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Odloži podatke za tabelo `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `prof_id`, `required_attendance`) VALUES
(4, 'Course One', 1, 0),
(5, 'Course Two', 1, 0),
(6, 'Course Three', 1, 0),
(8, 'Course Four', 1, 0),
(9, 'Course Five (Prof2)', 6, 0),
(10, 'Course Seven', 1, 0),
(14, 'Course Six (Prof 2)', 6, 0),
(15, 'Course New', 1, 0),
(16, 'Course 70', 1, 70),
(18, 'NewTestCourse', 1, 70),
(19, 'Demo Course', 1, 70),
(20, 'Demo Course 2', 1, 70);

-- --------------------------------------------------------

--
-- Struktura tabele `goesTo`
--

CREATE TABLE IF NOT EXISTS `goesTo` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Odloži podatke za tabelo `goesTo`
--

INSERT INTO `goesTo` (`entry_id`, `user_id`, `course_id`) VALUES
(1, 2, 4),
(2, 2, 6),
(3, 3, 5),
(4, 3, 6),
(5, 3, 8),
(6, 2, 5),
(7, 2, 9),
(16, 2, 16),
(17, 11, 16),
(18, 11, 4),
(19, 11, 5),
(20, 2, 18),
(21, 2, 19),
(22, 2, 20);

-- --------------------------------------------------------

--
-- Struktura tabele `lessons`
--

CREATE TABLE IF NOT EXISTS `lessons` (
  `lesson_id` int(6) NOT NULL AUTO_INCREMENT,
  `course_id` int(6) NOT NULL,
  `lesson_timestamp` datetime NOT NULL,
  `unique_code` varchar(200) NOT NULL,
  `lesson_title` varchar(50) DEFAULT NULL,
  `lesson_description` varchar(300) DEFAULT NULL,
  `latitude` float(16,12) NOT NULL,
  `longitude` float(16,12) NOT NULL,
  PRIMARY KEY (`lesson_id`),
  UNIQUE KEY `unique_code` (`unique_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Odloži podatke za tabelo `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `course_id`, `lesson_timestamp`, `unique_code`, `lesson_title`, `lesson_description`, `latitude`, `longitude`) VALUES
(1, 4, '0000-00-00 00:00:00', '123a', NULL, NULL, 0.000000000000, 0.000000000000),
(2, 4, '0000-00-00 00:00:00', 'c', NULL, NULL, 0.000000000000, 0.000000000000),
(3, 4, '0000-00-00 00:00:00', 'cc', NULL, NULL, 0.000000000000, 0.000000000000),
(4, 4, '0000-00-00 00:00:00', 'ccc', NULL, NULL, 0.000000000000, 0.000000000000),
(5, 5, '0000-00-00 00:00:00', 'aa', NULL, NULL, 0.000000000000, 0.000000000000),
(6, 4, '0000-00-00 00:00:00', 'aaa', NULL, NULL, 0.000000000000, 0.000000000000),
(7, 4, '2017-11-01 02:11:45', '123', 'test', '', 0.000000000000, 0.000000000000),
(8, 4, '2017-11-01 02:11:44', '124', 'testTitle', '', 0.000000000000, 0.000000000000),
(9, 4, '2017-11-01 02:11:24', 'aab', 'testTitle2', 'testDesc2', 0.000000000000, 0.000000000000),
(10, 4, '2017-11-01 02:11:25', 'VJe6uj', 'a', 'b', 0.000000000000, 0.000000000000),
(11, 4, '2017-11-01 07:11:57', 'SzOJQ0', 'aa', 'bbb', 0.000000000000, 0.000000000000),
(12, 4, '2017-11-01 07:11:28', 'fMiWvn', 'a', 'bb', 0.000000000000, 0.000000000000),
(13, 4, '2017-11-01 07:11:20', 'o2kXYm', 'test', 'aa', 0.000000000000, 0.000000000000),
(14, 4, '2017-11-01 07:11:14', 'wXTqxv', 'a', 'bbbbb', 0.000000000000, 0.000000000000),
(15, 4, '2017-11-01 07:11:58', 'DlFAV2', 'new', 'test', 0.000000000000, 0.000000000000),
(16, 4, '2017-11-01 10:11:56', '0yBbUQ', 'NewLesson', 'QrTest', 0.000000000000, 0.000000000000),
(17, 4, '2017-11-01 10:11:48', 'xK1hFz', 'LastTest', 'Tonight', 0.000000000000, 0.000000000000),
(18, 5, '2017-11-02 12:11:38', '1E8HgX', 'secondTwo', 'desc', 0.000000000000, 0.000000000000),
(19, 5, '2017-11-02 12:11:53', 'i3b7g4', 'thirdTwo', 'desc', 0.000000000000, 0.000000000000),
(20, 5, '2017-11-03 12:11:09', 'L1JsuA', 'testLesson', 'testDescription', 0.000000000000, 0.000000000000),
(21, 6, '2017-11-03 12:11:36', 'B4EErg', 'firstLesson', 'Course three', 0.000000000000, 0.000000000000),
(22, 4, '2017-11-03 12:11:17', '7P9lqK', '', '', 0.000000000000, 0.000000000000),
(23, 4, '2017-11-03 03:11:55', '5Znihj', 'newTitle', 'newDescription', 0.000000000000, 0.000000000000),
(24, 0, '2017-11-03 03:11:30', '13VI8n', 'a', 'b', 0.000000000000, 0.000000000000),
(25, 4, '2017-11-03 03:11:47', 'MAUzwF', 'a', 'b', 0.000000000000, 0.000000000000),
(26, 0, '2017-11-04 02:11:33', 'BDkMEB', 'FirstLe', '', 0.000000000000, 0.000000000000),
(27, 9, '2017-11-04 02:11:44', '3yYAL0', 'FirstLesson', 'CourseFive', 0.000000000000, 0.000000000000),
(45, 4, '2017-12-01 03:12:19', 'nSuvRN', 'locationCheck', 'LessonTest', 54.903923034668, 23.957784652710),
(46, 4, '2017-12-01 03:12:47', 'aTl7lI', 'Aaaa', 'Location fail', 46.064674377441, 14.821682929993),
(47, 4, '2017-12-01 04:12:37', 'OwBjCY', 'newLesson', 'Location ok', 54.903923034668, 23.957784652710),
(48, 4, '2017-12-01 06:12:53', 'JmPsxj', 'aaa', 'bbb', 54.903923034668, 23.957784652710),
(49, 4, '2017-12-02 11:12:59', 'xdc9FN', 'NovLesson', 'BetkaBetka', 54.903923034668, 23.957784652710),
(50, 16, '2017-12-03 06:12:45', 'Bu8Ois', 'Check', 'Percent', 54.903923034668, 23.957784652710),
(51, 16, '2017-12-03 09:12:27', '7kfHY7', 'new lesson', 'to test', 54.903923034668, 23.957784652710),
(52, 16, '2017-12-04 09:12:08', 'tWPVbG', 'aa', 'bb', 54.903923034668, 23.957784652710),
(53, 16, '2017-12-04 09:12:24', 'pwPsH8', 'bb', 'cc', 54.903923034668, 23.957784652710),
(58, 18, '2017-12-12 11:12:40', '0gX7Wv', 'FirstLesson', 'firstDecription', 54.903923034668, 23.957784652710),
(59, 19, '2017-12-12 11:12:20', 'Z2FNOE', 'DemoTitle', 'Desc', 54.903923034668, 23.957784652710),
(60, 19, '2017-12-12 11:12:14', '7w0V4J', 'aaa', 'bbb', 46.064674377441, 14.821682929993),
(61, 19, '2017-12-12 04:12:25', 'bDP1HG', 'aa', 'bb', 54.903923034668, 23.957784652710),
(62, 4, '2017-12-12 04:12:10', 'RA5fvu', 'aa', 'bb', 54.903923034668, 23.957784652710),
(63, 19, '2017-12-12 04:12:21', '8RkAuC', 'aa', 'bbbbb', 54.903923034668, 23.957784652710),
(64, 20, '2017-12-14 02:12:27', 'Kf3fhK', 'Lesson 1', 'Desc 1', 54.903923034668, 23.957784652710),
(65, 19, '2017-12-14 02:12:02', '5eijEb', 'newLesson', 'aaaa', 54.903923034668, 23.957784652710),
(66, 19, '2017-12-14 02:12:27', 'Z5DQ0F', 'WrongLocation', 'aaaa', 46.064674377441, 14.821682929993),
(67, 4, '2017-12-18 03:12:23', 'MQf6Xc', 'ThakkoTest', 'aaaa', 54.903923034668, 23.957784652710),
(68, 9, '2018-03-08 07:03:17', 'zO693A', 'aaa', 'bbb', 46.050071716309, 14.468921661377);

-- --------------------------------------------------------

--
-- Struktura tabele `prof_info`
--

CREATE TABLE IF NOT EXISTS `prof_info` (
  `user_id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `hire_date` date NOT NULL,
  `birth_date` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `home_address` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Odloži podatke za tabelo `prof_info`
--

INSERT INTO `prof_info` (`user_id`, `department`, `hire_date`, `birth_date`, `gender`, `home_address`, `phone`) VALUES
(1, 'Programming', '2017-01-01', '1995-06-17', 'Male', 'Ljubljana, Slovenia', '123-456-789 (Mobile)'),
(6, 'Department Two', '2000-10-05', '1950-05-10', 'Male', 'Professor street', '539595616');

-- --------------------------------------------------------

--
-- Struktura tabele `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `user_id` int(6) NOT NULL,
  `role` varchar(15) NOT NULL,
  PRIMARY KEY (`user_id`,`role`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Odloži podatke za tabelo `role`
--

INSERT INTO `role` (`user_id`, `role`) VALUES
(1, 'professor'),
(2, 'student'),
(3, 'student'),
(6, 'professor'),
(7, 'student'),
(11, 'student');

-- --------------------------------------------------------

--
-- Struktura tabele `testTable`
--

CREATE TABLE IF NOT EXISTS `testTable` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `website` varchar(100) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Odloži podatke za tabelo `testTable`
--

INSERT INTO `testTable` (`id`, `name`, `email`, `website`, `reg_date`) VALUES
(1, 'abcdef', 'efghife', 'testWebpage.si', '0000-00-00 00:00:00'),
(2, 'abcdef', 'efghifeaaa', 'testWebpage.si', '0000-00-00 00:00:00'),
(3, 'abcdef', 'efghifeaaa', 'testWebpage.si', '0000-00-00 00:00:00'),
(4, 'abcdef', 'efghifeaaa', 'testWebpage.si', '0000-00-00 00:00:00'),
(5, 'Testing', 'After', 'Modification.com', '2017-09-29 12:41:38'),
(6, 'Test', 'Data', 'Entry.com', '2017-09-30 10:19:20'),
(7, 'Test', 'thing', 'post', '2017-10-01 21:10:25'),
(8, 'a', 'b', 'c', '2017-10-01 21:21:43'),
(9, 'a', 'b', 'cdefg', '2017-10-01 21:24:45'),
(10, 'last', 'test', 'today', '2017-10-01 21:33:08'),
(11, 'a', 'b', 'c', '2017-10-02 09:43:47'),
(12, 'New', 'Data Entry', 'Testtesttest', '2017-10-02 09:54:00'),
(13, 'New', 'Data Entry', 'Testtesttest', '2017-10-02 09:54:28'),
(14, 'Last One', 'Before', 'Push', '2017-10-02 09:55:18'),
(15, 'New', 'Test', 'AfterOptimization', '2017-10-02 10:52:25'),
(16, 'a', 'o', 'c', '2017-10-02 10:53:06'),
(17, 'a', 'o', 'c2', '2017-10-02 10:53:12'),
(18, 'after', 'lib', 'remove', '2017-10-02 10:56:03'),
(19, '', '', '', '2017-10-24 10:46:07'),
(20, 'New', 'Entry ', 'Today.com', '2017-10-24 10:46:24'),
(21, 'new', 'test', '123', '2017-11-02 14:42:39'),
(22, 'test', 'ttt', 'ttt', '2017-11-02 14:54:43');

-- --------------------------------------------------------

--
-- Struktura tabele `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `id_2` (`user_id`),
  KEY `id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Odloži podatke za tabelo `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `username`, `password`, `reg_date`) VALUES
(1, 'Jan', 'Starc ', 'jan.starc@gmail.com', 'js5898 ', '12d3dc5a6e8758f06181965e9dda38b509b9ecf2dfdb1fd2b458a43504691bcf76e96cd9c727c573abc72f2db075ced27ad024a5ad026036d3b9290d4062abcf', '2017-10-31 12:43:09'),
(2, 'Student', 'One ', 'student.one@mail.com', 'student1 ', '12d3dc5a6e8758f06181965e9dda38b509b9ecf2dfdb1fd2b458a43504691bcf76e96cd9c727c573abc72f2db075ced27ad024a5ad026036d3b9290d4062abcf', '2017-11-02 00:11:30'),
(3, 'Student', 'Two ', 'student.two@mail.com', 'student2 ', '12d3dc5a6e8758f06181965e9dda38b509b9ecf2dfdb1fd2b458a43504691bcf76e96cd9c727c573abc72f2db075ced27ad024a5ad026036d3b9290d4062abcf', '2017-11-02 00:11:47'),
(6, 'Professor', 'Two ', 'professor.two@mail.com', 'prof2 ', '12d3dc5a6e8758f06181965e9dda38b509b9ecf2dfdb1fd2b458a43504691bcf76e96cd9c727c573abc72f2db075ced27ad024a5ad026036d3b9290d4062abcf', '2017-11-04 13:37:32'),
(7, 'Student', 'Three ', 'student.three@mail.com', 'student3 ', '12d3dc5a6e8758f06181965e9dda38b509b9ecf2dfdb1fd2b458a43504691bcf76e96cd9c727c573abc72f2db075ced27ad024a5ad026036d3b9290d4062abcf', '2017-11-04 16:47:58'),
(11, 'Phone', 'User1 ', 'aaa', 'phone1 ', '12d3dc5a6e8758f06181965e9dda38b509b9ecf2dfdb1fd2b458a43504691bcf76e96cd9c727c573abc72f2db075ced27ad024a5ad026036d3b9290d4062abcf', '2017-12-03 21:03:55');

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`prof_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Omejitve za tabelo `prof_info`
--
ALTER TABLE `prof_info`
  ADD CONSTRAINT `prof_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Omejitve za tabelo `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

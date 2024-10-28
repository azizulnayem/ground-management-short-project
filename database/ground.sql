-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2024 at 01:38 PM
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
-- Database: `ground`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `blog_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`blog_id`, `title`, `content`, `image_url`, `created_at`) VALUES
(1, 'The Benefits of Regular Exercise', 'Regular exercise offers numerous benefits for both physical and mental health. Physically, it helps maintain a healthy weight, strengthens the heart, enhances muscle and bone health, and boosts the immune system. Mentally, exercise is linked to improved mood and reduced symptoms of anxiety and depression, thanks to the release of endorphins, which act as natural mood lifters. Additionally, engaging in regular physical activity can improve sleep quality and cognitive function while providing a sense of accomplishment and increasing self-esteem. Overall, incorporating exercise into daily life can significantly enhance one\'s quality of life by promoting overall well-being.', 'uploads/671eb33b0b67b.jpeg', '2024-10-28 03:32:35'),
(2, 'The Importance of Hydration', 'Staying hydrated is crucial for maintaining overall health and well-being. Water plays a vital role in numerous bodily functions, including temperature regulation, nutrient transport, and waste elimination. Dehydration can lead to fatigue, headaches, and impaired cognitive function. It\'s essential to drink an adequate amount of water daily, which can vary based on individual needs, activity levels, and environmental conditions. Incorporating water-rich foods into your diet, such as fruits and vegetables, can also contribute to hydration.', 'uploads/671ebc25b1b61.png', '2024-10-28 04:18:13'),
(3, 'The Benefits of Mindfulness Meditation', 'Mindfulness meditation is a practice that encourages individuals to focus on the present moment, cultivating awareness and acceptance without judgment. Research shows that regular practice can reduce stress, enhance emotional regulation, and improve mental clarity. By dedicating a few minutes each day to mindfulness meditation, individuals can develop a greater sense of calm and resilience, leading to improved overall well-being. This accessible practice can be integrated into daily routines, providing a valuable tool for navigating life\'s challenges.', 'uploads/671ebcd018318.jpeg', '2024-10-28 04:21:04'),
(4, 'The Impact of Nutrition on Mental Health', 'Nutrition plays a significant role in mental health, with research indicating that a balanced diet can influence mood and cognitive function. Consuming a variety of nutrient-dense foods, such as fruits, vegetables, whole grains, and lean proteins, supports brain health and can help mitigate symptoms of anxiety and depression. Omega-3 fatty acids found in fatty fish and nuts have been shown to have particularly beneficial effects on mood regulation. Prioritizing nutrition is essential for maintaining not only physical health but also mental well-being.', 'uploads/671ebd287f6d1.jpg', '2024-10-28 04:22:32'),
(5, 'Tips for Better Sleep Hygiene', 'Establishing good sleep hygiene is essential for achieving restorative sleep and overall health. This includes creating a relaxing bedtime routine, maintaining a consistent sleep schedule, and optimizing the sleep environment by reducing noise and light. Limiting screen time before bed and avoiding caffeine in the afternoon can also contribute to improved sleep quality. Prioritizing sleep hygiene can enhance mood, cognitive function, and physical health, making it a vital aspect of a healthy lifestyle.', 'uploads/671ebd81b7d76.jpeg', '2024-10-28 04:24:01'),
(6, 'The Power of Positive Thinking', 'Positive thinking is a mental attitude that focuses on the favorable aspects of life and anticipates positive outcomes. Research suggests that cultivating a positive mindset can lead to improved mental health, increased resilience, and enhanced overall well-being. Practicing gratitude, surrounding oneself with supportive people, and reframing negative thoughts are effective strategies for fostering positivity. By shifting focus from challenges to possibilities, individuals can create a more fulfilling and optimistic life.', 'uploads/671ebda865efe.png', '2024-10-28 04:24:40'),
(8, 'How to Cultivate a Growth Mindset', 'A growth mindset is the belief that abilities and intelligence can be developed through dedication and hard work. This perspective fosters a love for learning, resilience in the face of challenges, and a willingness to embrace failure as a stepping stone to success. To cultivate a growth mindset, individuals can focus on setting achievable goals, seeking feedback, and celebrating progress, no matter how small. By adopting this mindset, people can unlock their potential and pursue their aspirations with confidence.', 'uploads/671ebe1262e4d.jpeg', '2024-10-28 04:26:26');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('open','resolved') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp(),
  `resolution_comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `user_id`, `description`, `status`, `created_at`, `resolution_comment`) VALUES
(4, 10, 'I can not access my profile correctly.', 'resolved', '2024-10-28 12:32:14', 'problem solved'),
(5, 10, 'I can not complain ', 'resolved', '2024-10-28 12:32:57', 'Now try again , you can complain properly.'),
(6, 10, 'Probelm', 'resolved', '2024-10-28 15:13:46', 'solvedddddddddddd'),
(7, 10, 'how to do it', 'resolved', '2024-10-28 15:13:52', 'dddddddddd'),
(8, 10, 'i have serious problem', 'open', '2024-10-28 15:19:59', NULL),
(9, 10, 'complain korlam tomar name', 'resolved', '2024-10-28 16:52:54', 'solved');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `image_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`image_id`, `image_url`, `description`) VALUES
(4, 'uploads/gallery/Carosol_1.jpg', 'UIU - ground view from corner side'),
(6, 'uploads/gallery/ground 1.jpeg', 'UIU Sports Club - UIU Sports Club.');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `notice_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`notice_id`, `title`, `description`, `image_path`, `created_at`) VALUES
(4, 'Community Cleanup Day', 'Join us for a community cleanup day on Saturday, April 15th, from 9 AM to 1 PM. We will meet at the community park. Supplies will be provided. Let’s work together to keep our neighborhood clean!', 'uploads/clean.jpeg', '2024-10-27 14:26:54'),
(5, ' Monthly Book Club Meeting', 'Our next book clubb meeting will be held on Thursday, May 20th, at 7 PM in the community center. This month\'s book is \"The Great Gatsby.\" All are welcome!', 'uploads/images (5).jpeg', '2024-10-27 14:32:30'),
(6, 'Summer Sports Camp Registration Open', 'Registration for the Summer Sports Camp is now open! The camp will run from June 5th to June 30th, offering a variety of sports for kids aged 6-14. Visit our website to register.', 'uploads/clean.jpeg', '2024-10-27 14:33:18'),
(7, 'Annual Fundraising Gala', 'You are cordially invited to our Annual Fundraising Gala on Friday, March 10th, at 6 PM at the City Hall. Enjoy an evening of dinner, auctions, and entertainment while supporting our local charity.', 'uploads/images (1).jpeg', '2024-10-27 14:34:15'),
(8, 'Health and Wellness Workshop', 'Join us for a Health and Wellness Workshop on Sunday, April 30th, from 10 AM to 2 PM. Learn about nutrition, fitness, and mental health. Free refreshments will be provided!', 'uploads/images (2).jpeg', '2024-10-27 14:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`) VALUES
(5, 'admin', '$2y$10$IUVlI7pSu/XJDGN6SwKBQO99cbpFRtYrb.Hcex/VPd5.ZztGwfJNW', 'admin@gmail.com', 'admin'),
(10, 'mohsina', '$2y$10$8GHaaKwlDSj2TwIN0XBC1.2d0Jo1MFUVxcvGve1xUuMQnH9b.n71C', 'mohsina@gmail.com', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`blog_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`notice_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `blog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

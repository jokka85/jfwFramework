--
-- Table structure for table `admin_settings`
--

DROP TABLE IF EXISTS `admin_settings`;
CREATE TABLE `admin_settings` (
  `setting_name` varchar(255) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Settings used throughout application';

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`setting_name`, `setting_value`) VALUES
('min_password', '8'),
('max_password', '25'),
('min_username', '8'),
('max_username', '12'),
('alphaNum_password', 'true'),
('alphaNum_username', 'true'),
('logo', 'logo.jpg'),
('slogan', 'This is a test slogan'),
('mask_username', 'user_name'),
('mask_password', 'pass_word'),
('mask_first', 'first_name'),
('mask_middle', 'middle_name'),
('mask_last', 'last_name'),
('mask_birthdate', 'birth_date');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`setting_name`);

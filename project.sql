SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Table structure for table `authassignment`
--

CREATE TABLE IF NOT EXISTS `AuthAssignment` (
  `itemname` varchar(64) collate utf8_unicode_ci NOT NULL,
  `userid` varchar(64) collate utf8_unicode_ci NOT NULL,
  `bizrule` text collate utf8_unicode_ci,
  `data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`itemname`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `authassignment`
--
INSERT INTO `AuthAssignment` (`itemname`, `userid`, `bizrule`, `data`)VALUES
('Admin', '1', NULL, 'N;'),
('Admin', 'admin', NULL, 'N;');

-- --------------------------------------------------------

--
-- Table structure for table `authitem`
--

CREATE TABLE IF NOT EXISTS `AuthItem` (
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text collate utf8_unicode_ci,
  `bizrule` text collate utf8_unicode_ci,
  `data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `authitem`
--

INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
('Admin', 2, NULL, NULL, 'N;'),
('Admin.Default.*', 1, NULL, NULL, 'N;'),
('Authenticated', 2, NULL, NULL, 'N;'),
('FileInfo.*', 1, NULL, NULL, 'N;'),
('FileInfo.Admin', 0, NULL, NULL, 'N;'),
('FileInfo.Create', 0, NULL, NULL, 'N;'),
('FileInfo.Delete', 0, NULL, NULL, 'N;'),
('FileInfo.Index', 0, NULL, NULL, 'N;'),
('FileInfo.Update', 0, NULL, NULL, 'N;'),
('FileInfo.View', 0, NULL, NULL, 'N;'),
('Guest', 2, NULL, NULL, 'N;'),
('Site.*', 1, NULL, NULL, 'N;'),
('Site.Contact', 0, NULL, NULL, 'N;'),
('Site.Error', 0, NULL, NULL, 'N;'),
('Site.Index', 0, NULL, NULL, 'N;'),
('Site.Login', 0, NULL, NULL, 'N;'),
('Site.Logout', 0, NULL, NULL, 'N;'),
('Upload.*', 1, NULL, NULL, 'N;'),
('Upload.Index', 0, NULL, NULL, 'N;'),
('User.*', 1, NULL, NULL, 'N;'),
('User.Admin', 0, NULL, NULL, 'N;'),
('User.Create', 0, NULL, NULL, 'N;'),
('User.Delete', 0, NULL, NULL, 'N;'),
('User.Index', 0, NULL, NULL, 'N;'),
('User.Update', 0, NULL, NULL, 'N;'),
('User.View', 0, NULL, NULL, 'N;'),
('ZipGzDownloads.Admin', 0, NULL, NULL, 'N;'),
('ZipGzDownloads.Delete', 0, NULL, NULL, 'N;'),
('ZipGzDownloads.Download', 0, NULL, NULL, 'N;'),
('ZipGzDownloads.Index', 0, NULL, NULL, 'N;'),
('ZipGzDownloads.Update', 0, NULL, NULL, 'N;'),
('ZipGzDownloads.View', 0, NULL, NULL, 'N;');

-- --------------------------------------------------------

--
-- Table structure for table `authitemchild`
--

CREATE TABLE IF NOT EXISTS `authitemchild` (
  `parent` varchar(64) collate utf8_unicode_ci NOT NULL,
  `child` varchar(64) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `authitemchild`
--
--
INSERT INTO `authitemchild` (`parent`, `child`) VALUES
('Admin.Default.*', 'FileInfo.*'),
('Authenticated', 'Guest'),
('Admin.Default.*', 'Site.*'),
('Authenticated', 'Site.*'),
('Guest', 'Site.*'),
('Authenticated', 'Site.Contact'),
('Guest', 'Site.Contact'),
('Authenticated', 'Site.Error'),
('Guest', 'Site.Error'),
('Authenticated', 'Site.Index'),
('Guest', 'Site.Index'),
('Authenticated', 'Site.Login'),
('Guest', 'Site.Login'),
('Authenticated', 'Site.Logout'),
('Guest', 'Site.Logout'),
('Admin.Default.*', 'Upload.*'),
('Authenticated', 'Upload.*'),
('Authenticated', 'Upload.Index'),
('Admin.Default.*', 'User.*'),
('Admin.Default.*', 'ZipGzDownloads.Admin'),
('Authenticated', 'ZipGzDownloads.Download'),
('Authenticated', 'ZipGzDownloads.Index'),
('Authenticated', 'ZipGzDownloads.View');
-- --------------------------------------------------------


--
-- Table structure for table `csv_meta_paths`
--

CREATE TABLE IF NOT EXISTS `csv_meta_paths` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(7) NOT NULL,
  `path` varchar(255) collate utf8_unicode_ci NOT NULL,
  `added_to_archives` int(1) NOT NULL default '0',
  `creationdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `csv_meta_paths`
--

--
-- Table structure for table `excel_metadata`
--

CREATE TABLE IF NOT EXISTS `excel_metadata` (
  `id` int(7) NOT NULL auto_increment,
  `app_name` varchar(50) collate utf8_unicode_ci default NULL,
  `app_version` varchar(50) collate utf8_unicode_ci default NULL,
  `author` varchar(255) collate utf8_unicode_ci default NULL,
  `company` varchar(255) collate utf8_unicode_ci NOT NULL,
  `content_type` varchar(100) collate utf8_unicode_ci default NULL,
  `creationdate` varchar(50) collate utf8_unicode_ci default NULL,
  `last_author` varchar(255) collate utf8_unicode_ci default NULL,
  `last_modified` varchar(255) collate utf8_unicode_ci default NULL,
  `creator` varchar(255) collate utf8_unicode_ci default NULL,
  `date_create` varchar(255) collate utf8_unicode_ci default NULL,
  `protected` varchar(25) collate utf8_unicode_ci default NULL,
  `publisher` varchar(255) collate utf8_unicode_ci default NULL,
  `resourcename` varchar(255) collate utf8_unicode_ci default NULL,
  `title` varchar(255) collate utf8_unicode_ci default NULL,
  `file_id` int(12) NOT NULL,
  `user_id` int(7) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `excel_metadata`
--


--
-- Table structure for table `file_type`
--

CREATE TABLE IF NOT EXISTS `file_type` (
  `id` int(4) NOT NULL auto_increment,
  `file_type` varchar(125) collate utf8_bin NOT NULL,
  `file_type_name` varchar(50) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- Dumping data for table `file_type`
--

INSERT INTO `file_type` (`id`, `file_type`, `file_type_name`) VALUES
(1, 'application/pdf', 'PDF'),
(2, 'application/msword', 'MS Word 2003 format'),
(3, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'MS Word 2007 format'),
(4, 'image/tiff', 'TIFF'),
(5, 'image/jpeg', 'JPEG'),
(6, 'image/gif', 'GIF'),
(7, 'text/plain', 'Text File'),
(8, 'application/vnd.ms-excel', 'MS Excel 2003 format'),
(9, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'MS Excel 2007 format'),
(10, 'image/png', 'PNG');

--
-- Table structure for table `file_info`
--

CREATE TABLE IF NOT EXISTS `file_info` (
  `id` int(10) NOT NULL auto_increment,
  `org_file_path` varchar(2084) collate utf8_bin default NULL,
  `temp_file_path` varchar(1000) collate utf8_bin default NULL COMMENT 'orginial file path.  2083 character URL appears to be IE limit',
  `file_type_id` int(1) NOT NULL default '0' COMMENT 'current file path on the server',
  `checksum` varchar(40) collate utf8_bin default NULL COMMENT 'file check sum sha1 or md5. sha1 is the default',
  `virus_check` int(1) NOT NULL default '0' COMMENT 'has file had virus check',
  `metadata` int(1) NOT NULL default '0' COMMENT 'Whether metadata extraction has been run',
  `dynamic_file` int(1) NOT NULL default '0' COMMENT 'is the file dynamically generated from orginal URL',
  `last_modified` varchar(15) collate utf8_bin default NULL COMMENT 'file last modified timestamp',
  `zipped` int(1) NOT NULL default '0' COMMENT 'Yes/No added to a Zip Archive',
  `problem_file` int(1) NOT NULL default '0',
  `user_id` int(6) NOT NULL default '0' COMMENT 'CONSTRAINT FOREIGN KEY (user_id) REFERENCES user(id)',
  `upload_file_id` int(6) NOT NULL default '0' COMMENT 'CONSTRAINT FOREIGN KEY (upload_file_id) REFERENCES user_uploads(id)',
  `download_time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'time file was downloaded for processing',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='downloaded file information' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `file_info`
--

-- --------------------------------------------------------

--
-- Table structure for table `files_for_download`
--

CREATE TABLE IF NOT EXISTS `files_for_download` (
  `id` int(10) NOT NULL auto_increment,
  `url` varchar(500) collate utf8_unicode_ci NOT NULL,
  `user_uploads_id` int(7) NOT NULL COMMENT "CONSTRAINT FOREIGN KEY (user_uploads_id) REFERENCES user_uploads(id)",
  `user_id` int(6) NOT NULL COMMENT "CONSTRAINT FOREIGN KEY (user_id) REFERENCES user(id)",
  `processed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `files_for_download`
--


--
-- Table structure for table `jpg_metadata`
--

CREATE TABLE IF NOT EXISTS `jpg_metadata` (
  `id` int(12) NOT NULL auto_increment,
  `color_space` varchar(25) collate utf8_unicode_ci default NULL,
  `component_one` varchar(150) collate utf8_unicode_ci default NULL,
  `component_two` varchar(150) collate utf8_unicode_ci default NULL,
  `component_three` varchar(150) collate utf8_unicode_ci default NULL,
  `compression` varchar(25) collate utf8_unicode_ci default NULL,
  `content_type` varchar(50) collate utf8_unicode_ci default NULL,
  `data_precision` varchar(15) collate utf8_unicode_ci default NULL,
  `date_time` varchar(50) collate utf8_unicode_ci default NULL,
  `exif_image_height` varchar(25) collate utf8_unicode_ci default NULL,
  `exif_image_width` varchar(25) collate utf8_unicode_ci default NULL,
  `last_modified` varchar(50) collate utf8_unicode_ci default NULL,
  `number_of_components` varchar(5) collate utf8_unicode_ci default NULL,
  `orientation` varchar(75) collate utf8_unicode_ci default NULL,
  `software` varchar(75) collate utf8_unicode_ci default NULL,
  `x_resolution` varchar(50) collate utf8_unicode_ci default NULL,
  `y_resolution` varchar(50) collate utf8_unicode_ci default NULL,
  `resourcename` varchar(255) collate utf8_unicode_ci default NULL,
  `file_id` int(12) NOT NULL,
  `user_id` int(7) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `jpg_metadata`
--


-- --------------------------------------------------------

--
-- Table structure for table `pdf_metadata`
--

CREATE TABLE IF NOT EXISTS `pdf_metadata` (
  `id` int(10) NOT NULL auto_increment,
  `author` varchar(250) collate utf8_bin default NULL,
  `creation_date` varchar(30) collate utf8_bin default NULL COMMENT 'file creation date',
  `last_modified` varchar(30) collate utf8_bin default NULL COMMENT 'file last modified date',
  `creator` varchar(250) collate utf8_bin default NULL,
  `producer` varchar(250) collate utf8_bin default NULL COMMENT 'software used to create the PDF',
  `resource_name` varchar(250) collate utf8_bin default NULL COMMENT 'similar too but not the same as title',
  `title` text collate utf8_bin,
  `pages` int(5) default 0 COMMENT 'Number of pages',
  `subject` varchar(250) collate utf8_bin default NULL,
  `keywords` text collate utf8_bin,
  `licensed_to` varchar(250) collate utf8_bin default NULL COMMENT 'who the pdf software is registered too',
  `file_id` int(10) default NULL COMMENT 'id of associated file',
  `user_id` int(6) default NULL COMMENT 'id of user associated with the file',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pdf_metadata`
--

-- --------------------------------------------------------

--
-- Table structure for table `problem_files`
--

CREATE TABLE IF NOT EXISTS `problem_files` (
  `id` int(7) NOT NULL auto_increment,
  `error_id` int(3) NOT NULL,
  `file_id` int(10) default NULL,
  `user_id` int(7) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `error_id` (`error_id`),
  KEY `file_id` (`file_id`),
  KEY `user_id` (`user_id`)
)

--
-- Table structure for table `rights`
--

CREATE TABLE IF NOT EXISTS `Rights` (
  `itemname` varchar(64) collate utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY  (`itemname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `rights`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(5) NOT NULL auto_increment,
  `username` varchar(25) collate utf8_unicode_ci NOT NULL,
  `email` varchar(256) collate utf8_unicode_ci NOT NULL,
  `password` varchar(64) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_session_info`
--

CREATE TABLE IF NOT EXISTS `user_session_info` (
  `id` char(32) collate utf8_unicode_ci NOT NULL,
  `expire` int(11) default NULL,
  `data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_session_info`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_uploads`
--

CREATE TABLE IF NOT EXISTS `upload` (
  `id` int(7) NOT NULL auto_increment,
  `user_id` int(6) NOT NULL COMMENT "CONSTRAINT FOREIGN KEY (user_id) REFERENCES user(id)",
  `upload_path` varchar(250) collate utf8_unicode_ci NOT NULL,
  `processed` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `upload`
--


--
-- Table structure for table `word_metadata`
--

CREATE TABLE IF NOT EXISTS `word_metadata` (
  `id` int(12) NOT NULL auto_increment,
  `app_name` varchar(50) collate utf8_unicode_ci default NULL,
  `app_version` varchar(50) collate utf8_unicode_ci default NULL,
  `author` varchar(255) collate utf8_unicode_ci default NULL,
  `comments` varchar(255) collate utf8_unicode_ci default NULL,
  `company` varchar(255) collate utf8_unicode_ci default NULL,
  `content_type` varchar(100) collate utf8_unicode_ci default NULL,
  `creationdate` varchar(50) collate utf8_unicode_ci default NULL,
  `keywords` varchar(255) collate utf8_unicode_ci default NULL,
  `last_author` varchar(255) collate utf8_unicode_ci default NULL,
  `last_modified` varchar(255) collate utf8_unicode_ci default NULL,
  `pages` int(5) default NULL,
  `revision_number` int(4) default NULL,
  `template` varchar(50) collate utf8_unicode_ci default NULL,
  `creator` varchar(255) collate utf8_unicode_ci default NULL,
  `date_create` varchar(255) collate utf8_unicode_ci default NULL,
  `publisher` varchar(255) collate utf8_unicode_ci default NULL,
  `resourcename` varchar(255) collate utf8_unicode_ci default NULL,
  `subject` varchar(255) collate utf8_unicode_ci default NULL,
  `title` varchar(255) collate utf8_unicode_ci default NULL,
  `file_id` int(12) NOT NULL,
  `user_id` int(7) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `word_metadata`
--



--
-- Table structure for table `zip_gz_downloads`
--

CREATE TABLE IF NOT EXISTS `zip_gz_downloads` (
  `id` int(9) NOT NULL auto_increment,
  `user_id` int(7) NOT NULL,
  `archive_path` varchar(500) collate utf8_unicode_ci NOT NULL,
  `downloaded` int(1) NOT NULL default '0',
  `creationdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `zip_gz_downloads`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `authassignment`
--
ALTER TABLE `AuthAssignment`
  ADD CONSTRAINT `AuthAssignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `authitemchild`
--
ALTER TABLE `authitemchild`
  ADD CONSTRAINT `authitemchild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `authitemchild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rights`
--
ALTER TABLE `Rights`
  ADD CONSTRAINT `Rights_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `problem_files`
--
ALTER TABLE `problem_files`
  ADD CONSTRAINT `problem_files_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `problem_files_ibfk_1` FOREIGN KEY (`error_id`) REFERENCES `error_type` (`id`),
  ADD CONSTRAINT `problem_files_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file_info` (`id`);
 
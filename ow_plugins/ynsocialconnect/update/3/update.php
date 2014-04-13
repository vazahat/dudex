<?php

// update languges
Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'ynsocialconnect');

//Update config
Updater::getConfigService() -> addConfig('ynsocialconnect', 'signup_mode', '0');

//Update database
$dbPrefix = OW_DB_PREFIX;

$sql =
    <<<EOT
DELETE FROM `{$dbPrefix}ynsocialconnect_services` WHERE `{$dbPrefix}ynsocialconnect_services`.`name` = 'myspace';

EOT;

Updater::getDbo()->query($sql);

$sql =
   <<<EOT
CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialconnect_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `label` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_name` (`service`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
   
INSERT IGNORE INTO `{$dbPrefix}ynsocialconnect_options` (`id`, `service`, `name`, `label`) VALUES
(1, 'yahoo', 'email', 'Email'),
(2, 'yahoo', 'full_name', 'Full Name'),
(3, 'yahoo', 'FirstName', 'First Name'),
(4, 'yahoo', 'LastName', 'Last Name'),
(5, 'yahoo', 'gender', 'Gender'),
(6, 'google', 'full_name', 'Full Name'),
(7, 'google', 'email', 'Email'),
(8, 'google', 'FirstName', 'First Name'),
(9, 'google', 'LastName', 'Last Name'),
(10, 'google', 'gender', 'Gender'),
(29, 'facebook', 'id', 'ID'),
(30, 'facebook', 'name', 'Name'),
(31, 'facebook', 'first_name', 'First Name'),
(32, 'facebook', 'birthday', 'Birthday'),
(33, 'facebook', 'last_name', 'Last Name'),
(34, 'facebook', 'link', 'Link'),
(35, 'facebook', 'gender', 'Gender'),
(36, 'facebook', 'timezone', 'Timezone'),
(37, 'facebook', 'locale', 'Locale'),
(41, 'twitter', 'notifications', 'Notifications'),
(43, 'twitter', 'description', 'Description'),
(44, 'twitter', 'lang', 'Language'),
(46, 'twitter', 'location', 'Location'),
(49, 'twitter', 'time_zone', 'Timezone'),
(51, 'twitter', 'username', 'Username'),
(52, 'twitter', 'first_name', 'First Name'),
(53, 'twitter', 'following', 'Following'),
(56, 'twitter', 'followers_count', 'Followers Count'),
(58, 'twitter', 'contributors_enabled', 'Contributors'),
(62, 'twitter', 'favourites_count', 'Favourites Count'),
(64, 'twitter', 'screen_name', 'Screent Name'),
(66, 'twitter', 'name', 'Name'),
(67, 'twitter', 'friends_count', 'Friends Count'),
(68, 'twitter', 'id', 'User ID'),
(69, 'twitter', 'follow_request_sent', 'Follow Request Sent'),
(70, 'twitter', 'about_me', 'About Me'),
(71, 'twitter', 'url', 'URL'),
(77, 'twitter', 'last_name', 'Last Name'),
(85, 'twitter', 'website', 'Website'),
(128, 'facebook', 'email', 'Email'),
(129, 'linkedin', 'id', 'User Id'),
(130, 'linkedin', 'first_name', 'First Name'),
(131, 'linkedin', 'last_name', 'Last Name'),
(132, 'linkedin', 'headline', 'Headline'),
(135, 'linkedin', 'username', 'Username'),
(136, 'linkedin', 'current-status', 'Status'),
(138, 'linkedin', 'displayname', 'Full Name'),
(165, 'flickr', 'username', 'Username'),
(166, 'flickr', 'realname', 'Real Name'),
(167, 'flickr', 'location', 'Location'),
(168, 'flickr', 'photosurls', 'Photo Url'),
(170, 'flickr', 'profileurls', 'Profile Url'),
(251, 'clavid', 'nickname', 'Nickname'),
(252, 'clavid', 'email', 'Email'),
(253, 'clavid', 'fullname', 'Full Name'),
(254, 'clavid', 'dob', 'Date of Birth'),
(255, 'clavid', 'gender', 'Gender'),
(256, 'clavid', 'postcode', 'Postcode'),
(257, 'clavid', 'country', 'Country'),
(258, 'clavid', 'language', 'Language'),
(259, 'clavid', 'timezone', 'Timezone'),
(311, 'liquidid', 'nickname', 'Nickname'),
(312, 'liquidid', 'email', 'Email'),
(313, 'liquidid', 'fullname', 'Full Name'),
(314, 'liquidid', 'dob', 'Date of Birth'),
(315, 'liquidid', 'gender', 'Gender'),
(316, 'liquidid', 'postcode', 'Postcode'),
(317, 'liquidid', 'country', 'Country'),
(318, 'liquidid', 'language', 'Language'),
(319, 'liquidid', 'timezone', 'Timezone'),
(371, 'verisign', 'nickname', 'Nickname'),
(372, 'verisign', 'email', 'Email'),
(373, 'verisign', 'fullname', 'Full Name'),
(374, 'verisign', 'dob', 'Date of Birth'),
(375, 'verisign', 'gender', 'Gender'),
(376, 'verisign', 'postcode', 'Postcode'),
(377, 'verisign', 'country', 'Country'),
(378, 'verisign', 'language', 'Language'),
(379, 'verisign', 'timezone', 'Timezone'),
(381, 'wordpress', 'nickname', 'nickname'),
(382, 'wordpress', 'email', 'Email'),
(383, 'wordpress', 'fullname', 'Full Name'),
(384, 'wordpress', 'dob', 'Date of Birth'),
(385, 'wordpress', 'gender', 'Gender'),
(386, 'wordpress', 'postcode', 'Postcode'),
(387, 'wordpress', 'country', 'Country'),
(389, 'wordpress', 'timezone', 'Timezone'),
(390, 'facebook', 'website', 'Website'),
(391, 'facebook', 'username', 'Username'),
(392, 'live', 'email', 'Email'),
(393, 'live', 'full_name', 'Full Name'),
(394, 'live', 'first_name', 'First Name'),
(395, 'live', 'last_name', 'Last Name');

CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialconnect_fields` (
  `id` int(11) NOT NULL auto_increment,
  `question` varchar(32) collate utf8_unicode_ci default NULL,
  `field` varchar(32) collate utf8_unicode_ci default NULL,
  `service` varchar(32) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

INSERT IGNORE INTO `{$dbPrefix}ynsocialconnect_fields` (`id`, `question`, `field`, `service`) VALUES
(73, 'realname', 'name', 'facebook'),
(74, 'email', 'email', 'facebook'),
(76, 'username', 'username', 'facebook'),
(77, 'sex', 'gender', 'facebook'),
(78, 'birthdate', 'birthday', 'facebook'),
(79, 'realname', 'full_name', 'yahoo'),
(80, 'sex', 'gender', 'yahoo'),
(81, 'email', 'email', 'yahoo'),
(82, 'username', 'full_name', 'yahoo'),
(84, 'realname', 'full_name', 'google'),
(85, 'sex', 'gender', 'google'),
(86, 'email', 'email', 'google'),
(87, 'username', 'full_name', 'google'),
(89, 'realname', 'name', 'twitter'),
(92, 'realname', 'displayname', 'linkedin'),
(93, 'username', 'username', 'linkedin'),
(94, 'username', 'username', 'twitter'),
(95, 'realname', 'full_name', 'live'),
(96, 'email', 'email', 'live'),
(97, 'username', 'full_name', 'live'),
(98, 'realname', 'fullname', 'verisign'),
(99, 'sex', 'gender', 'verisign'),
(100, 'email', 'email', 'verisign'),
(101, 'birthdate', 'dob', 'verisign'),
(102, 'username', 'nickname', 'verisign'),
(103, 'realname', 'fullname', 'clavid'),
(104, 'sex', 'gender', 'clavid'),
(105, 'email', 'email', 'clavid'),
(106, 'birthdate', 'dob', 'clavid'),
(107, 'username', 'nickname', 'clavid'),
(108, 'realname', 'realname', 'flickr'),
(109, 'username', 'username', 'flickr'),
(110, 'realname', 'fullname', 'wordpress'),
(111, 'sex', 'gender', 'wordpress'),
(112, 'email', 'email', 'wordpress'),
(113, 'birthdate', 'dob', 'wordpress'),
(114, 'username', 'nickname', 'wordpress'),
(115, 'realname', 'fullname', 'liquidid'),
(116, 'sex', 'gender', 'liquidid'),
(117, 'email', 'email', 'liquidid'),
(118, 'birthdate', 'dob', 'liquidid'),
(119, 'username', 'nickname', 'liquidid');

EOT;

Updater::getDbo()->query($sql);
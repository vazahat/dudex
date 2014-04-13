<?php
$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ynsocialconnect_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `identity` varchar(128) NOT NULL,
  `serviceId` int(11) unsigned NOT NULL,
  `ordering` int(11) unsigned NOT NULL,
  `status` text NOT NULL,
  `login` int(10) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `tokenData` text NOT NULL,
  `token` varchar(256) NOT NULL,
  `createdTime` int(11) NOT NULL,
  `loginTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `logoutTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);";
OW::getDbo() -> query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ynsocialconnect_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `title` varchar(128) NOT NULL,
  `privacy` tinyint(1) NOT NULL DEFAULT '0',
  `connect` int(11) NOT NULL DEFAULT '0',
  `protocol` varchar(32) NOT NULL DEFAULT 'openid',
  `mode` varchar(32) NOT NULL DEFAULT 'popup',
  `w` int(11) NOT NULL DEFAULT '800',
  `h` int(11) NOT NULL DEFAULT '450',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `isActive` int(11) NOT NULL DEFAULT '1',
  `params` text,
  `totalSignup` int(10) unsigned NOT NULL DEFAULT '0',
  `totalSync` int(10) unsigned NOT NULL DEFAULT '0',
  `totalLogin` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);";
OW::getDbo() -> query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ynsocialconnect_user_linking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `identity` varchar(128) NOT NULL,
  `serviceId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
);";
OW::getDbo() -> query($sql);


$sql = "INSERT IGNORE INTO `" . OW_DB_PREFIX . "ynsocialconnect_services` (`id`, `name`, `title`, `privacy`, `connect`, `protocol`, `mode`, `w`, `h`, `ordering`, `isActive`, `params`, `totalSignup`, `totalSync`, `totalLogin`) VALUES
(1, 'facebook', 'Facebook', 1, 1, 'oauth', 'popup', 800, 450, 1, 1, NULL, 0, 0, 0),
(2, 'twitter', 'Twitter', 1, 1, 'oauth', 'popup', 800, 450, 4, 1, NULL, 0, 0, 0),
(3, 'google', 'Google', 1, 1, 'oauth', 'popup', 800, 450, 5, 1, NULL, 0, 0, 0),
(4, 'yahoo', 'Yahoo', 1, 1, 'oauth', 'popup', 800, 450, 2, 1, NULL, 0, 0, 0),
(5, 'linkedin', 'Linkedin', 1, 1, 'oauth', 'popup', 800, 450, 3, 1, NULL, 0, 0, 0),
(6, 'live', 'Live', 1, 1, 'oauth', 'popup', 800, 450, 7, 1, NULL, 0, 0, 0),
(7, 'flickr', 'Flickr', 1, 1, 'oauth', 'popup', 800, 450, 24, 1, NULL, 0, 0, 0),
(8, 'liquidid', 'LiquidID', 1, 0, 'openid', 'popup', 800, 450, 38, 1, NULL, 0, 0, 0),
(9, 'wordpress', 'WordPress', 1, 0, 'openid', 'popup', 800, 450, 27, 1, NULL, 0, 0, 0),
(10, 'verisign', 'VeriSign', 1, 0, 'openid', 'popup', 800, 450, 11, 1, NULL, 0, 0, 0),
(11, 'clavid', 'Clavid', 1, 0, 'openid', 'popup', 800, 450, 14, 1, NULL, 0, 0, 0);
";
OW::getDbo() -> query($sql);

$dbPrefix = OW_DB_PREFIX;
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
OW::getDbo()->query($sql);

//	global setting, will delete ALL automatically when uninstall
OW::getConfig()->addConfig('ynsocialconnect', 'limit_providers_view_on_login_header', '7');
OW::getConfig()->addConfig('ynsocialconnect', 'size_of_provider_icon_px', '22');
OW::getConfig()->addConfig('ynsocialconnect', 'position_providers_on_header', '1');
OW::getConfig()->addConfig('ynsocialconnect', 'signup_mode', '0');

//	add setting
OW::getPluginManager()->addPluginSettingsRouteName('ynsocialconnect', 'ynsocialconnect_admin_settings');

OW::getLanguage() -> importPluginLangs(OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getRootDir() . 'langs.zip', 'ynsocialconnect');

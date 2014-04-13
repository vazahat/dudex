<?php

BOL_LanguageService::getInstance()->addPrefix('ynsocialpublisher', 'Social Publisher');

// insert to db
$dbPrefix = OW_DB_PREFIX;

$sql =
    <<<EOT
CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialpublisher_usersetting` (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `option` tinyint(1) NOT NULL default '0',
  `privacy` int(1) default '7',
  `providers` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
EOT;

ow::getDbo()->query($sql);

// Adds admin settings page route.
OW::getPluginManager()->addPluginSettingsRouteName('ynsocialpublisher', 'ynsocialpublisher.admin');

// add language file
$path = OW::getPluginManager()->getPlugin('ynsocialpublisher')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'photo');

// --- Add pre admin config ---
$config = OW::getConfig();
// wall feed
if ( !$config->configExists('ynsocialpublisher', 'newsfeed') )
{
    $config->addConfig('ynsocialpublisher', 'newsfeed', '{"type":"user-status","title":"NewsFeed","active":"1","providers":["facebook","twitter","linkedin"]}', 'NewsFeed');
}
// photo
if ( !$config->configExists('ynsocialpublisher', 'photo') )
{
    $config->addConfig('ynsocialpublisher', 'photo', '{"type":"photo","title":"Photo","active":"1","providers":["facebook","twitter","linkedin"]}', 'Photo');
}
// blog
if ( !$config->configExists('ynsocialpublisher', 'blogs') )
{
    $config->addConfig('ynsocialpublisher', 'blogs', '{"type":"blog","title":"Blog","active":"1","providers":["facebook","twitter","linkedin"]}', 'Blog');
}
// event
if ( !$config->configExists('ynsocialpublisher', 'event') )
{
    $config->addConfig('ynsocialpublisher', 'event', '{"type":"event","title":"Event","active":"1","providers":["facebook","twitter","linkedin"]}', 'Event');
}
// forum
if ( !$config->configExists('ynsocialpublisher', 'forum') )
{
    $config->addConfig('ynsocialpublisher', 'forum', '{"type":"forum","title":"Forum","active":"1","providers":["facebook","twitter","linkedin"]}', 'Forum');
}
// group
if ( !$config->configExists('ynsocialpublisher', 'groups') )
{
    $config->addConfig('ynsocialpublisher', 'groups', '{"type":"group","title":"Group","active":"1","providers":["facebook","twitter","linkedin"]}', 'Group');
}
// video
if ( !$config->configExists('ynsocialpublisher', 'video') )
{
    $config->addConfig('ynsocialpublisher', 'video', '{"type":"video","title":"Video","active":"1","providers":["facebook","twitter","linkedin"]}', 'Video');
}
// link
if ( !$config->configExists('ynsocialpublisher', 'links') )
{
    $config->addConfig('ynsocialpublisher', 'links', '{"type":"link","title":"Link","active":"1","providers":["facebook","twitter","linkedin"]}', 'Link');
}




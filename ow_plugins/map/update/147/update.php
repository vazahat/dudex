<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'map');




$sql="INSERT INTO `" . OW_DB_PREFIX. "map_category` (
    id,  id2 ,    active , name
)VALUES(
    '','0','1','POI'
)";
$last_insertid=OW::getDbo()->insert($sql);
if ($last_insertid>0){
    $sql="update `" . OW_DB_PREFIX. "map_category` set id2='".$last_insertid."' WHERE id2='0' AND id<>'".$last_insertid."' ";
    OW::getDbo()->query($sql);
}

/*
$plname="map";
$source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
$pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;
//echo $source;
//echo "<hr>";
//echo $pluginStaticDir;
//exit;
//CMS_BOL_Service::getInstance()->cpydir($source, $pluginStaticDir);
//echo "sss";exit;
map_cpydir($source, $pluginStaticDir);
function map_cpydir($source,$dest){
        if(is_dir($source)) {
            $dir_handle=opendir($source);
            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){
                    if(is_dir($source.$file)){

                        if (!is_dir($dest.$file.DS)) mkdir($dest.$file.DS);

                        map_cpydir($source.$file.DS, $dest.$file.DS);
                    } else {
//echo $source.$file."<br>".$dest.$file."<hr>";
//                        if (!is_file($dest.$file)) copy($source.$file, $dest.$file);
                         copy($source.$file, $dest.$file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            copy($source, $dest);
        }
}
*/



/*
$config = OW::getConfig();
if ( !$config->configExists('map', 'support_mobile_app') ){
    $config->addConfig('map', 'support_mobile_app', '0', '');
}
*/
/*
$errors=array();
try {
$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_scan` (
  `id` int(11) NOT NULL auto_increment,
  `id_owner` int(11) NOT NULL,
  `active` enum('0','1') collate utf8_bin NOT NULL default '1',
  `secret` varchar(64) collate utf8_bin NOT NULL,
  `hash_unique` varchar(32) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `hash_unique` (`hash_unique`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_scan_data` (
  `id_scan` int(11) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `source_post_type` enum('online','file') collate utf8_bin NOT NULL default 'online',
  `d_latitude` varchar(20) collate utf8_bin NOT NULL,
  `d_longitude` varchar(20) collate utf8_bin NOT NULL,
  `d_accuracy` varchar(10) collate utf8_bin default NULL,
  `d_altitude` varchar(20) collate utf8_bin default NULL,
  `d_provider` varchar(10) collate utf8_bin default NULL,
  `d_bearing` varchar(10) collate utf8_bin default NULL,
  `d_speed` varchar(10) collate utf8_bin default NULL,
  `d_time` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `d_battlevel` int(10) default NULL,
  `d_charging` int(2) default NULL,
  `d_deviceid` varchar(32) collate utf8_bin default NULL,
  `d_subscriberid` varchar(32) collate utf8_bin default NULL,
  `duplicate_times` int(11) NOT NULL default '0',
  UNIQUE KEY `allaaall` (`id_scan`,`d_latitude`,`d_longitude`,`d_altitude`,`id_owner`),
  KEY `id_scan` (`id_scan`),
  KEY `d_time` (`d_time`),
  KEY `id_owner` (`id_owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}


if ( !empty($errors) )
{
//    print_r($errors);
}
*/




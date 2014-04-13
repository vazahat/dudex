<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'map');

$config = OW::getConfig();
if ( !$config->configExists('map', 'show_owner') ){
    $config->addConfig('map', 'show_owner', '1', '');
}
if ( !$config->configExists('map', 'tabdisable_events') ){
    $config->addConfig('map', 'tabdisable_events', '0', '');
}


$source=OW_DIR_PLUGIN.'map'. DS.'static'. DS;
$pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.'map'.DS;


//echo $source;
//echo "<hr>";
//echo $pluginStaticDir;
//exit;

//CMS_BOL_Service::getInstance()->cpydir($source, $pluginStaticDir);
//echo "sss";exit;
cms_cpydir($source, $pluginStaticDir);
function cms_cpydir($source,$dest){
        if(is_dir($source)) {
            $dir_handle=opendir($source);
            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){
                    if(is_dir($source.$file)){

                        if (!is_dir($dest.$file.DS)) mkdir($dest.$file.DS);

                        cms_cpydir($source.$file.DS, $dest.$file.DS);
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





$errors=array();
try {
    $sql="ALTER TABLE  `".OW_DB_PREFIX."map` ADD  `id_cat` INT( 10 ) NOT NULL DEFAULT  '0' AFTER  `id_owner` ,ADD INDEX (  `id_cat` ) ";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql="CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_category` (
  `id` int(10) NOT NULL auto_increment,
  `id2` int(10) NOT NULL default '0',
  `active` enum('0','1') collate utf8_bin NOT NULL default '1',
  `name` varchar(100) collate utf8_bin NOT NULL,
  `name_translate` varchar(100) collate utf8_bin NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `id2` (`id2`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql="INSERT INTO  `".OW_DB_PREFIX."map_category` (
`id` ,
`id2` ,
`active` ,
`name` ,
`name_translate`
)
VALUES (
'0',  '0',  '1',  'Default',  'cat_default'
);";
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



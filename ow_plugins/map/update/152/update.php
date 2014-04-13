<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'map');




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


$errors=array();
try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `from_plugin`  `from_plugin` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT  'map';";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `tags`  `tags` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `city_name`  `city_name` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `lat`  `lat` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `type_promo`  `type_promo` ENUM(  'normal',  'promotion_todate',  'promotion_unlimited' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT  'normal';";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `oryginal_id`  `oryginal_id` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `oryginal_furl`  `oryginal_furl` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
    $sql = "ALTER TABLE  `".OW_DB_PREFIX."map` CHANGE  `active`  `active` ENUM(  '0',  '1' ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT  '1';";
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





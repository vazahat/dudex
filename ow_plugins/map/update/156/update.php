<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'map');

/*
BOL_ComponentAdminService::getInstance()->deleteWidget('MAP_CMP_IndexWidgetprofile');

$cmpService = BOL_ComponentAdminService::getInstance();
$widget = $cmpService->addWidget('MAP_CMP_IndexWidgetprofile');
$placeWidget = $cmpService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_PROFILE);
$cmpService->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_RIGHT,0);
*/


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
$sql = "ALTER TABLE  `".OW_DB_PREFIX."map` ADD  `slogan` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `desc` , ADD  `discount` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `slogan` , ADD  `price` DECIMAL( 10, 2 ) NULL DEFAULT NULL AFTER  `discount` , ADD INDEX (  `price` );";
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





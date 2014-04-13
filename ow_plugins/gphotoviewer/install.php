<?php
$config = OW::getConfig();
if ( !$config->configExists('gphotoviewer', 'enable_photo_viewer') )
{
    $config->addConfig('gphotoviewer', 'enable_photo_viewer', 1, 'Enable Photo Viewer');
}
if ( !$config->configExists('gphotoviewer', 'slideshow_time_per_a_photo') )
{
    $config->addConfig('gphotoviewer', 'slideshow_time_per_a_photo', 3, 'Slideshow time per a photo (sec)');
}
if ( !$config->configExists('gphotoviewer', 'can_users_to_download_photos') )
{
    $config->addConfig('gphotoviewer', 'can_users_to_download_photos', 1, 'Can users to download photos');
}
OW::getPluginManager()->addPluginSettingsRouteName('gphotoviewer', 'gphotoviewer.admin_config');
$path = OW::getPluginManager()->getPlugin('gphotoviewer')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'gphotoviewer');
?>
<?php
$config = OW::getConfig();
if ( !$config->configExists('gvideoviewer', 'enable_video_viewer') )
{
    $config->addConfig('gvideoviewer', 'enable_video_viewer', 1, 'Enable Video Viewer');
}

if ( !$config->configExists('gvideoviewer', 'can_users_to_get_embed_videos') )
{
    $config->addConfig('gvideoviewer', 'can_users_to_get_embed_videos', 1, 'Can users get embed videos');
}
OW::getPluginManager()->addPluginSettingsRouteName('gvideoviewer', 'gvideoviewer.admin_config');
$path = OW::getPluginManager()->getPlugin('gvideoviewer')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'gvideoviewer');
?>
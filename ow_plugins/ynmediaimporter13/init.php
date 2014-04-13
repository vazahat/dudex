<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('error_reporting', -1);

defined('YNMEDIAIMPORTER_DEBUG') or define('YNMEDIAIMPORTER_DEBUG', 0);
defined('YNMEDIAIMPORTER_SSID') or define('YNMEDIAIMPORTER_SSID', 'ynmediaimporter_ssid');
defined('YNMEDIAIMPORTER_PLATFORM') or define('YNMEDIAIMPORTER_PLATFORM', 'OW');
$configs =  OW::getConfig()->getValues('ynmediaimporter');
define('YNMEDIAIMPORTER_PER_PAGE', intval($configs['page']));
defined('YNMEDIAIMPORTER_SESSION_SPACE') or define('YNMEDIAIMPORTER_SESSION_SPACE', 'YNMEDIAIMPORTER');
defined('YNMEDIAIMPORTER_PROVIDER_PATH') or define('YNMEDIAIMPORTER_PROVIDER_PATH', OW::getPluginManager()->getPlugin('ynmediaimporter')->getRootDir() . 'provider');
defined('YNMEDIAIMPORTER_PLUGIN_PATH') or define('YNMEDIAIMPORTER_PLUGIN_PATH', OW::getPluginManager()->getPlugin('ynmediaimporter')->getRootDir());
defined('YNMEDIAIMPORTER_CENTRALIZE_HOST') or define('YNMEDIAIMPORTER_CENTRALIZE_HOST', 'http://openid.younetid.com/v2');

require_once YNMEDIAIMPORTER_PROVIDER_PATH . '/Service.php';

OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.index', 'media-importer', "YNMEDIAIMPORTER_CTRL_Importer", 'index') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.connect', 'media-importer/connect/:service', "YNMEDIAIMPORTER_CTRL_Importer", 'connect') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.disconnect', 'media-importer/disconnect/:service', "YNMEDIAIMPORTER_CTRL_Importer", 'disconnect') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.callback', 'media-importer/callback/:service', "YNMEDIAIMPORTER_CTRL_Importer", 'callback') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.getdata', 'media-importer/getdata', "YNMEDIAIMPORTER_CTRL_Importer", 'getdata') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.postimport', 'media-importer/postimport', "YNMEDIAIMPORTER_CTRL_Importer", 'postimport') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.check', 'media-importer/check', "YNMEDIAIMPORTER_CTRL_Importer", 'check') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.addphoto', 'media-importer/addphoto', "YNMEDIAIMPORTER_CTRL_Importer", 'addphoto') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.scheduler', 'media-importer/scheduler/:scheduler_id', "YNMEDIAIMPORTER_CTRL_Importer", 'scheduler') );

OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.facebook', 'media-importer/facebook', "YNMEDIAIMPORTER_CTRL_Facebook", 'index') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.flickr', 'media-importer/flickr', "YNMEDIAIMPORTER_CTRL_Flickr", 'index') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.picasa', 'media-importer/picasa', "YNMEDIAIMPORTER_CTRL_Picasa", 'index') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.instagram', 'media-importer/instagram', "YNMEDIAIMPORTER_CTRL_Instagram", 'index') );
//OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.yfrog', 'media-importer/yfrog', "YNMEDIAIMPORTER_CTRL_Yfrog", 'index') );

OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.admin_general', 'admin/media-importer/general', "YNMEDIAIMPORTER_CTRL_Admin", 'general') );
OW::getRouter()->addRoute(new OW_Route('ynmediaimporter.admin_providers', 'admin/media-importer/providers', "YNMEDIAIMPORTER_CTRL_Admin", 'providers') );

function ynmediaimporter_deactive()
{
	if (OW::getPluginManager() -> isPluginActive('ynsocialbridge') == false)
	{
		BOL_PluginService::getInstance()->deactivate('ynmediaimporter');
	}
}
OW::getEventManager()->bind(OW_EventManager::ON_APPLICATION_INIT, 'ynmediaimporter_deactive');


function ynmediaimporter_addAdminNotification(BASE_CLASS_EventCollector $e)
{
	$language = OW::getLanguage();

	if (OW::getPluginManager() -> isPluginActive('ynsocialbridge') == false)
	{
		$e->add($language->text('ynmediaimporter', 'requires_configuration_message'));
	}
}
OW::getEventManager()->bind('admin.add_admin_notification', 'ynmediaimporter_addAdminNotification');
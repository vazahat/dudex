<?php

/**
 * Here we can add new routes and autoload class rules, bind events etc.
 *
 * This file will be executed every time your plugin is active.
 */

//	init
$plugin = OW::getPluginManager() -> getPlugin('ynsocialconnect');

$key = strtoupper($plugin -> getKey());


//	back end
////	add router
$route = new OW_Route('ynsocialconnect_admin_settings', 'admin/plugins/ynsocialconnect/settings', "{$key}_CTRL_Admin", 'settings');
OW::getRouter() -> addRoute($route);
$route = new OW_Route('ynsocialconnect_admin_manage_providers', 'admin/plugins/ynsocialconnect/manage-providers', "{$key}_CTRL_Admin", 'manageProviders');
OW::getRouter() -> addRoute($route);
$route = new OW_Route('ynsocialconnect_admin_statistics', 'admin/plugins/ynsocialconnect/statistics', "{$key}_CTRL_Admin", 'statistics');
OW::getRouter() -> addRoute($route);

OW::getRouter()->addRoute(new OW_Route('ynsocialconnect-admin-ajaxUpdateProfileQuestion', 'admin/plugins/ynsocialconnect/ajax-update-profile-question', "{$key}_CTRL_Admin", 'ajaxUpdateProfileQuestion'));

$eventHandler = YNSOCIALCONNECT_CLASS_RequestEventHandler::getInstance();
$eventHandler -> init();

////	add router
OW::getRouter() -> addRoute(new OW_Route('ynsocialconnect-socialconnect', 'ynsocialconnect/social-connect/:service/:type', "{$key}_CTRL_SocialConnect", 'index'));
$route = new OW_Route('ynsocialconnect_user_sync', 'ynsocialconnect/user-sync/', "{$key}_CTRL_UserSync", 'index');
OW::getRouter() -> addRoute($route);
$route = new OW_Route('ynsocialconnect_user_user_linking', 'ynsocialconnect/userlinking/', "{$key}_CTRL_Userlinking", 'index');
OW::getRouter() -> addRoute($route);


$registry = OW::getRegistry();
$registry -> addToArray(BASE_CTRL_Join::JOIN_CONNECT_HOOK, array(
	new YNSOCIALCONNECT_CMP_ViewInJoinPage(),
	'render'
));

////	handle events
OW::getEventManager() -> bind(BASE_CMP_ConnectButtonList::HOOK_REMOTE_AUTH_BUTTON_LIST, array(
	$eventHandler,
	'onAddButton'
));
OW::getEventManager() -> bind(OW_EventManager::ON_USER_REGISTER, array(
	$eventHandler,
	'onUserRegister'
));
OW::getEventManager() -> bind(OW_EventManager::ON_USER_UNREGISTER, array(
	$eventHandler,
	'onUserUnregister'
));
OW::getEventManager() -> bind(OW_EventManager::ON_USER_LOGOUT, array(
	$eventHandler,
	'onUserLogout'
));
OW::getEventManager() -> bind('base.preference_menu_items', array(
	$eventHandler,
	'basePreferenceMenuItems'
));
OW::getEventManager() -> bind('admin.add_admin_notification', array(
	$eventHandler,
	'addAdminNotification'
));

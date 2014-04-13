<?php
$plugin = OW::getPluginManager()->getPlugin('ynsocialbridge');
$key = strtoupper($plugin->getKey());

//Admin Routs
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-admin', 'admin/socialbridge/facebook', "{$key}_CTRL_Admin", 'facebook'));
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-admin-twitter', 'admin/socialbridge/twitter', "{$key}_CTRL_Admin", 'twitter'));
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-admin-linkedin', 'admin/socialbridge/linkedin', "{$key}_CTRL_Admin", 'linkedin'));

//Frontend Routs
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-connects', 'socialbridge', "{$key}_CTRL_Socialbridge", 'index'));
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-connect-facebook', 'socialbridge/connect-facebook', "{$key}_CTRL_Socialbridge", 'connectFacebook'));
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-connect-twitter', 'socialbridge/connect-twitter', "{$key}_CTRL_Socialbridge", 'connectTwitter'));
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-connect-linkedin', 'socialbridge/connect-linkedin', "{$key}_CTRL_Socialbridge", 'connectLinkedin'));
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-disconnect', 'socialbridge/disconnect', "{$key}_CTRL_Socialbridge", 'disconnect'));

function socialbridge_preference_menu_item( BASE_EventCollector $event )
{
    $router = OW_Router::getInstance();
    $language = OW::getLanguage();

    $menuItems = array();

    $menuItem = new BASE_MenuItem();

    $menuItem->setKey('socialbridge');
    $menuItem->setLabel($language->text( 'ynsocialbridge', 'manage_socialbridge_label'));
    $menuItem->setIconClass('ow_ic_moderator');
    $menuItem->setUrl($router->urlForRoute('ynsocialbridge-connects'));
    $menuItem->setOrder(99);
    $event->add($menuItem);
}

OW::getEventManager()->bind('base.preference_menu_items', 'socialbridge_preference_menu_item');

function socialbridge_user_logout(OW_Event $event )
{
	if(!empty($_SESSION['socialbridge_session']) ) 
	{
		unset($_SESSION['socialbridge_session']);
	}
}
OW::getEventManager()->bind(OW_EventManager::ON_USER_LOGOUT, 'socialbridge_user_logout');

function socialbridge_add_admin_notification( BASE_CLASS_EventCollector $e )
{
    $language = OW::getLanguage();
	$fbConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig('facebook');
	$twConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig('twitter');
	$liConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig('linkedin');
   
    if ( !$fbConfig || !$twConfig || !$liConfig)
    {
        $e->add($language->text('ynsocialbridge', 'requires_configuration_message', array( 'settingsUrl' => OW::getRouter()->urlForRoute('ynsocialbridge-admin'))));
    }
}
OW::getEventManager()->bind('admin.add_admin_notification', 'socialbridge_add_admin_notification');

function ynsocialbridge_add_auth_labels( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(
        array(
            'ynsocialbridge' => array(
                'label' => $language->text('ynsocialbridge', 'auth_group_label'),
                'actions' => array(
                )
            )
        )
    );
}
OW::getEventManager()->bind('admin.add_auth_labels', 'ynsocialbridge_add_auth_labels');

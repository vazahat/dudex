<?php
define('YNSOCIALPUBLISHER_SESSION_DATA', 'ynsocialpublisher_session_key');

//$plugin = OW::getPluginManager()->getPlugin('ynsocialpublisher');

// route for admin
OW::getRouter()->addRoute(new OW_Route('ynsocialpublisher.admin', 'admin/plugins/ynsocialpublisher', "YNSOCIALPUBLISHER_CTRL_Admin", 'index'));
// route for socialbridge - scocial publisher settings page
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-publisher-settings', 'socialbridge/publisher', 'YNSOCIALPUBLISHER_CTRL_Ynsocialpublisher', 'index'));

function ynsocialpublisher_deactive()
{
	if (OW::getPluginManager() -> isPluginActive('ynsocialbridge') == false)
	{
		BOL_PluginService::getInstance()->deactivate('ynsocialpublisher');
	}
	else 
	{
		$build = BOL_PluginService::getInstance()->findPluginByKey('ynsocialbridge')->build;
		if ($build < 2)
		{
			BOL_PluginService::getInstance()->deactivate('ynsocialpublisher');
		}		
	}
}
OW::getEventManager()->bind(OW_EventManager::ON_APPLICATION_INIT, 'ynsocialpublisher_deactive');

function ynsocialpublisher_addAdminNotification(BASE_CLASS_EventCollector $e)
{		
	$language = OW::getLanguage();
	
	if (OW::getPluginManager() -> isPluginActive('ynsocialbridge') == false)
	{
		$e->add($language->text('ynsocialpublisher', 'requires_configuration_message'));
	}	
	else
	{
		$build = BOL_PluginService::getInstance()->findPluginByKey('ynsocialbridge')->build;
		if ($build < 2)
		{
			$e->add($language->text('ynsocialpublisher', 'requires_configuration_message'));
		}
	}
}

OW::getEventManager()->bind('admin.add_admin_notification', 'ynsocialpublisher_addAdminNotification');
// add script
function ynsocialpublisher_jquery_cookie_declarations( $e )
{
    $plugin = OW::getPluginManager()->getPlugin('ynsocialpublisher');
    OW::getDocument()->addScript($plugin->getStaticJsUrl() . 'jquery_cookie.js');
    OW::getDocument()->addScript($plugin->getStaticJsUrl() . 'core.js');
}
OW::getEventManager()->bind(OW_EventManager::ON_FINALIZE, 'ynsocialpublisher_jquery_cookie_declarations');

function ynsocialpublisher_on_entity_add(OW_Event $event)
{
    $params = $event->getParams();
    $data = $event->getData();


    $pluginKey = $params['pluginKey'];
    $entityType = $params['entityType'];
    $entityId = $params['entityId'];
    $userId = $params['userId'];

    $service = YNSOCIALPUBLISHER_BOL_Service::getInstance();
    $userSetting = $service->getUsersetting($userId, $pluginKey);
    $supportedPluginType = YNSOCIALPUBLISHER_CLASS_Core::getInstance()->getTypesByPluginKey($pluginKey);
    $count = 0;
    foreach (array('facebook', 'twitter', 'linkedin') as $serviceName)
    {
    	if (YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($serviceName))
    	{
			$count++;    		
    	}
    }
    
    /*
     * Must check:
     * _ Setting is available
     * _ At least one provider is available
     * _ Option is not 'Do not ask me again' (2)
     */

    if (!empty($userSetting)
            && (count($userSetting['providers']) > 0)
            && (in_array($entityType, $supportedPluginType))
    		&& $count)
    {
        $providers = $userSetting['providers'];

        if ($userSetting['option'] == YNSOCIALPUBLISHER_BOL_UsersettingDao::OPTIONS_ASK)
        {
            if ($pluginKey == 'newsfeed')
            {
                $params = implode(';',array($entityId, $userId));
                setcookie('ynsocialpublisher_feed_data_' . $entityId, $params, time() + 300, '/');
            }
            else
            {
                $_SESSION[YNSOCIALPUBLISHER_SESSION_DATA] = implode(';',array($pluginKey, $entityType, $entityId));
            }
        }
        elseif ($userSetting['option'] == YNSOCIALPUBLISHER_BOL_UsersettingDao::OPTIONS_AUTO)
        {
            // auto publish to selected providers
            $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();
            $language = OW::getLanguage();
            $status = $core->getDefaultStatus($pluginKey, $entityType, $entityId);
            $postData = $core->getPostData($pluginKey, $entityId, $entityType, $providers, $status);
            $coreBridge = new YNSOCIALBRIDGE_CLASS_Core();
			
            foreach ($providers as $provider)
            {
				$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($provider);
        		if ($clientConfig && isset($postData[$provider]))
				{
	                $obj = $coreBridge -> getInstance($provider);
	                try
	                {
	                    $obj->postActivity($postData[$provider]);
	                } catch(Exception $e) {
	                    //echo $e->getMessage();
	                }
				}
            }
        }
    }

}

OW::getEventManager()->bind('feed.on_entity_add', 'ynsocialpublisher_on_entity_add');

function ynsocialpublisher_show_popup( OW_Event $event )
{
    if(OW::getRequest()->isAjax())
    {
        return ;
    }

    $params = $event->getParams();
    $eventData = $event->getData();

    $sessionData = isset($_SESSION[YNSOCIALPUBLISHER_SESSION_DATA])?$_SESSION[YNSOCIALPUBLISHER_SESSION_DATA]:'';

    if (empty($sessionData))
    {
        return;

    }

    //setcookie('xpsd', $sessionData, time() + 300, '/');

    unset($_SESSION[YNSOCIALPUBLISHER_SESSION_DATA]);

    list($pluginKey, $entityType, $entityId) = explode(';', $sessionData);

    $script = "OW.ajaxFloatBox('YNSOCIALPUBLISHER_CMP_Popup', {pluginKey :'$pluginKey', entityType: '$entityType', entityId: $entityId}, {width:620, height:560, iconClass: 'ow_ic_user', title: ''});";
    OW::getDocument()->addOnloadScript($script);

}

OW::getEventManager()->bind(OW_EventManager::ON_AFTER_ROUTE, 'ynsocialpublisher_show_popup');




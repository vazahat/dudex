<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

OW::getRouter()->addRoute(new OW_Route('profileprogressbar.admin', 'admin/profileprogressbar', 'PROFILEPROGRESSBAR_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('profileprogressbar.admin_features', 'admin/profileprogressbar/features', 'PROFILEPROGRESSBAR_CTRL_Admin', 'features'));
OW::getRouter()->addRoute(new OW_Route('profileprogressbar.admin_hint', 'admin/profileprogressbar/hint', 'PROFILEPROGRESSBAR_CTRL_Admin', 'hint'));

function profileprogressbar_after_route( OW_Event $event )
{
    $handler = OW::getRequestHandler()->getHandlerAttributes();
    
    if ( $handler[OW_RequestHandler::ATTRS_KEY_CTRL] == 'BASE_CTRL_Edit' && $handler[OW_RequestHandler::ATTRS_KEY_ACTION] == 'index' )
    {
        OW::getRegistry()->addToArray(BASE_CTRL_Edit::EDIT_SYNCHRONIZE_HOOK, array(new PROFILEPROGRESSBAR_CMP_Synchronize(), 'render'));
    }
}
OW::getEventManager()->bind(OW_EventManager::ON_AFTER_ROUTE, 'profileprogressbar_after_route');

function profileprogressbar_feed_action( OW_Event $event )
{
    $params = $event->getParams();
    
    if ( !in_array($params['entityType'], PROFILEPROGRESSBAR_BOL_Service::getEntityTypes()) )
    {
        return;
    }
    
    $log = new PROFILEPROGRESSBAR_BOL_ActivityLog();
    
    switch ( $params['entityType'] )
    {
        case PROFILEPROGRESSBAR_BOL_Service::ENTITY_TYPE_FRIEND:
            $log->userId = (int)$params['userId'][0];
        
            $_log = new PROFILEPROGRESSBAR_BOL_ActivityLog();
            $_log->userId = (int)$params['userId'][1];
            $_log->entityType = $params['entityType'];
            $_log->timeStamp = time();
            $_log->entityId = $params['entityId'];

            PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance()->save($_log);
            break;
        case PROFILEPROGRESSBAR_BOL_Service::ENTITY_TYPE_GROUPS:
        case PROFILEPROGRESSBAR_BOL_Service::ENTITY_TYPE_GIFT:
            $log->userId = OW::getUser()->getId();
            break;
        default:
            $log->userId = (int)$params['userId'];
            break;
    }
    
    $log->entityType = $params['entityType'];
    $log->timeStamp = time();
    $log->entityId = $params['entityId'];
    
    PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance()->save($log);
}
OW::getEventManager()->bind('feed.action', 'profileprogressbar_feed_action');

function profileprogressbar_delete_item( OW_Event $event )
{
    $params = $event->getParams();
    
    if ( !in_array($params['entityType'], PROFILEPROGRESSBAR_BOL_Service::getEntityTypes()) || empty($params['entityId']) )
    {
        return;
    }

    $log = PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance()->findCompletedLog($params['entityType'], $params['entityId']);
    
    if ( !empty($log) && $log->userId == OW::getUser()->getId() )
    {
        PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance()->deleteById($log->id);
    }
}
OW::getEventManager()->bind('feed.delete_item', 'profileprogressbar_delete_item');

function profileprogressbar_cancelled( OW_Event $event )
{
    $params = $event->getParams();

    PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance()->deleteCompletedFriendLog($params['recipientId']);
}
OW::getEventManager()->bind('friends.cancelled', 'profileprogressbar_cancelled');

function profile_progressbar_event_on_delete_event( OW_Event $event )
{
    $params = $event->getParams();
    
    PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance()->deleteCompletedEventLog(OW::getUser()->getId(), $params['eventId']);
}
OW::getEventManager()->bind('event_on_delete_event', 'profile_progressbar_event_on_delete_event');

function profile_progressbar_collect_info_config( OW_Event $event )
{
    $params = $event->getParams();
        
    if ( in_array($params['line'],  array(HINT_BOL_Service::INFO_LINE0, HINT_BOL_Service::INFO_LINE1, HINT_BOL_Service::INFO_LINE2)) )
    {
        $event->add(
            array(
                'key' => 'profileprogressbar',
                'label' => OW::getLanguage()->text('profileprogressbar', 'user_hint_caption')
            )
        );
    }
}
OW::getEventManager()->bind('hint.collect_info_config', 'profile_progressbar_collect_info_config');

function profile_progressbar_info_preview( OW_Event $event )
{
    $params = $event->getParams();
        
    if ( $params['key'] != 'profileprogressbar' )
    {
        return;
    }
    
    $event->setData('<div class="profile-progressbar">
        <span class="profile-progressbar-caption">36%</span>
        <div class="profile-progressbar-complete" style="width: 36%;"></div>
    </div>');
}
OW::getEventManager()->bind('hint.info_preview', 'profile_progressbar_info_preview');

function profile_progressbar_info_render( OW_Event $event )
{
    $params = $event->getParams();
        
    if ( $params['key'] != 'profileprogressbar' )
    {
        return;
    }

    $hint = new PROFILEPROGRESSBAR_CMP_Hint($params['entityId']);
    
    $event->setData($hint->render());
}
OW::getEventManager()->bind('hint.info_render', 'profile_progressbar_info_render');

function profile_progressbar_finalize( OW_Event $event )
{
    if ( OW::getPluginManager()->isPluginActive('hint') )
    {
        $theme = OW::getConfig()->getValue('profileprogressbar', 'theme');
        $plugin = OW::getPluginManager()->getPlugin('profileprogressbar');
        $document = OW::getDocument();
        
        $document->addStyleSheet($plugin->getStaticCssUrl() . $theme . '.css');
        $document->addScript($plugin->getStaticJsUrl() . 'jquery-ui.custom.min.js');
    }
}
OW::getEventManager()->bind(OW_EventManager::ON_FINALIZE, 'profile_progressbar_finalize');

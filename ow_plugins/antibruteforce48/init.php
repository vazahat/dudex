<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

OW::getRouter()->addRoute( new OW_Route('antibruteforce.admin', 'antibruteforce/admin', 'ANTIBRUTEFORCE_CTRL_Admin', 'index') );
OW::getRouter()->addRoute( new OW_Route('antibruteforce.authenticate_fail', 'antibruteforce/lock', 'ANTIBRUTEFORCE_CTRL_Antibruteforce', 'index') );

$eventManager = OW::getEventManager();

function antibruteforce_core_after_route( OW_Event $event )
{
    if ( OW::getUser()->isAuthenticated() )
    {
        return;
    }
    
    $classDir = OW::getPluginManager()->getPlugin( 'antibruteforce' )->getClassesDir();
    $handler = OW::getRequestHandler()->getHandlerAttributes();
    
    if ( OW::getConfig()->getValue('antibruteforce', 'authentication') )
    {
        include_once $classDir . 'sign_in.php';
        include_once $classDir . 'auth_result.php';
    }
    
    if ( OW::getConfig()->getValue('antibruteforce', 'registration') )
    {
        if ( $handler[OW_RequestHandler::ATTRS_KEY_CTRL] == 'BASE_CTRL_Join' && $handler[OW_RequestHandler::ATTRS_KEY_ACTION] == 'index' )
        {
            OW::getEventManager()->bind( OW_EventManager::ON_FINALIZE, 'antibruteforce_core_finalize' );
        }
        else if ( $handler[OW_RequestHandler::ATTRS_KEY_CTRL] == 'BASE_CTRL_Captcha' && $handler[OW_RequestHandler::ATTRS_KEY_ACTION] == 'ajaxResponder' )
        {
            include_once $classDir . 'captcha.php';
        }
    }
    
    if ( $handler[OW_RequestHandler::ATTRS_KEY_CTRL] != 'ANTIBRUTEFORCE_CTRL_Antibruteforce' )
    {
        if ( ANTIBRUTEFORCE_BOL_Service::getInstance()->isLocked() )
        {
            ANTIBRUTEFORCE_BOL_Service::getInstance()->redirect();
        }
    }
}
$eventManager->bind( OW_EventManager::ON_AFTER_ROUTE, 'antibruteforce_core_after_route' );

$eventManager->bind( ANTIBRUTEFORCE_BOL_Service::EVENT_AUTHENTICATE_FAIL, array(ANTIBRUTEFORCE_BOL_Service::getInstance(), 'bruteforceTrack') );
$eventManager->bind( 'base.bot_detected', array(ANTIBRUTEFORCE_BOL_Service::getInstance(), 'bruteforceTrack') );

function antibruteforce_successfully( OW_Event $event )
{
    OW::getSession()->delete( ANTIBRUTEFORCE_BOL_Service::SESSION_NAME );
}
$eventManager->bind( OW_EventManager::ON_USER_LOGIN, 'antibruteforce_successfully' );

function antibruteforce_core_finalize( OW_Event $event )
{
    $javaScripts = OW::getDocument()->getJavaScripts();
    
    foreach ( $javaScripts['items'][1000]['text/javascript'] as $key => $script )
    {
        if ( strpos($script, '/ow_static/plugins/base/js/captcha.js') !== false )
        {
            $javaScripts['items'][1000]['text/javascript'][$key] = OW::getPluginManager()->getPlugin( 'antibruteforce' )->getStaticJsUrl() . 'captcha.js';
            OW::getDocument()->setJavaScripts( $javaScripts );
            
            break;
        }
    }
}

function antibruteforce_base_splash_screen_exceptions( BASE_CLASS_EventCollector $event )
{
    $event->add( array(OW_RequestHandler::ATTRS_KEY_CTRL => 'ANTIBRUTEFORCE_CTRL_Antibruteforce', OW_RequestHandler::ATTRS_KEY_ACTION => 'index') );
}
$eventManager->bind( 'base.splash_screen_exceptions', 'antibruteforce_base_splash_screen_exceptions' );
$eventManager->bind( 'base.members_only_exceptions', 'antibruteforce_base_splash_screen_exceptions' );

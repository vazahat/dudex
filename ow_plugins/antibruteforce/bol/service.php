<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * 
 *
 * @author Kairat Bakytow <kainisoft@gmail.com>
 * @package ow_plugins.antibruteforce.bol
 * @since 1.0
 */
class ANTIBRUTEFORCE_BOL_Service
{
    CONST TRY_COUNT = 'try_count';
    CONST EXPIRE_TIME = 'expire_time';
    
    CONST EVENT_AUTHENTICATE_FAIL = 'antibruteforce.authenticate_fail';
    CONST SESSION_NAME = 'antibrutefore_try_count';
    
    private static $classInstance;
    
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    private $blockIpDao;
    
    private function __construct()
    {
        $this->blockIpDao = ANTIBRUTEFORCE_BOL_BlockIpDao::getInstance();
    }
    
    public function bruteforceTrack()
    {
        $try_count = (int)OW::getSession()->get( self::SESSION_NAME );
        
        if ( ++$try_count >= OW::getConfig()->getValue('antibruteforce', self::TRY_COUNT) )
        {
            $this->addBlockIp();
            $this->redirect();
        }
        
        OW::getSession()->set( self::SESSION_NAME, $try_count );
    }
    
    public function addBlockIp()
    {
        $this->blockIpDao->addBlockIp();
    }

    public function isLocked()
    {
        return $this->blockIpDao->isLocked();
    }
    
    public function deleteBlockIp()
    {
        return $this->blockIpDao->deleteBlockIp();
    }
    
    public function redirect()
    {
        if ( OW::getRequest()->isAjax() )
        {
            $handler = OW::getRequestHandler()->getHandlerAttributes();
            
            if ( $handler[OW_RequestHandler::ATTRS_KEY_CTRL] == 'BASE_CTRL_User' && $handler[OW_RequestHandler::ATTRS_KEY_ACTION] == 'ajaxSignIn' )
            {
                OW::getSession()->delete( ANTIBRUTEFORCE_BOL_Service::SESSION_NAME );
                
                exit( json_encode(array('result' => TRUE, 'message' => '')) );
            }
            else if ( $handler[OW_RequestHandler::ATTRS_KEY_CTRL] == 'BASE_CTRL_Captcha' && $handler[OW_RequestHandler::ATTRS_KEY_ACTION] == 'ajaxResponder' )
            {
                OW::getSession()->delete( ANTIBRUTEFORCE_BOL_Service::SESSION_NAME );
                
                exit ( json_encode(array('result' => FALSE, 'reload' => OW::getRouter()->urlForRoute('antibruteforce.authenticate_fail'))) );
            }
            else if ( $handler[OW_RequestHandler::ATTRS_KEY_CTRL] == 'SMARTCAPTCHA_CTRL_SmartCaptcha' && $handler[OW_RequestHandler::ATTRS_KEY_ACTION] == 'ajaxResponder' )
            {
                OW::getSession()->delete( ANTIBRUTEFORCE_BOL_Service::SESSION_NAME );
                
                exit ( json_encode(array('result' => FALSE, 'reload' => OW::getRouter()->urlForRoute('antibruteforce.authenticate_fail'))) );
            }
        }
        else
        {
            OW::getSession()->delete( ANTIBRUTEFORCE_BOL_Service::SESSION_NAME );
            
            UTIL_Url::redirect( OW::getRouter()->urlForRoute('antibruteforce.authenticate_fail') );
        }
    }
}

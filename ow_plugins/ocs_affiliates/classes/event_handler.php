<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate event handler
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.classes
 * @since 1.5.3
 */
class OCSAFFILIATES_CLASS_EventHandler
{
    /**
     * Singleton instance.
     *
     * @var OCSAFFILIATES_CLASS_EventHandler
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OCSAFFILIATES_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var OCSAFFILIATES_BOL_Service
     */
    private $service;

    private function __construct()
    {
        $this->service = OCSAFFILIATES_BOL_Service::getInstance();
    }


    public function init()
    {
        $this->genericInit();
    }

    public function genericInit()
    {
        $em = OW::getEventManager();

        $em->bind(OW_EventManager::ON_USER_REGISTER, array($this, 'onUserRegister'));
        $em->bind(OW_EventManager::ON_USER_UNREGISTER, array($this, 'onUserUnregister'));
        $em->bind(OW_EventManager::ON_PLUGINS_INIT, array($this, 'affiliateSystemEntry'));
    }

    public function affiliateSystemEntry()
    {
        if ( defined('OW_CRON') )
        {
            return;
        }

        if ( !OW::getRequest()->isPost() && !OW::getRequest()->isAjax() )
        {
            $this->service->catchAffiliateVisit();
        }
    }

    public function onUserRegister( OW_Event $event )
    {
        $params = $event->getParams();
        $userId = $params['userId'];

        $this->service->catchAffiliateSignup($userId);
    }

    public function onUserUnregister( OW_Event $event )
    {
        $params = $event->getParams();
        $userId = $params['userId'];


        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $service->deleteAffiliateUserByUserId($userId);
    }
}
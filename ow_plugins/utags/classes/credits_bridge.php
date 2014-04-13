<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.classes
 */
class UTAGS_CLASS_CreditsBridge
{
    /**
     * Singleton instance.
     *
     * @var UTAGS_CLASS_CreditsBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return UTAGS_CLASS_CreditsBridge
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
     * @var UTAGS_CLASS_Credits
     */
    public $credits;

    /**
     *
     * @var OW_Plugin
     */
    private $plugin;

    private function __construct()
    {
        $this->credits = new UTAGS_CLASS_Credits();
        $this->plugin = OW::getPluginManager()->getPlugin('utags');
    }

    public function onTagAdd( OW_Event $e )
    {
        $this->credits->trackUse(UTAGS_CLASS_Credits::ACTION_TAG_PHOTO);
    }

    public function getAllPermissions()
    {
        $out = array();

        foreach ( $this->credits->allActions as $action )
        {
            $out[$action] = $this->credits->isAvaliable($action);
        }

        return $out;
    }

    public function getAllPermissionMessages()
    {
        $out = array();

        foreach ( $this->credits->allActions as $action )
        {
            $out[$action] = $this->credits->getErrorMessage($action);
        }

        return $out;
    }

    public function genericInit()
    {
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_AFTER_ADD, array($this, 'onTagAdd'));
    }
    
    public function init()
    {
        $this->genericInit();
        
        $this->credits->triggerCreditActionsAdd();
        OW::getEventManager()->bind('usercredits.on_action_collect', array($this->credits, 'bindCreditActionsCollect'));
    }
}
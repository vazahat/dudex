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
class UTAGS_CLASS_PrivacyBridge
{

    const ACTION_VIEW_TAGS = 'view-tags';
    const ACTION_TAG_MY_PHOTO = 'tag-my-photo';

    /**
     * Class instance
     *
     * @var UTAGS_CLASS_PrivacyBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return UTAGS_CLASS_PrivacyBridge
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private $actions = array();
    
    /**
     *
     * @var OW_Plugin
     */
    private $plugin;

    public function __construct()
    {
        $this->plugin = OW::getPluginManager()->getPlugin('utags');
        
        $this->actions = array(
            self::ACTION_TAG_MY_PHOTO,
            self::ACTION_VIEW_TAGS
        );
    }
    
    public function onCollectList( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();

        $action = array(
            'key' => self::ACTION_VIEW_TAGS,
            'pluginKey' => $this->plugin->getKey(),
            'label' => $language->text($this->plugin->getKey(), 'privacy_action_view_tags'),
            'description' => '',
            'defaultValue' => 'everybody'
        );

        $event->add($action);
        
        $action = array(
            'key' => self::ACTION_TAG_MY_PHOTO,
            'pluginKey' => $this->plugin->getKey(),
            'label' => $language->text($this->plugin->getKey(), 'privacy_action_tag_my_photo'),
            'description' => '',
            'defaultValue' => 'everybody'
        );

        $event->add($action);
    }

    public function checkPrivacy( $action, $userId )
    {
        $eventParams = array(
            'action' => $action,
            'ownerId' => $userId,
            'viewerId' => OW::getUser()->getId()
        );

        try
        {
            OW::getEventManager()->getInstance()->call('privacy_check_permission', $eventParams);
        }
        catch ( RedirectException $e )
        {
            return false;
        }

        return true;
    }
    
    public function getAllPermissions( $userId )
    {
        $out = array();
        foreach ( $this->actions as $action )
        {
            $out[$action] = $this->checkPrivacy($action, $userId);
        }
        
        return $out;
    }

    public function genericInit()
    {
        OW::getEventManager()->bind('plugin.privacy.get_action_list', array($this, 'onCollectList'));
    }
    
    public function init()
    {
        $this->genericInit();
    }
}
<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package uheader.classes
 */
class UHEADER_CLASS_EventHandler
{
    const API_VERSION = 2;
    
    /**
     * Class instance
     *
     * @var UHEADER_CLASS_EventHandler
     */
    protected static $classInstance;

    /**
     * Returns class instance
     *
     * @return UHEADER_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var UHEADER_BOL_Service
     */
    protected $service;
    
    protected function __construct()
    {
        $this->service = UHEADER_BOL_Service::getInstance();
    }
    
    public function onAuthLabelsCollect( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();
        $event->add(
            array(
                'uheader' => array(
                    'label' => $language->text('uheader', 'auth_group_label'),
                    'actions' => array(
                        'view_cover' => $language->text('uheader', 'auth_action_view_cover'),
                        'add_cover' => $language->text('uheader', 'auth_action_add_cover'),
                        'add_comment' => $language->text('uheader', 'auth_action_label_add_comment'),
                        'delete_comment_by_content_owner' => $language->text('uheader', 'auth_action_label_delete_comment_by_content_owner')
                    )
                )
            )
        );
    }
    
    public function onGetClassInstance( OW_Event $event )
    {
        $params = $event->getParams();

        if ( $params['className'] != 'BASE_CMP_ProfileActionToolbar' )
        {
            return;
        }

        $arguments = $params['arguments'];
        $cmp = new UHEADER_CMP_ProfileActionToolbarMock($arguments[0]);
        $event->setData($cmp);

        return $cmp;
    }
    
    public function getCover( OW_Event $event )
    {
        $params = $event->getParams();
        $userId = $params["userId"];
        $forWidth = empty($params["forWidth"]) ? null : $params["forWidth"];
        
        $permited = UHEADER_CLASS_PrivacyBridge::getInstance()->checkPrivacy($userId);
        
        if ( !$permited )
        {
            return null;
        }
        
        $cover = $this->service->findCoverByUserId($userId, UHEADER_BOL_Cover::STATUS_ACTIVE);
        if ( $cover === null )
        {
            $cover = $this->service->findDefaultTemplateForUser($userId);
        }
        
        if ( $cover === null )
        {
            return null;
        }
        
        $out = array(
            "userId" => $userId,
            "src" => $cover->getSrc(),
            "data" => $cover->getSettings(),
            "canvas" => $cover->getCanvas($forWidth),
            "css" => $cover->getCss(),
            "cssString" => $cover->getCssString()
        );
        
        $event->setData($out);
        return $out;
    }
    
    public function getVersion( OW_Event $event )
    {
        $event->setData(self::API_VERSION);
        
        return self::API_VERSION;
    }
    
    public function genericInit()
    {
        OW::getEventManager()->bind('uheader.get_cover', array($this, 'getCover'));
        OW::getEventManager()->bind('uheader.get_version', array($this, 'getVersion'));
    }
    
    public function init()
    {
        $this->genericInit();
        
        OW::getEventManager()->bind('admin.add_auth_labels', array($this, 'onAuthLabelsCollect'));
        OW::getEventManager()->bind('class.get_instance', array($this, 'onGetClassInstance'));
    }
}
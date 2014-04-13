<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.classes
 */
class EQUESTIONS_CLASS_GroupsBridge
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_GroupsBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_GroupsBridge
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function isActive()
    {
        return OW::getPluginManager()->isPluginActive('groups');
    }

    public function onCheckInteractPermission( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        $settings = $params['settings'];

        if ( empty($settings['context']['type']) || $settings['context']['type'] != 'groups' )
        {
            return;
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            return;
        }

        if ( !$this->isActive() )
        {
            return;
        }

        $groupId = (int) $settings['context']['id'];

        $event->setData( $data && GROUPS_BOL_Service::getInstance()->findUser($groupId, OW::getUser()->getId()) !== null );
    }

    public function onBeforeQuestionAdd( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();
        if ( empty($params['settings']['context']['type']) || $params['settings']['context']['type'] != 'groups' )
        {
            return;
        }

        if ( !$this->isActive() )
        {
            return;
        }

        $context = $params['settings']['context'];

        $service = GROUPS_BOL_Service::getInstance();

        $groupId = (int) $context['id'];
        $group = $service->findGroupById($groupId);
        $url = $service->getGroupUrl($group);
        $title = UTIL_String::truncate(strip_tags($group->title), 100, '...');

        $context['label'] = $title;
        $context['url'] = $url;

        $data['settings']['context'] = $context;
        $data['privacy'] = 'groups';

        $event->setData($data);
    }
}
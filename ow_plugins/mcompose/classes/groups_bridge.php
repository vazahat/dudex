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
 * @package mcompose.classes
 */
class MCOMPOSE_CLASS_GroupsBridge
{
    const ID_PREFIX = "group";

    /**
     * Class instance
     *
     * @var MCOMPOSE_CLASS_GroupsBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return MCOMPOSE_CLASS_GroupsBridge
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function __construct()
    {
        
    }
    
    public function isActive()
    {
        return OW::getPluginManager()->isPluginActive("groups");
    }
    
    public function isEnabled()
    {
        return OW::getConfig()->getValue("mcompose", "groups_enabled");
    }
    
    public function findGroups( $kw, $userId, $limit = null )
    {
        $groupDao = GROUPS_BOL_GroupDao::getInstance();
        $groupUserDao = GROUPS_BOL_GroupUserDao::getInstance();
        
        $params = array(
            'u' => $userId
        );
        
        if ( !empty($kw) )
        {
            $params["kw"] = "%" . $kw . "%";
        }
        
        $limitStr = $limit === null ? '' : 'LIMIT 0, ' . intval($limit);
        $kwWhere = empty($kw) ? "1" : "g.title LIKE :kw";
        
        $query = "SELECT DISTINCT g.* FROM " . $groupDao->getTableName() . " g
            INNER JOIN " . $groupUserDao->getTableName() . " u ON g.id = u.groupId AND u.id != :u
            WHERE g.userId=:u AND $kwWhere " . $limitStr;

        return OW::getDbo()->queryForObjectList($query, $groupDao->getDtoClassName(), $params);
    }
    
    public function onSearch( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        
        $kw = $params["kw"];
        $userId = $params["userId"];
        $recipients = $params["recipients"];
        //$context = $params["context"];
        
        $groups = array();
        
        if ( $kw === null && $params["context"] == MCOMPOSE_BOL_Service::CONTEXT_EVENT )
        {
            $groups = $this->findGroups(null, $userId);
        }
        else
        {
            $groups = $this->findGroups($kw, $userId);
        }
        
        if ( !empty($recipients) ) 
        {   
            $rIds = array();
            foreach ( $recipients as $r )
            {
                list($prefix, $id) = explode("_", $r);
            
                if ( $prefix == self::ID_PREFIX )
                {
                    $rIds[] = $id;
                }
            }
            
            $_groups = GROUPS_BOL_GroupDao::getInstance()->findByIdList($rIds);
            $groups = array_merge($groups, $_groups);
        }
        
        $data = $this->buildData($groups, OW::getLanguage()->text('mcompose', 'selector_group_my_groups'));
        
        foreach ( $data as $item )
        {
            $event->add($item);
        }
    }
    
    public function onInputInit( OW_Event $event )
    {
        $params = $event->getParams();
        
        $userId = $params["userId"];
        /* @var $input MCOMPOSE_CLASS_UserSelectField */
        $input = $params["input"];
        
        if ( $params["context"] != MCOMPOSE_BOL_Service::CONTEXT_GROUP )
        {
            return;
        }
        
        $group = OW::getLanguage()->text('mcompose', 'selector_group_my_groups');
        OW::getLanguage()->addKeyForJs('mcompose', 'selector_group_my_groups');
        
        $input->setupGroup($group, array(
            'priority' => 11,
            'alwaysVisible' => true,
            'noMatchMessage' => false
        ));
    }
    
    protected function buildData( $groups, $itemsGroup = null )
    {
        if ( empty($groups) )
        {
            return array();
        }
        
        $groupIds = array();
        foreach ( $groups as $g )
        {
            $groupIds[] = $g->id;
        }
        
        $service = GROUPS_BOL_Service::getInstance();
        $userCounts = $service->findUserCountForList($groupIds);
        
        $out = array();

        foreach ( $groups as $group )
        {
            /* @var $group GROUPS_BOL_Group */
            
            $data = array();
            $data['id'] = $group->id;
            
            $data['url'] = $service->getGroupUrl($group);
            $data['avatar'] = $service->getGroupImageUrl($group);
            $data['text'] = $group->title;
            $data['info'] = OW::getLanguage()->text("mcompose", "selector_group_item_info", array(
                "usersCount" => $userCounts[$group->id]
            ));
            
            $itemCmp = new MCOMPOSE_CMP_GroupItem($data);
            
            $item = array();
            $item["id"] = self::ID_PREFIX . "_" . $group->id;
            $item["text"] = $data['text'];
            $item["url"] = $data['url'];
            $item['html'] = $itemCmp->render();
            $item['count'] = $userCounts[$group->id];

            if ( !empty($itemsGroup) ) {
                $item['group'] = $itemsGroup;
            }

            $out[self::ID_PREFIX . '_' . $group->id] = $item;
        }

        return $out;
    }
    
    public function onSend( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        $recipients = $params["recipients"];
        
        foreach ( $recipients as $r )
        {
            list($prefix, $id) = explode("_", $r);
            
            if ( $prefix != self::ID_PREFIX )
            {
                continue;
            }
            
            $userIds = GROUPS_BOL_Service::getInstance()->findGroupUserIdList($id);
            
            foreach ( $userIds as $userId )
            {
                if ( $userId != OW::getUser()->getId() )
                {
                    $event->add($userId);
                }
            }
        }
    }
    
    public function collectToolbar( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        $groupId = $params["groupId"];
        
        $group = GROUPS_BOL_Service::getInstance()->findGroupById($groupId);
        
        if ( empty($group) || $group->userId != OW::getUser()->getId() )
        {
            return false;
        }
        
        $uniqId = uniqid("mcompose-");
        
        $recipients = array(self::ID_PREFIX . "_" . $groupId);
        
        $href = OW::getRouter()->urlForRoute("mcompose-index");
        $href = OW::getRequest()->buildUrlQueryString($href, array(
            "recipients" => $recipients,
            "context" => MCOMPOSE_BOL_Service::CONTEXT_GROUP
        ));
        
        $toolbar = array(
            "label" => OW::getLanguage()->text("mcompose", "groups_send_message"),
            "href" => $href,
            "id" => $uniqId
        );
        
        $event->add($toolbar);
        
        $js = UTIL_JsGenerator::newInstance();
        $js->jQueryEvent("#" . $uniqId, "click", 'OW.ajaxFloatBox(e.data.class, e.data.params); return false;', array("e"), array(
            "class" => "MCOMPOSE_CMP_SendMessage",
            "params" => array(
                $recipients,
                MCOMPOSE_BOL_Service::CONTEXT_GROUP
            )
        ));
        
        OW::getDocument()->addOnloadScript($js);
    }

    public function init()
    {
        if ( !$this->isActive() || !$this->isEnabled() ) return;
        
        OW::getEventManager()->bind(MCOMPOSE_BOL_Service::EVENT_ON_SEND, array($this, "onSend"));
        OW::getEventManager()->bind(MCOMPOSE_BOL_Service::EVENT_ON_SEARCH, array($this, "onSearch"));
        OW::getEventManager()->bind(MCOMPOSE_BOL_Service::EVENT_ON_INPUT_INIT, array($this, "onInputInit"));
        
        
        OW::getEventManager()->bind("groups.on_toolbar_collect", array($this, "collectToolbar"));
    }
}
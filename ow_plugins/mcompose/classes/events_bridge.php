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
class MCOMPOSE_CLASS_EventsBridge
{
    const ID_PREFIX = "event";

    /**
     * Class instance
     *
     * @var MCOMPOSE_CLASS_EventsBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return MCOMPOSE_CLASS_EventsBridge
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
        return OW::getPluginManager()->isPluginActive("event");
    }
    
    public function isEnabled()
    {
        return OW::getConfig()->getValue("mcompose", "events_enabled");
    }
    
    public function findEvents( $past, $kw, $userId, $limit = null )
    {
        $eventDao = EVENT_BOL_EventDao::getInstance();
        $eventUserDao = EVENT_BOL_EventUserDao::getInstance();
        
        $params = array(
            'startTime' => time(),
            'endTime' => time(),
            'u' => $userId,
            's' => EVENT_BOL_EventUserDao::VALUE_STATUS_YES
        );
        
        if ( !empty($kw) )
        {
            $params["kw"] = "%" . $kw . "%";
        }
        
        $limitStr = $limit === null ? '' : 'LIMIT 0, ' . intval($limit);
        $kwWhere = empty($kw) ? "1" : "e.title LIKE :kw";
        
        $order = "e.`startTimeStamp` " . ($past ? "DESC" : "");
        
        $query = "SELECT e.* FROM `" . $eventDao->getTableName() . "` e 
            INNER JOIN " . $eventUserDao->getTableName() . " u ON u.eventId=e.id AND u.status=:s
            WHERE u.userId!=:u AND e.userId=:u AND " . $this->getTimeClause($past, "e") . " AND " . $kwWhere . "
            ORDER BY $order $limitStr";

        return OW::getDbo()->queryForObjectList($query, $eventDao->getDtoClassName(), $params);
    }
    
    private function getTimeClause( $past = false, $alias = null )
    {
        if ( $past )
        {
            return "( " . (!empty($alias) ? "`{$alias}`." : "" ) . "`" . EVENT_BOL_EventDao::START_TIME_STAMP . "` <= :startTime AND ( " . (!empty($alias) ? "`{$alias}`." : "" ) . "`" . EVENT_BOL_EventDao::END_TIME_STAMP . "` IS NULL OR " . (!empty($alias) ? "`{$alias}`." : "" ) . "`" . EVENT_BOL_EventDao::END_TIME_STAMP . "` <= :endTime ) )";
        }

        return "( " . (!empty($alias) ? "`{$alias}`." : "" ) . "`" . EVENT_BOL_EventDao::START_TIME_STAMP . "` > :startTime OR ( " . (!empty($alias) ? "`{$alias}`." : "" ) . "`" . EVENT_BOL_EventDao::END_TIME_STAMP . "` IS NOT NULL AND " . (!empty($alias) ? "`{$alias}`." : "" ) . "`" . EVENT_BOL_EventDao::END_TIME_STAMP . "` > :endTime ) )";
    }
    
    public function onSearch( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        
        $kw = $params["kw"];
        $userId = $params["userId"];
        $recipients = $params["recipients"];
        
        $upcoming = array();
        $past = array();
        
        if ( $kw === null && $params["context"] == MCOMPOSE_BOL_Service::CONTEXT_EVENT )
        {
            $upcoming = $this->findEvents(false, null, $userId);
            $past = $this->findEvents(true, null, $userId);
        }
        else
        {
            $upcoming = $this->findEvents(false, $kw, $userId);
            $past = $this->findEvents(true, $kw, $userId);
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
            
            $events = EVENT_BOL_EventService::getInstance()->findByIdList($rIds);
            
            foreach ( $events as $e )
            {
                /* @var $e EVENT_BOL_Event */
                if ( $e->startTimeStamp > time() || $e->endTimeStamp > time() )
                {
                    $upcoming[] = $e;
                }
                else
                {
                    $past[] = $e;
                }
            }
        }
        
        $upcomingData = $this->buildData($upcoming, OW::getLanguage()->text('mcompose', 'selector_group_my_upcoming_events'));
        $pastData = $this->buildData($past, OW::getLanguage()->text('mcompose', 'selector_group_my_past_events'));
        
        foreach ( array_merge($upcomingData, $pastData) as $item )
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
        
        if ( $params["context"] != MCOMPOSE_BOL_Service::CONTEXT_EVENT )
        {
            return;
        }
        
        $group = OW::getLanguage()->text('mcompose', 'selector_group_my_upcoming_events');
        OW::getLanguage()->addKeyForJs('mcompose', 'selector_group_my_upcoming_events');
        
        $input->setupGroup($group, array(
            'priority' => 11,
            'alwaysVisible' => true,
            'noMatchMessage' => false
        ));
        
        $group = OW::getLanguage()->text('mcompose', 'selector_group_my_past_events');
        OW::getLanguage()->addKeyForJs('mcompose', 'selector_group_my_past_events');
        
        $input->setupGroup($group, array(
            'priority' => 12,
            'alwaysVisible' => true,
            'noMatchMessage' => false
        ));
    }
    
    protected function buildData( $events, $itemsGroup = null )
    {
        if ( empty($events) )
        {
            return array();
        }
        
        $eventIds = array();
        foreach ( $events as $e )
        {
            $eventIds[] = $e->id;
        }
        
        $service = EVENT_BOL_EventService::getInstance();
        $out = array();

        foreach ( $events as $event )
        {
            /* @var $event EVENT_BOL_Event */
            
            $ownerAttending = $service->findEventUser($event->id, OW::getUser()->getId())!== null;
            $userCount = $service->findEventUsersCount($event->id, EVENT_BOL_EventUserDao::VALUE_STATUS_YES);
            if ( $ownerAttending ) 
            {
                $userCount--; // Except the event owner
            }
            
            $avatar = $event->getImage() ? $service->generateImageUrl($event->getImage(), true) : $service->generateDefaultImageUrl();
            
            $data = array();
            $data['id'] = $event->id;
            
            $data['url'] = OW::getRouter()->urlForRoute('event.view', array('eventId' => $event->id));
            $data['avatar'] = $avatar;
            $data['text'] = $event->title;
            
            $langKeySuff = $userCount > 1 ? "_many" : '';
            $data['info'] = OW::getLanguage()->text("mcompose", "selector_event_item_info" . $langKeySuff, array(
                "usersCount" => $userCount
            ));
            
            $itemCmp = new MCOMPOSE_CMP_EventItem($data);
            
            $item = array();
            $item["id"] = self::ID_PREFIX . "_" . $event->id;
            $item["text"] = $data['text'];
            $item["url"] = $data['url'];
            $item['html'] = $itemCmp->render();
            $item['count'] = $userCount;

            if ( !empty($itemsGroup) ) {
                $item['group'] = $itemsGroup;
            }

            $out[self::ID_PREFIX . '_' . $event->id] = $item;
        }

        return $out;
    }
    
    public function onSend( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        $recipients = $params["recipients"];
        $service = EVENT_BOL_EventService::getInstance();
        
        foreach ( $recipients as $r )
        {
            list($prefix, $id) = explode("_", $r);
            
            if ( $prefix != self::ID_PREFIX )
            {
                continue;
            }
            
            $users = $service->findEventUsers($id, EVENT_BOL_EventUserDao::VALUE_STATUS_YES, null, 10000);

            foreach ( $users as $user )
            {
                /* @var $user EVENT_BOL_EventUser */
                
                if ( $user->userId != OW::getUser()->getId() )
                {
                    $event->add($user->userId);
                }
            }
        }
    }

    public function onEventContent( BASE_CLASS_EventCollector $event )
    {
        $dispatchAttrs = OW::getRequestHandler()->getDispatchAttributes();
        $params = $dispatchAttrs["params"];
        $eventId = $params['eventId'];
        
        $eventDto = EVENT_BOL_EventService::getInstance()->findEvent($eventId);
        
        if ( empty($eventDto) || $eventDto->userId != OW::getUser()->getId() )
        {
            return;
        }
        
        $uniqId = uniqid("mcompose-");
        $recipients = array(self::ID_PREFIX . "_" . $eventId);
        
        $button = array(
            "label" => OW::getLanguage()->text("mcompose", "events_send_message"),
            "class" => "ow_ic_mail",
            "id" => $uniqId
        );
        
        $event->add('<div class="ow_smallmargin ow_center">' . OW::getThemeManager()->processDecorator("button", $button) . '</div>' );
        
        $js = UTIL_JsGenerator::newInstance();
        $js->jQueryEvent("#" . $uniqId, "click", 'OW.ajaxFloatBox(e.data.class, e.data.params); return false;', array("e"), array(
            "class" => "MCOMPOSE_CMP_SendMessage",
            "params" => array(
                $recipients,
                MCOMPOSE_BOL_Service::CONTEXT_EVENT
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
        
        OW::getEventManager()->bind("events.view.content.after_event_description", array($this, "onEventContent"));
    }
}
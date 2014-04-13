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
 * @package mcompose.class
 * @since 1.0
 */
class MCOMPOSE_CLASS_BaseBridge extends MCOMPOSE_CLASS_AbstractUserBridge
{

    /**
     * Class instance
     *
     * @var MCOMPOSE_CLASS_BaseBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return MCOMPOSE_CLASS_BaseBridge
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
    
    //TODO Methods that should be rewrited after Public interfaces will be avaliable
    public function findUsers( $kw, $limit = null )
    {
        $questionName = OW::getConfig()->getValue('base', 'display_name_question');
        $questionDataTable = BOL_QuestionDataDao::getInstance()->getTableName();

        $limitStr = $limit === null ? '' : 'LIMIT 0, ' . intval($limit);

        $query = 'SELECT DISTINCT qd.userId FROM ' . $questionDataTable . ' qd
            LEFT JOIN `' . BOL_UserSuspendDao::getInstance()->getTableName() . '` AS `us` ON ( `qd`.`userId` = `us`.`userId` )
            WHERE `us`.`userId` IS NULL AND questionName=:name AND textValue LIKE :kw ' . $limitStr;

        return OW::getDbo()->queryForColumnList($query, array(
            'kw' => '%' . $kw . '%',
            'name' => $questionName
        ));
    }
    
    public function onSearch( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        
        $kw = $params["kw"];
        $userId = $params["userId"];
        
        if ( $kw === null )
        {
            $users = BOL_UserService::getInstance()->findList(0, 200);
            $userIds = array();
            
            foreach ( $users as $u )
            {
                $userIds[] = $u->id;
            }
        }
        else
        {
            $userIds = $this->findUsers($kw);
        }
        
        $data = $this->buildData($userIds, OW::getLanguage()->text('mcompose', 'selector_group_other'), array($userId));
        
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
    }

    public function onProfileToolbar( OW_Event $event )
    {
        $params = $event->getParams();
        $userId = $params['userId'];
        
        $uniqId = uniqid("mcompose-");
        $recipients = array(self::ID_PREFIX . "_" . $userId);
        
        $href = OW::getRouter()->urlForRoute("mcompose-index");
        $href = OW::getRequest()->buildUrlQueryString($href, array(
            "recipients" => $recipients,
            "context" => MCOMPOSE_BOL_Service::CONTEXT_GROUP
        ));
        
        $toolbar = array(
            "label" => OW::getLanguage()->text('mailbox', 'create_conversation_button'),
            "href" => $href,
            "id" => $uniqId
        );
        
        $js = UTIL_JsGenerator::newInstance();
        $js->jQueryEvent("#" . $uniqId, "click", 'OW.ajaxFloatBox(e.data.class, e.data.params); return false;', array("e"), array(
            "class" => "MCOMPOSE_CMP_SendMessage",
            "params" => array(
                $recipients,
                MCOMPOSE_BOL_Service::CONTEXT_GROUP
            )
        ));
        
        OW::getDocument()->addOnloadScript($js);
        
        $event->setData($toolbar);
        
        return $toolbar;
    }
    
    public function init()
    {
        parent::init();
        
        OW::getEventManager()->bind(MCOMPOSE_BOL_Service::EVENT_ON_SEARCH, array($this, "onSearch"));
        OW::getEventManager()->bind(MCOMPOSE_BOL_Service::EVENT_ON_INPUT_INIT, array($this, "onInputInit"));
        
        OW::getEventManager()->bind("mcompose.get_profile_toolbar_item", array($this, "onProfileToolbar"));
    }
}
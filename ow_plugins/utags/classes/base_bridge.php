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
class UTAGS_CLASS_BaseBridge extends UTAGS_CLASS_AbstractUserBridge
{

    /**
     * Class instance
     *
     * @var UTAGS_CLASS_BaseBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return UTAGS_CLASS_BaseBridge
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
        
        $userIds = array();
        
        if ( $kw === null )
        {
            $users = BOL_UserService::getInstance()->findList(0, 200);
            
            foreach ( $users as $u )
            {
                $userIds[] = $u->id;
            }
        }
        else
        {
            $userIds = $this->findUsers($kw);
        }
        
        $data = $this->buildData($userIds, OW::getLanguage()->text('utags', 'selector_group_other'));
        
        foreach ( $data as $item )
        {
            $event->add($item);
        }
    }
    
    public function onInputInit( OW_Event $event )
    {
        $params = $event->getParams();
        
        $userId = $params["userId"];
        /* @var $input UTAGS_CMP_Tags */
        $input = $params["input"];
        
        $input->setupGroup(OW::getLanguage()->text('utags', 'selector_group_other'), array(
            'priority' => 9,
            'alwaysVisible' => true,
            'noMatchMessage' => false
        ));
    }
    
    
    public function genericInit()
    {
        
    }
    
    public function init()
    {
        parent::init();

        $this->genericInit();
        
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_ON_SEARCH, array($this, "onSearch"));
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_ON_INPUT_INIT, array($this, "onInputInit"));
    }
}
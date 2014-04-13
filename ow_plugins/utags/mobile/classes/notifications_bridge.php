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
class UTAGS_MCLASS_NotificationsBridge extends UTAGS_CLASS_NotificationsBridge
{
    /**
     * Returns class instance
     *
     * @return UTAGS_MCLASS_NotificationsBridge
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    public function onItemRender( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $params["data"];

        if ( !in_array($params['entityType'], array(self::TYPE_TAG_ME, self::TYPE_TAG_MY_PHOTO)) )
        {
            return;
        }
                
        $event->setData($data);
    }
    
    public function init()
    {
        $this->genericInit();
        
        OW::getEventManager()->bind('mobile.notifications.on_item_render', array($this, 'onItemRender'));
    }
}
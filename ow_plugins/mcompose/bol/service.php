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
 * @package mcompose.bol
 * @since 1.0
 */
class MCOMPOSE_BOL_Service
{
    const CONTEXT_EVENT = "event";
    const CONTEXT_GROUP = "group";
    const CONTEXT_USER = "user";
    
    const EVENT_ON_SEARCH = "mcompose.on_search";
    const EVENT_ON_INPUT_INIT = "mcompose.on_input_init";
    const EVENT_ON_SEND = "mcompose.on_send";
    
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return MCOMPOSE_BOL_Service
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {

    }

    public function getSuggestEntries( $userId, $kw = null, $recipients = null, $context = self::CONTEXT_USER )
    {
        $event = new BASE_CLASS_EventCollector(self::EVENT_ON_SEARCH, array(
            "kw" => $kw,
            "userId" => $userId,
            "context" => $context,
            "recipients" => $recipients
        ));
        
        OW::getEventManager()->trigger($event);
        
        $out = array();
        
        foreach ( $event->getData() as $item )
        {
            $out[$item["id"]] = $item;
        }
        
        return $out;
    }
   
}
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
 * @package uheader.components
 */
class UHEADER_CMP_ActionToolbar extends BASE_CMP_ProfileActionToolbar
{
    public function initToolbar( $items )
    {
        $cmpsMarkup = '';
        $ghroupsCount = 0;

        $tplActions = array();
        $tplGroups = array();

        foreach ( $items as $item  )
        {
            if ( isset($item[self::DATA_KEY_CMP_CLASS]) && $item[self::DATA_KEY_CMP_CLASS] == "MAILBOX_CMP_CreateConversation" )
            {
                $_item = OW::getEventManager()->call("mcompose.get_profile_toolbar_item", array(
                    "userId" => $this->userId
                ));
                
                if ( $_item !== null )
                {
                    $item = $_item;
                }
            }
            
            //Hack for Oxwall 1.5.4. Should be removed later
            if ( isset($item[self::DATA_KEY_CMP_CLASS]) 
                    && $item[self::DATA_KEY_CMP_CLASS] == "BASE_CMP_BlockUser"
                    && isset($item[self::DATA_KEY_LINK_GROUP_KEY])
                    && $item[self::DATA_KEY_LINK_GROUP_KEY] == "base.moderation")
            {
                unset($item[self::DATA_KEY_LINK_GROUP_KEY]);
                unset($item[self::DATA_KEY_LINK_GROUP_LABEL]);
            }
            
            
            if ( isset($item[self::DATA_KEY_LINK_ORDER]) )
            {
                $action['order'] = $item[self::DATA_KEY_LINK_ORDER];
            }
            
            if ( !empty($item[self::DATA_KEY_LINK_GROUP_KEY]) )
            {
                if ( empty($tplGroups[$item[self::DATA_KEY_LINK_GROUP_KEY]]) )
                {
                    $tplGroups[$item[self::DATA_KEY_LINK_GROUP_KEY]] = array(
                        "key" => $item[self::DATA_KEY_LINK_GROUP_KEY],
                        "label" => $item[self::DATA_KEY_LINK_GROUP_LABEL],
                        "toolbar" => array()
                    );
                }
                
                $action['order'] = isset($action['order']) ? $action['order'] : count($tplGroups[$item[self::DATA_KEY_LINK_GROUP_KEY]]["toolbar"]);
                $action = &$tplGroups[$item[self::DATA_KEY_LINK_GROUP_KEY]]["toolbar"][$action['order']];
            }
            else
            {
                $action['order'] = isset($action['order']) ? $action['order'] : count($tplActions);
                $action = &$tplActions[$action['order']];
            }
            
            $action['label'] = $item[self::DATA_KEY_LABEL];

            $attrs = isset($item[self::DATA_KEY_LINK_ATTRIBUTES]) && is_array($item[self::DATA_KEY_LINK_ATTRIBUTES])
                ? $item[self::DATA_KEY_LINK_ATTRIBUTES]
                : array();

            $attrs['href'] = isset($item[self::DATA_KEY_LINK_HREF]) ? $item[self::DATA_KEY_LINK_HREF] : 'javascript://';

            if ( isset($item[self::DATA_KEY_LINK_ID]) )
            {
                $attrs['id'] = $item[self::DATA_KEY_LINK_ID];
            }

            if ( isset($item[self::DATA_KEY_LINK_CLASS]) )
            {
                $attrs['class'] = $item[self::DATA_KEY_LINK_CLASS];
            }

            if ( isset($item[self::DATA_KEY_CMP_CLASS]) )
            {
                $cmpClass = trim($item[self::DATA_KEY_CMP_CLASS]);

                $cmp = OW::getEventManager()->call('class.get_instance', array(
                    'className' => $cmpClass,
                    'arguments' => array(
                        array('userId' => $this->userId)
                    )
                ));

                $cmp = $cmp === null ? new $cmpClass(array('userId' => $this->userId)) : $cmp;

                $cmpsMarkup .= $cmp->render();
            }

            $_attrs = array();
            foreach ( $attrs as $name => $value )
            {
                $_attrs[] = $name . '="' . $value . '"';
            }

            $action['attrs'] = implode(' ', $_attrs);
        }

        
        krsort($tplActions);
        
        $this->assign('toolbar', $tplActions);
        
        foreach ( array_keys($tplGroups) as $key )
        {
            ksort($tplGroups[$key]["toolbar"]);
        }
        
        $this->assign('groups', $tplGroups);
        
        $this->assign('cmpsMarkup', $cmpsMarkup);
    }
}
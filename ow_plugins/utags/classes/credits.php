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
class UTAGS_CLASS_Credits
{
    const ACTION_TAG_PHOTO = 'tag_photo';

    public $allActions = array();

    private $actions;

    public function __construct()
    {
        $this->actions[] = array('pluginKey' => 'utags', 'action' => self::ACTION_TAG_PHOTO, 'amount' => 0);

        $this->allActions = array(
            self::ACTION_TAG_PHOTO
        );
    }

    public function bindCreditActionsCollect( BASE_CLASS_EventCollector $e )
    {
        foreach ( $this->actions as $action )
        {
            $e->add($action);
        }
    }

    public function triggerCreditActionsAdd()
    {
        $e = new BASE_CLASS_EventCollector('usercredits.action_add');

        foreach ( $this->actions as $action )
        {
            $e->add($action);
        }

        OW::getEventManager()->trigger($e);
    }

    public function isAvaliable( $action )
    {
        $eventParams = array(
            'pluginKey' => 'utags',
            'action' => $action
        );

        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);

        return $credits === null ? true : $credits;
    }

    public function getErrorMessage( $action )
    {
        $eventParams = array(
            'pluginKey' => 'utags',
            'action' => $action
        );

        return OW::getEventManager()->call('usercredits.error_message', $eventParams);
    }

    public function trackUse( $action )
    {
        $eventParams = array(
            'pluginKey' => 'utags',
            'action' => $action
        );

        OW::getEventManager()->call('usercredits.track_action', $eventParams);
    }
}
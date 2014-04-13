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
 * @package gheader.classes
 */
class GHEADER_CLASS_Credits
{
    const ACTION_ADD = 'add_cover';

    public $allActions = array();

    private $actions;

    public function __construct()
    {
        $this->actions[] = array('pluginKey' => 'gheader', 'action' => self::ACTION_ADD, 'amount' => 0);

        $this->allActions = array(
            self::ACTION_ADD
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
            'pluginKey' => 'gheader',
            'action' => $action
        );

        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);

        return $credits === null ? true : $credits;
    }

    public function getErrorMessage( $action )
    {
        $eventParams = array(
            'pluginKey' => 'gheader',
            'action' => $action
        );

        return OW::getEventManager()->call('usercredits.error_message', $eventParams);
    }

    public function trackUse( $action )
    {
        $eventParams = array(
            'pluginKey' => 'gheader',
            'action' => $action
        );

        OW::getEventManager()->call('usercredits.track_action', $eventParams);
    }
}
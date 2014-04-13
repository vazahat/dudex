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
class EQUESTIONS_CLASS_Credits
{
    const ACTION_ASK = 'ask_question';
    const ACTION_ANSWER = 'answer_question';
    const ACTION_COMMENT = 'add_comment';
    const ACTION_ASK_FRIEND = 'ask_friend';
    const ACTION_ADD_ANSWER = 'add_answer';

    public $allActions = array();

    private $actions;

    public function __construct()
    {
        $this->actions[] = array('pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY, 'action' => self::ACTION_ASK, 'amount' => 2);
        $this->actions[] = array('pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY, 'action' => self::ACTION_ANSWER, 'amount' => 1);
        $this->actions[] = array('pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY, 'action' => self::ACTION_COMMENT, 'amount' => 1);
        $this->actions[] = array('pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY, 'action' => self::ACTION_ASK_FRIEND, 'amount' => 2);
        $this->actions[] = array('pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY, 'action' => self::ACTION_ADD_ANSWER, 'amount' => 1);

        $this->allActions = array(
            self::ACTION_ASK,
            self::ACTION_ANSWER,
            self::ACTION_COMMENT,
            self::ACTION_ASK_FRIEND,
            self::ACTION_ADD_ANSWER
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
            'pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => $action
        );

        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);

        return $credits === null ? true : $credits;
    }

    public function getErrorMessage( $action )
    {
        $eventParams = array(
            'pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => $action
        );

        return OW::getEventManager()->call('usercredits.error_message', $eventParams);
    }

    public function trackUse( $action )
    {
        $eventParams = array(
            'pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => $action
        );

        OW::getEventManager()->call('usercredits.track_action', $eventParams);
    }
}
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
 * @package questions.classes
 */
class EQUESTIONS_CLASS_CreditsBridge
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_CreditsBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_CreditsBridge
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var EQUESTIONS_CLASS_Credits
     */
    public $credits;

    private function __construct()
    {
        $this->credits = new EQUESTIONS_CLASS_Credits();
    }

    public function onQuestionAdd( OW_Event $e )
    {
        $this->credits->trackUse(EQUESTIONS_CLASS_Credits::ACTION_ASK);
    }

    public function onAnswerAdd( OW_Event $e )
    {
        $params = $e->getParams();

        $option = EQUESTIONS_BOL_Service::getInstance()->findOption($params['optionId']);

        if ( $option->userId != OW::getUser()->getId() )
        {
            $this->credits->trackUse(EQUESTIONS_CLASS_Credits::ACTION_ANSWER);
        }
    }

    public function onPostAdd( OW_Event $e )
    {
        /*$params = $e->getParams();

        $question = EQUESTIONS_BOL_Service::getInstance()->findQuestion($params['questionId']);

        if ( $question->userId != OW::getUser()->getId() )
        {
            $this->credits->trackUse(EQUESTIONS_CLASS_Credits::ACTION_COMMENT);
        }*/
    }

    public function onAsk( OW_Event $e )
    {
        $this->credits->trackUse(EQUESTIONS_CLASS_Credits::ACTION_ASK_FRIEND);
    }

    public function onOptionAdd( OW_Event $e )
    {
        $params = $e->getParams();

        $question = EQUESTIONS_BOL_Service::getInstance()->findQuestion($params['questionId']);

        if ( $question->userId != OW::getUser()->getId() )
        {
            $this->credits->trackUse(EQUESTIONS_CLASS_Credits::ACTION_ADD_ANSWER);
        }
    }

    public function getAllPermissions()
    {
        $out = array();

        foreach ( $this->credits->allActions as $action )
        {
            $out[$action] = $this->credits->isAvaliable($action);
        }

        return $out;
    }

    public function getAllPermissionMessages()
    {
        $out = array();

        foreach ( $this->credits->allActions as $action )
        {
            $out[$action] = $this->credits->getErrorMessage($action);
        }

        return $out;
    }
}
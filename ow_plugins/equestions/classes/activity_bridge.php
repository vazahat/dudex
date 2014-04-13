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
class EQUESTIONS_CLASS_ActivityBridge
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_ActivityBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_ActivityBridge
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
     * @var EQUESTIONS_BOL_FeedService
     */
    private $service;

    private function __construct()
    {
        $this->service = EQUESTIONS_BOL_FeedService::getInstance();
    }

    public function onQuestionAdd( OW_Event $e )
    {
        $params = $e->getParams();

        $activity = new EQUESTIONS_BOL_Activity();
        $activity->questionId = (int) $params['id'];
        $activity->activityType = EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE;
        $activity->activityId = (int) $params['id'];
        $activity->userId = (int) $params['userId'];
        $activity->privacy = $params['privacy'];
        $activity->timeStamp = time();

        $this->service->saveActivity($activity);
    }

    public function onQuestionRemove( OW_Event $e )
    {
        $params = $e->getParams();
        $this->service->deleteActivity($params['id'], EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE, $params['id']);
    }

    public function onAnswerAdd( OW_Event $e )
    {
        $params = $e->getParams();
        $option = EQUESTIONS_BOL_Service::getInstance()->findOption($params['optionId']);

        if ( $option === null )
        {
            return;
        }

        $activity = new EQUESTIONS_BOL_Activity();
        $activity->questionId = $option->questionId;
        $activity->activityType = EQUESTIONS_BOL_FeedService::ACTIVITY_ANSWER;
        $activity->activityId = (int) $params['id'];
        $activity->userId = (int) $params['userId'];
        $activity->timeStamp = time();
        $activity->setData(array(
            'text' => $option->text,
            'optionId' => $option->id
        ));

        $this->service->saveActivity($activity);

        $this->service->deleteActivity($option->questionId, EQUESTIONS_BOL_FeedService::ACTIVITY_ASK, $params['userId']);
    }

    public function onAnswerRemove( OW_Event $e )
    {
        $params = $e->getParams();
        $option = EQUESTIONS_BOL_Service::getInstance()->findOption($params['optionId']);

        if ( $option === null )
        {
            return;
        }

        $this->service->deleteActivity($option->questionId, EQUESTIONS_BOL_FeedService::ACTIVITY_ANSWER, $params['id']);
    }

    public function onFollowAdd( OW_Event $e )
    {
        $params = $e->getParams();

        $activity = new EQUESTIONS_BOL_Activity();
        $activity->questionId = (int) $params['questionId'];
        $activity->activityType = EQUESTIONS_BOL_FeedService::ACTIVITY_FOLLOW;
        $activity->activityId = (int) $params['userId'];
        $activity->userId = (int) $params['userId'];
        $activity->timeStamp = time();

        $this->service->saveActivity($activity);
    }

    public function onFollowRemove( OW_Event $e )
    {
        $params = $e->getParams();
        $this->service->deleteActivity($params['questionId'], EQUESTIONS_BOL_FeedService::ACTIVITY_FOLLOW, $params['userId']);
    }

    public function onPostAdd( OW_Event $e )
    {
        $params = $e->getParams();

        $activity = new EQUESTIONS_BOL_Activity();
        $activity->questionId = (int) $params['questionId'];
        $activity->activityType = EQUESTIONS_BOL_FeedService::ACTIVITY_POST;
        $activity->activityId = (int) $params['id'];
        $activity->userId = (int) $params['userId'];
        $activity->timeStamp = time();

        $activity->setData(array(
            'text' => $params['text']
        ));

        $this->service->saveActivity($activity);
    }

    public function onPostRemove( OW_Event $e )
    {
        $params = $e->getParams();
        $this->service->deleteActivity($params['questionId'], EQUESTIONS_BOL_FeedService::ACTIVITY_POST, $params['id']);
    }

    public function onAsk( OW_Event $e )
    {
        $params = $e->getParams();

        $activity = new EQUESTIONS_BOL_Activity();
        $activity->questionId = (int) $params['questionId'];
        $activity->activityType = EQUESTIONS_BOL_FeedService::ACTIVITY_ASK;
        $activity->activityId = (int) $params['recipientId'];
        $activity->userId = (int) $params['userId'];
        $activity->privacy = EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY;
        $activity->timeStamp = time();

        $this->service->saveActivity($activity);
    }
}
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
 * @package equestions.bol
 */
class EQUESTIONS_BOL_FeedService
{
    const ACTIVITY_CREATE = 'create';
    const ACTIVITY_FOLLOW = 'follow';
    const ACTIVITY_ANSWER = 'answer';
    const ACTIVITY_POST = 'post';
    const ACTIVITY_ASK = 'ask';

    const PRIVACY_EVERYBODY = 'everybody';
    const PRIVACY_FRIENDS = 'friends_only';
    const PRIVACY_ONLY_ME = 'only_for_me';
    const PRIVACY_NOBODY = 'nobody';

    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return EQUESTIONS_BOL_FeedService
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var EQUESTIONS_BOL_ActivityDao
     */
    private $activityDao;

    /**
     *
     * @var EQUESTIONS_BOL_QuestionDao
     */
    private $questionDao;

    private function __construct()
    {
        $this->activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();
        $this->questionDao = EQUESTIONS_BOL_QuestionDao::getInstance();
    }

    public function saveActivity( EQUESTIONS_BOL_Activity $activity )
    {
        $oldActivity = $this->findActivity($activity->questionId, $activity->activityType, $activity->activityId);

        if ( $oldActivity !== null )
        {
            $activity->id = $oldActivity->id;
        }

        $this->activityDao->save($activity);
    }

    public function findActivity( $questionId, $activityType, $activityId )
    {
        return $this->activityDao->findActivity($questionId, $activityType, $activityId);
    }

    public function deleteActivity( $questionId, $activityType, $activityId )
    {
        $this->activityDao->deleteActivity($questionId, $activityType, $activityId);
    }


    public function findMainFeed( $startStamp, $count, $questionIds )
    {
        return $this->questionDao->findMainFeed($startStamp, $count, $questionIds);
    }

    public function findOrderedMainFeed( $startStamp, $count, $questionIds, $orderActivities )
    {
        return $this->questionDao->findOrderedMainFeed($startStamp, $count, $questionIds, $orderActivities);
    }

    public function findMainFeedCount( $startStamp )
    {
        return $this->questionDao->findMainFeedCount($startStamp);
    }

    public function findMainActivity( $startStamp, $questionIds )
    {
        return $this->fetchActivity($this->activityDao->findMainActivity($startStamp, $questionIds));
    }

    public function findMyFeed( $startStamp, $userId, $count, $questionIds )
    {
        return $this->questionDao->findMyFeed($startStamp, $userId, $count, $questionIds);
    }

    public function findOrderedMyFeed( $startStamp, $userId, $count, $questionIds, $orderActivities )
    {
        return $this->questionDao->findOrderedMyFeed($startStamp, $userId, $count, $questionIds, $orderActivities);
    }

    public function findMyFeedCount( $startStamp, $userId )
    {
        return $this->questionDao->findMyFeedCount($startStamp, $userId);
    }

    public function findMyActivity( $startStamp, $userId, $questionIds )
    {
        return $this->fetchActivity($this->activityDao->findMyActivity($startStamp, $questionIds, $userId));
    }


    public function findFriendsFeed( $startStamp, $userId, $count, $questionIds )
    {
        return $this->questionDao->findFriendsFeed($startStamp, $userId, $count, $questionIds);
    }

    public function findOrderedFriendsFeed( $startStamp, $userId, $count, $questionIds, $orderActivities )
    {
        return $this->questionDao->findOrderedFriendsFeed($startStamp, $userId, $count, $questionIds, $orderActivities);
    }

    public function findFriendsFeedCount( $startStamp, $userId )
    {
        return $this->questionDao->findFriendsFeedCount($startStamp, $userId);
    }

    public function findFriendsActivity( $startStamp, $userId, $questionIds )
    {
        return $this->fetchActivity($this->activityDao->findFriendsActivity($startStamp, $questionIds, $userId));
    }


    public function findNotificationsFeed( $startStamp, $userId, $count, $questionIds )
    {
        return $this->questionDao->findNotificationsFeed($startStamp, $userId, $count, $questionIds);
    }

    public function findNotificationsFeedCount( $startStamp, $userId )
    {
        return $this->questionDao->findNotificationsFeedCount($startStamp, $userId);
    }

    public function findNotificationsActivity( $startStamp, $userId, $questionIds )
    {
        return $this->fetchActivity($this->activityDao->findNotificationsActivity($startStamp, $questionIds, $userId));
    }


    private function fetchActivity( $list )
    {
        $out = array();
        foreach ( $list as $item )
        {
            $out[$item->questionId][] = $item;
        }

        return $out;
    }

    private function fetchFeed( $feed )
    {
        $out = array();
        foreach ( $feed as $item )
        {
            $question = new EQUESTIONS_BOL_Question();
            $question->id = (int) $item['qId'];
            $question->settings = $item['qSettings'];
            $question->text = $item['qText'];
            $question->timeStamp = (int) $item['qTimeStamp'];
            $question->userId = (int) $item['qUserId'];

            $activity = new EQUESTIONS_BOL_Activity();
            $activity->id = (int) $item['aId'];
            $activity->activityType = $item['aActivityType'];
            $activity->activityId = (int) $item['aActivityId'];
            $activity->data = $item['aData'];
            $activity->privacy = $item['aPrivacy'];
            $activity->questionId = (int) $item['aQuestionId'];
            $activity->timeStamp = (int) $item['aTimeStamp'];
            $activity->userId = (int) $item['aUserId'];

            $out[] = array(
                'question' => $question,
                'activity' => $activity
            );
        }

        return $out;
    }

    public function setOrder( $feedType, $order, $userId )
    {
        setcookie('questions_list_order_' . $feedType, $order, time() + 3600 * 24 * 365, '/');
    }

    public function getOrder( $feedType, $userId )
    {
        $order = null;

        if ( !empty($_COOKIE['questions_list_order_' . $feedType]) )
        {
            $order = $_COOKIE['questions_list_order_' . $feedType];
        }

        return !empty($order) ? $order : $this->getDefaultOrder();
    }

    public function getDefaultOrder()
    {
        return OW::getConfig()->getValue(EQUESTIONS_Plugin::PLUGIN_KEY, 'list_order');
    }

    public function setPrivacy( $userId, $privacy )
    {
        $this->activityDao->setPrivacy($userId, $privacy);
    }
}
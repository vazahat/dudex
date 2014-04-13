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
class EQUESTIONS_BOL_NotificationService
{
    const LISTENER_NODE_ID = 'online_notification_listener';
    const LISTENER_COUNT = 8;

    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return EQUESTIONS_BOL_NotificationService
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
     * @var EQUESTIONS_BOL_NotificationDao
     */
    private $notificationDao;

    private function __construct()
    {
        $this->notificationDao = EQUESTIONS_BOL_NotificationDao::getInstance();
    }

    /**
     *
     * @param string $type
     * @param int $questionId
     * @param int $userId
     * @return EQUESTIONS_BOL_Notification
     */
    public function findNotification( $questionId, $type, $userId )
    {
        return $this->notificationDao->findNotification($questionId, $type, $userId);
    }

    /**
     *
     * @param int $id
     * @return EQUESTIONS_BOL_Notification
     */
    public function findNotificationById( $id )
    {
        return $this->notificationDao->findById($id);
    }

    public function saveNotification( EQUESTIONS_BOL_Notification $notification )
    {
        if ( empty($notification->id) )
        {
            $dto = $this->findNotification($notification->questionId, $notification->type, $notification->userId);

            if ( $dto !== null )
            {
                $notification->id = $dto->id;
            }
        }

        return $this->notificationDao->save($notification);
    }

    public function markNotificationViewed( $questionId, $type, $userId, $viewed = true )
    {
        $dto = $this->findNotification($questionId, $type, $userId);
        $dto->viewed = (int) $viewed;

        $this->saveNotification($dto);
    }

    public function markNotificationListViewed( $idList, $viewed = true )
    {
        $this->notificationDao->markListViewed($idList, (int) $viewed);
    }

    public function deleteNotification( $questionId, $type, $userId )
    {
        $this->notificationDao->deleteNotification($questionId, $type, $userId);
    }

    public function deleteNotificationById( $id )
    {
        $this->notificationDao->deleteById($id);
    }

    public function findNewList( $userId, $limit )
    {
        return $this->notificationDao->findList($userId, $limit, false);
    }

    public function findNewListCount( $userId )
    {
        return $this->notificationDao->findCount($userId, false);
    }

    public function findAllList( $userId, $limit )
    {
        return $this->notificationDao->findList($userId, $limit);
    }

    public function findAllListCount( $userId )
    {
        return $this->notificationDao->findCount($userId);
    }

    public function findSentNotificationList( $questionId, $senderId, $type = null )
    {
        return $this->notificationDao->findSentList($questionId, $senderId, $type);
    }

    public function findNotificationListByQuestionId( $questionId )
    {
        return $this->notificationDao->findListByQuestionId($questionId);
    }
}
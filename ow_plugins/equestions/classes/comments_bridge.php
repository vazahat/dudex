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
class EQUESTIONS_CLASS_CommentsBridge
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_CommentsBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_CommentsBridge
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
     * @var BOL_CommentService
     */
    private $service;

    private function __construct()
    {
        $this->service = BOL_CommentService::getInstance();
    }

    public function onCommentAdd( OW_Event $e )
    {
        $params = $e->getParams();

        if ( $params['entityType'] != EQUESTIONS_BOL_Service::ENTITY_TYPE )
        {
            return;
        }

        $questionId = (int) $params['entityId'];
        $comment = $this->service->findComment($params['commentId']);

        $event = new OW_Event(EQUESTIONS_BOL_Service::EVENT_POST_ADDED, array(
            'questionId' => $questionId,
            'id' => (int) $params['commentId'],
            'userId' => (int) $params['userId'],
            'text' => $comment->message
        ));

        OW::getEventManager()->trigger($event);
    }

    public function onCommentRemove( OW_Event $e )
    {
        $params = $e->getParams();

        if ( $params['entityType'] != EQUESTIONS_BOL_Service::ENTITY_TYPE )
        {
            return;
        }

        $questionId = (int) $params['entityId'];

        $event = new OW_Event(EQUESTIONS_BOL_Service::EVENT_POST_REMOVED, array(
            'questionId' => $questionId,
            'id' => (int) $params['commentId'],
            'userId' => (int) $params['userId']
        ));

        OW::getEventManager()->trigger($event);
    }

    public function onQuestionRemove( OW_Event $e )
    {
        $params = $e->getParams();
        $questionId = (int) $params['id'];

        $posts = $this->service->findFullCommentList(EQUESTIONS_BOL_Service::ENTITY_TYPE, $questionId);

        foreach ( $posts as $post )
        {
            $event = new OW_Event(EQUESTIONS_BOL_Service::EVENT_POST_REMOVED, array(
                'questionId' => $questionId,
                'id' => $post->id,
                'userId' => $post->userId
            ));

            OW::getEventManager()->trigger($event);
        }

        $this->service->deleteEntityComments(EQUESTIONS_BOL_Service::ENTITY_TYPE, $questionId);
    }
}
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
class EQUESTIONS_CLASS_InvitationsBridge
{
    const INVITATION_ASK = 'questions-ask';
    const ACTION_ASK = 'questions-ask';

    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_InvitationsBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_InvitationsBridge
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
     * @var EQUESTIONS_BOL_NotificationService
     */
    private $service;

    private function __construct()
    {
        $this->service = EQUESTIONS_BOL_NotificationService::getInstance();
    }

    public function onBeforeAsk( OW_Event $event )
    {
        $params = $event->getParams();

        $question = EQUESTIONS_BOL_Service::getInstance()->findQuestion($params['questionId']);

        $data = array(
            'question' => $question->text
        );

        $notification = new EQUESTIONS_BOL_Notification();
        $notification->userId = (int) $params['recipientId'];
        $notification->special = empty($params['special']) ? 0 : 1;
        $notification->senderId = (int) $params['userId'];
        $notification->type = EQUESTIONS_BOL_FeedService::ACTIVITY_ASK;
        $notification->questionId = (int) $params['questionId'];
        $notification->setData($data);
        $notification->timeStamp = time();
        $notification->viewed = false;

        $this->service->saveNotification($notification);

        $params = array(
            'userId' => $notification->senderId,
            'questionId' => $notification->questionId,
            'recipientId' => $notification->userId,
            'special' => $notification->special,
            'id' => $notification->id
        );

        $event = new OW_Event( EQUESTIONS_BOL_Service::EVENT_QUESTION_ASKED, $params, $data);
        OW::getEventManager()->trigger($event);
    }

    public function onAnswerAdd( OW_Event $e )
    {
        $params = $e->getParams();
        $option = EQUESTIONS_BOL_Service::getInstance()->findOption($params['optionId']);

        if ( $option === null )
        {
            return;
        }

        $notification = $this->service->findNotification($option->questionId, EQUESTIONS_BOL_FeedService::ACTIVITY_ASK, $params['userId']);

        if ( $notification === null )
        {
            return;
        }

        $this->deleteInvitation($notification->id);
    }

    private function deleteInvitation( $notificationId )
    {
        $this->service->deleteNotificationById($notificationId);

        OW::getEventManager()->call('invitations.remove', array(
            'entityType' => self::INVITATION_ASK,
            'entityId' => $notificationId
        ));
    }


    public function onAfterAsk( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        $questionId = $params['questionId'];
        $invitationId = $params['id'];
        $userId = $params['userId'];
        $recipientId = $params['recipientId'];
        $special = $params['special'];
        $questionText = UTIL_String::truncate($data['question'], 100, '...');
        $questionUrl = OW::getRouter()->urlForRoute('equestions-question', array(
            'qid' => $questionId
        ));

        $userAvatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($userId));
        $userAvatar = $userAvatars[$userId];

        $uniqId = uniqid('question_notification_');

        $string = array(
            'key' => 'equestions+invitation_string',
            'vars' => array(
                'question' => '<a id="' . $uniqId . '" href="' . $questionUrl . '" >' . $questionText . '</a>',
                'user' => '<a href="' . $userAvatar['url'] . '">' . $userAvatar['title'] . '</a>'
            )
        );

        $questionSettings = array(
            'userContext' => array((int) $userId),
            'questionId' => $questionId,
            'relationId' => $questionId
        );

        $invitationEvent = new OW_Event('invitations.add', array(
            'pluginKey' => 'equestions',
            'entityType' => self::INVITATION_ASK,
            'entityId' => $invitationId,
            'userId' => $recipientId,
            'time' => time(),
            'action' => self::ACTION_ASK
        ), array(
            'string' => $string,
            'avatar' => $userAvatar,
            'questionSettings' => $questionSettings,
            'uniqId' => $uniqId
        ));

        OW::getEventManager()->trigger($invitationEvent);
    }

    public function onInviteRender( OW_Event $event )
    {
        $params = $event->getParams();

        if ( $params['entityType'] != self::INVITATION_ASK )
        {
            return;
        }

        EQUESTIONS_Plugin::getInstance()->addStatic(true);

        $data = $params['data'];
        $questionSettings = $data['questionSettings'];
        $uniqId = $data['uniqId'];

        $notificationId = (int) $params['entityId'];
        $itemKey = $params['key'];

        $data['toolbar'] = array(
            array(
                'label' => OW::getLanguage()->text('equestions', 'invitation_ignore_label'),
                'id'=> 'toolbar_ignore_' . $itemKey
            )
        );

        $event->setData($data);

        $js = UTIL_JsGenerator::newInstance();

        $js->jQueryEvent("#" . $uniqId, 'click',
                'QUESTIONS.openQuestion(e.data.questionSettings); return false;',
        array('e'), array(
            'questionSettings' => $questionSettings
        ));

        $jsData = array(
            'notificationId' => $notificationId,
            'itemKey' => $itemKey
        );

        $js->jQueryEvent("#toolbar_ignore_$itemKey", 'click',
                'OW.Invitation.send("questions.ignore", e.data.notificationId).removeItem(e.data.itemKey);',
        array('e'), $jsData);

        OW::getDocument()->addOnloadScript($js->generateJs());
    }

    public function onCommand( OW_Event $event )
    {
        $params = $event->getParams();

        if ( !in_array($params['command'], array('questions.ignore')) )
        {
            return;
        }

        $this->deleteInvitation($params['data']);
    }


    //Notifications

    public function onCollectNotificationActions( BASE_CLASS_EventCollector $e )
    {
        $e->add(array(
            'section' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => self::ACTION_ASK,
            'sectionIcon' => 'ow_ic_lens',
            'sectionLabel' => OW::getLanguage()->text(EQUESTIONS_Plugin::PLUGIN_KEY, 'email_notifications_section_label'),
            'description' => OW::getLanguage()->text(EQUESTIONS_Plugin::PLUGIN_KEY, 'email_notifications_setting_ask'),
            'selected' => true
        ));
    }


    public function onQuestionRemove( OW_Event $e )
    {
        $params = $e->getParams();
        $list = $this->service->findNotificationListByQuestionId($params['id']);

        foreach ( $list as $notification )
        {
            $this->deleteInvitation($notification->id);
        }
    }


    public function init()
    {
        //Invitations
        OW::getEventManager()->bind('invitations.on_command', array($this, 'onCommand'));
        OW::getEventManager()->bind('invitations.on_item_render', array($this, 'onInviteRender'));

        //Notifications
        OW::getEventManager()->bind('notifications.collect_actions', array($this, 'onCollectNotificationActions'));

        //Questions
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_BEFORE_ASK, array($this, 'onBeforeAsk'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ANSWER_ADDED, array($this, 'onAnswerAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_REMOVED, array($this, 'onQuestionRemove'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_QUESTION_ASKED, array($this, 'onAfterAsk'));
    }
}
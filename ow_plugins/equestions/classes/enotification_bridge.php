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
class EQUESTIONS_CLASS_EnotificationBridge
{
    const ACTION_ANSWER = 'questions-answer';
    const ACTION_POST = 'questions-post';

    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_NotificationBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_NotificationBridge
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
     * @var EQUESTIONS_BOL_Service
     */
    private $service;

    private function __construct()
    {
        $this->service = EQUESTIONS_BOL_Service::getInstance();
    }

    public function onAnswerAdd( OW_Event $event )
    {
        $params = $event->getParams();

        $option = $this->service->findOption($params['optionId']);
        $userId = $params['userId'];

        $userService = BOL_UserService::getInstance();

        $question = $this->service->findQuestion($option->questionId);
        $answer = $option->text;
        $answerId = $params['id'];
        $ownerId = $question->userId;

        $questionText = UTIL_String::truncate($question->text, 100, '...');
        $questionUrl = OW::getRouter()->urlForRoute('equestions-question', array(
            'qid' => $question->id
        ));

        $notificationParams = array(
            'pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => self::ACTION_ANSWER,
            'entityType' => 'questions-answer',
            'entityId' => $answerId,
            'userId' => null,
            'time' => time()
        );

        $userAvatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($userId));
        $userAvatar = $userAvatars[$userId];

        $uniqId = uniqid('notification_answer');

        $string = array(
            'key' => EQUESTIONS_Plugin::PLUGIN_KEY . '+notifications_answer',
            'vars' => array(
                'question' => '<a class="' . $uniqId . '" href="' . $questionUrl . '" >' . $questionText . '</a>',
                'user' => '<a href="' . $userAvatar['url'] . '">' . $userAvatar['title'] . '</a>',
                'answer' => '<a class="' . $uniqId . '" href="' . $questionUrl . '" >' . $answer . '</a>'
            )
        );

        $questionSettings = array(
            'userContext' => array((int) $userId),
            'questionId' => $question->id,
            'relationId' => $question->id
        );

        $notificationData = array(
            'string' => $string,
            'avatar' => $userAvatar,
            'questionSettings' => $questionSettings,
            'uniqId' => $uniqId,
            'url' => $questionUrl
        );

        $follows = $this->service->findFollows($question->id, null, array($userId));

        foreach ( $follows as $f )
        {
            $notificationParams['userId'] = $f->userId;

            $event = new OW_Event('notifications.add', $notificationParams, $notificationData);
            OW::getEventManager()->trigger($event);
        }
    }


    public function onInviteRender( OW_Event $event )
    {
        $params = $event->getParams();

        if ( !in_array($params['entityType'], array('questions-answer', 'questions-post')) )
        {
            return;
        }

        EQUESTIONS_Plugin::getInstance()->addStatic(true);

        $data = $params['data'];
        $questionSettings = $data['questionSettings'];
        $uniqId = $data['uniqId'];

        $data['url'] = 'javascript:(function() { QUESTIONS.openQuestion({
            questionId: ' . $questionSettings['questionId'] . ',
            relationId: ' . $questionSettings['relationId'] . ',
            userContext: ' . json_encode($questionSettings['userContext']) . '
        }); return void(0); })()';

        $event->setData($data);

        $js = UTIL_JsGenerator::newInstance();

        $js->jQueryEvent("." . $uniqId, 'click',
                'QUESTIONS.openQuestion(e.data.questionSettings); return false;',
        array('e'), array(
            'questionSettings' => $questionSettings
        ));

        OW::getDocument()->addOnloadScript($js->generateJs());
    }

    public function onPostAdd( OW_Event $event )
    {
        $params = $event->getParams();

        $userService = BOL_UserService::getInstance();
        $userId = $params['userId'];

        $question = $this->service->findQuestion($params['questionId']);
        $post = UTIL_String::truncate($params['text'], 100, '...');
        $postId = $params['id'];
        $ownerId = $question->userId;

        $questionUrl = OW::getRouter()->urlForRoute('equestions-question', array(
            'qid' => $question->id
        ));

        $questionText = UTIL_String::truncate($question->text, 100, '...');

        $notificationParams = array(
            'pluginKey' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => self::ACTION_POST,
            'entityType' => 'questions-post',
            'entityId' => $postId,
            'userId' => null,
            'time' => time()
        );

        $uniqId = uniqid('question_post');

        $userAvatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($userId));
        $userAvatar = $userAvatars[$userId];

        $string = array(
            'key' => EQUESTIONS_Plugin::PLUGIN_KEY . '+notifications_post',
            'vars' => array(
                'question' => '<a class="' . $uniqId . '" href="' . $questionUrl . '" >' . $questionText . '</a>',
                'user' => '<a href="' . $userAvatar['url'] . '">' . $userAvatar['title'] . '</a>',
                'post' => '<a class="' . $uniqId . '" href="' . $questionUrl . '" >' . $post . '</a>'
            )
        );

        $questionSettings = array(
            'userContext' => array((int) $userId),
            'questionId' => $question->id,
            'relationId' => $question->id
        );

        $notificationData = array(
            'string' => $string,
            'avatar' => $userAvatar,
            'questionSettings' => $questionSettings,
            'uniqId' => $uniqId,
            'url' => $questionUrl
        );

        $follows = $this->service->findFollows($question->id, null, array($userId));

        foreach ( $follows as $f )
        {
            $notificationParams['userId'] = $f->userId;

            $event = new OW_Event('notifications.add', $notificationParams, $notificationData);
            OW::getEventManager()->trigger($event);
        }
    }

    public function onCollectActions( BASE_CLASS_EventCollector $e )
    {
        $e->add(array(
            'section' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => self::ACTION_ANSWER,
            'sectionIcon' => 'ow_ic_lens',
            'sectionLabel' => OW::getLanguage()->text(EQUESTIONS_Plugin::PLUGIN_KEY, 'email_notifications_section_label'),
            'description' => OW::getLanguage()->text(EQUESTIONS_Plugin::PLUGIN_KEY, 'email_notifications_setting_answer'),
            'selected' => true
        ));

        $e->add(array(
            'section' => EQUESTIONS_Plugin::PLUGIN_KEY,
            'action' => self::ACTION_POST,
            'sectionIcon' => 'ow_ic_lens',
            'sectionLabel' => OW::getLanguage()->text(EQUESTIONS_Plugin::PLUGIN_KEY, 'email_notifications_section_label'),
            'description' => OW::getLanguage()->text(EQUESTIONS_Plugin::PLUGIN_KEY, 'email_notifications_setting_post'),
            'selected' => true
        ));
    }

    public function init()
    {
        OW::getEventManager()->bind('notifications.on_item_render', array($this, 'onInviteRender'));

        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_ANSWER_ADDED, array($this, 'onAnswerAdd'));
        OW::getEventManager()->bind(EQUESTIONS_BOL_Service::EVENT_POST_ADDED, array($this, 'onPostAdd'));
        OW::getEventManager()->bind('notifications.collect_actions', array($this, 'onCollectActions'));
    }
}
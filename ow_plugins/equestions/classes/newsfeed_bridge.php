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
class EQUESTIONS_CLASS_NewsfeedBridge
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_NewsfeedBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_NewsfeedBridge
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

    //Event Handlers

    public function onStatusCmp( OW_Event $event )
    {
        $params = $event->getParams();
        $language = OW::getLanguage();

        $status = new NEWSFEED_CMP_UpdateStatus($params['feedAutoId'], $params['entityType'], $params['entityId'], $params['visibility']);

        if ( EQUESTIONS_BOL_Service::getInstance()->isCurrentUserCanAsk() )
        {
            $tabs = new EQUESTIONS_CMP_Tabs();
            $tabs->addTab($language->text('equestions', 'newsfeed_status_tab'), $status, 'ow_ic_chat');
            $question = new EQUESTIONS_CMP_NewsfeedQuestionAdd($params['feedAutoId'], $params['entityType'], $params['entityId'], $params['visibility']);
            $tabs->addTab($language->text('equestions', 'newsfeed_question_tab'), $question, 'ow_ic_lens');

            $status = $tabs;
        }

        return $status;
    }

    public function onItemRender( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        if ( $params['action']['entityType'] != EQUESTIONS_BOL_Service::ENTITY_TYPE )
        {
            return;
        }

        $language = OW::getLanguage();
        $configs = OW::getConfig()->getValues('equestions');
        $questionId = $params['action']['entityId'];
        $userId = OW::getUser()->getId();

        $question = $this->service->findQuestion($questionId);
        $optionTotal = $this->service->findOptionCount($questionId);
        $answerCount = $this->service->findTotalAnswersCount($questionId);
        $postCount = BOL_CommentService::getInstance()->findCommentCount(EQUESTIONS_BOL_Service::ENTITY_TYPE, $params['action']['entityId']);
        $userContext = array();

        $count = EQUESTIONS_BOL_Service::DISPLAY_COUNT;
        if ( $optionTotal - $count < 2 )
        {
            $count = $optionTotal;
        }

        $cmp = new EQUESTIONS_CMP_Answers($question, $optionTotal, array(0, $count));
        $cmp->setTotalAnswerCount($answerCount);

        if ( in_array($params['feedType'], array('user', 'my')) )
        {
            foreach ( $params['activity'] as $act )
            {
                if ( $act['activityType'] == 'answer' )
                {
                    $userContext[] = $act['userId'];
                }
            }

            $cmp->setUsersContext($userContext);
        }

        $lastActivity = $this->getBubbleActivity($params);

        $data['assign']['answers'] = $cmp->render();

        $questionUrl = OW::getRouter()->urlForRoute('equestions-question', array(
            'qid' => $question->id
        ));

        $jsSelector = 'QUESTIONS_AnswerListCollection.' . $cmp->getUniqId();
        $allowPopups = !isset($configs['allow_popups']) || $configs['allow_popups'];

        $data['assign']['string'] = $data['string'] = $this->getItemString($question, $lastActivity, $jsSelector, $questionUrl);

        $data['features'] = array();

        $onClickStr = "window.location.href='$questionUrl'";

        if ( $configs['allow_comments'] )
        {
            if ( $allowPopups )
            {
                $onClickStr = "return {$jsSelector}.openQuestionDelegate(true);";
            }

            $data['features'][] = array(
                'class' => 'q-' . $cmp->getUniqId() . '-status-comments',
                'iconClass' => 'ow_miniic_comment',
                'label' => $postCount,
                'onclick' => $onClickStr,
                'string' => null
            );
        }

        if ( $allowPopups )
        {
            $onClickStr = "return {$jsSelector}.openQuestionDelegate();";
        }

        $data['features'][] = array(
            'class' => 'q-' . $cmp->getUniqId() . '-status-votes',
            'iconClass' => 'questions_miniicon_check',
            'label' => $answerCount,
            'onclick' => $onClickStr,
            'string' => null
        );

        if ( $configs['enable_follow'] )
        {
            $onClickStr = "OW.error('" . $language->text('equestions', 'follow_not_allowed') . "')";
            $isFollowing = false;

            if ( $this->service->isCurrentUserCanInteract($question) )
            {
                $isFollowing = $this->service->isFollow($userId, $question->id);
                $onClickStr = $isFollowing
                    ? $jsSelector . '.unfollowQuestion();'
                    : $jsSelector . '.followQuestion();';
            }
            else if ( OW::getUser()->isAuthenticated() )
            {
                $isFollowing = $this->service->isFollow($userId, $question->id);

                if ( $isFollowing )
                {
                    $onClickStr = $jsSelector . '.unfollowQuestion();';
                }
            }

            $data['features'][] = array(
                'class' => 'q-' . $cmp->getUniqId() . '-status-follows',
                'iconClass' => 'questions_miniic_follow',
                'label' => $this->service->findFollowsCount($question->id),
                'onclick' => $onClickStr,
                'active' => $isFollowing
            );
        }

        $settings = $question->getSettings();

        if ( isset($settings['context']) && $settings['context']['type'] != $params['feedType'] )
        {
            if ( !empty($settings['context']['url']) && !empty($settings['context']['label']) )
            {
                $data['context'] = $settings['context'];
            }
        }

        $event->setData($data);
    }

    private function getBubbleActivity( $params )
    {
        if ( !empty($params['lastActivity']) )
        {
            return $params['lastActivity'];
        }

        foreach ( $params['activity'] as $act ) //TODO: Back compatibility with 1.3.1
        {
            if ( !in_array($act['activityType'], array('subscribe')) )
            {
                return $act;
            }
        }

        return $params['createActivity'];
    }

    private function getItemString( $question, $bubbleActivity, $jsSelector, $questionUrl )
    {
        $activityType = $bubbleActivity['activityType'];

        $configs = OW::getConfig()->getValues('equestions');

        $allowPopups = !isset($configs['allow_popups']) || $configs['allow_popups'];

        $onClickStr = $allowPopups ? 'onclick="return ' . $jsSelector . '.openQuestionDelegate();"' : '';

        $questionEmbed = '<a href="' . $questionUrl . '" ' . $onClickStr . '>' . $question->text . '</a>';

        if ( in_array($activityType, array(EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE, EQUESTIONS_BOL_FeedService::ACTIVITY_FOLLOW)) )
        {
            return OW::getLanguage()->text('equestions', 'item_text_' . $activityType, array(
                'question' => $questionEmbed
            ));
        }

        $buubleData = $bubbleActivity['data'];
        $with = '';
        if ( !empty($buubleData['text']) )
        {
            $text = UTIL_String::truncate($buubleData['text'], 50, '...');
            $with = '<a href="' . $questionUrl . '" ' . $onClickStr . '>' . $text . '</a>';
        }

        return OW::getLanguage()->text('equestions', 'item_text_' . $activityType, array(
            'question' => $questionEmbed,
            'with' => $with
        ));
    }

    public function onEntityAdd( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();
        $language = OW::getLanguage();

        if ( $params['entityType'] != EQUESTIONS_BOL_Service::ENTITY_TYPE )
        {
            return;
        }

        $questionId = (int) $params['entityId'];
        $question = EQUESTIONS_BOL_Service::getInstance()->findQuestion($questionId);

        if ( $question === null )
        {
            return;
        }

        $questionUrl = OW::getRouter()->urlForRoute('equestions-question', array(
            'qid' => $question->id
        ));

        $questionEmbed = '<a href="' . $questionUrl . '">' . $question->text . '</a>';
        $string = $language->text('equestions', 'item_text_create', array(
            'question' => $questionEmbed
        ));

        $data = array_merge($data, array(
            'params' => array(
                'subscribe' => true
            ),
            'ownerId' => (int) $question->userId,
            'time' => (int) $question->timeStamp,
            'string' => $string,
            'content' => '[ph:answers]',
            'view' => array(
                'iconClass' => 'ow_ic_lens'
            ),
            'data' => array(
                'questionId' => $question->id
            )
        ));

        $event->setData($data);
    }

    public function onActivity( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        $siteActivity = true;

        if ( !$siteActivity )
        {
            if ( $params['entityType'] == EQUESTIONS_BOL_Service::ENTITY_TYPE )
            {
                if ( $params['activityType'] == 'comment' )
                {
                    $data['params']['subscribe'] = false;
                }

                if ( !isset($params['visibility']) && !isset($data['params']['visibility']) )
                {
                    $data['params']['visibility'] = 15;
                }
                else if (isset($params['visibility']))
                {
                    $data['params']['visibility'] = $params['visibility'];
                }

                if ( $params['activityType'] != 'create' && intval($data['params']['visibility']) & 1 )
                {
                    $data['params']['visibility'] -= 1; // All visibility (15) instead of SITE Visibility (1)
                }
            }
        }

        $event->setData($data);
    }

    public function onAnswerAdd( OW_Event $event )
    {
        $params = $event->getParams();
        $optionId = (int) $params['optionId'];

        $optionDto = EQUESTIONS_BOL_Service::getInstance()->findOption($optionId);

        $id = (int) $params['id'];

        $activityParams = array(
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $optionDto->questionId,
            'pluginKey' => 'equestions',
            'activityType' => EQUESTIONS_BOL_FeedService::ACTIVITY_ANSWER,
            'activityId' => $id,
            'userId' => $params['userId']
        );

        $activityData = array(
            'answerId' => $id,
            'optionId' => $optionId,
            'text' => $optionDto->text,
            'string' => '[ph:string]'
        );

        $event = new OW_Event('feed.activity', $activityParams, $activityData);
        OW::getEventManager()->trigger($event);
    }

    public function onAnswerRemove(  OW_Event $event )
    {
        $params = $event->getParams();

        $optionId = (int) $params['optionId'];
        $optionDto = EQUESTIONS_BOL_Service::getInstance()->findOption($optionId);

        $activityParams = array(
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $optionDto->questionId,
            'activityType' => EQUESTIONS_BOL_FeedService::ACTIVITY_ANSWER,
            'activityId' => $params['id']
        );

        $event =new OW_Event('feed.delete_activity', $activityParams);
        OW::getEventManager()->trigger($event);
    }

    public function onFollowAdd( OW_Event $event )
    {
        $params = $event->getParams();

        $activityParams = array(
            'pluginKey' => 'equestions',
            'userId' => $params['userId'],
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $params['questionId'],
            'activityId' => $params['userId'],
            'activityType' => 'subscribe',
            'visibility' => 14, // Visibility autor
            'time' => time()
        );

        $event = new OW_Event('feed.activity', $activityParams);
        OW::getEventManager()->trigger($event);

        $activityParams['activityType'] = EQUESTIONS_BOL_FeedService::ACTIVITY_FOLLOW;
        $activityParams['activityId'] = $params['id'];
        $activityParams['subscribe'] = false;

        $activityData = array(
            'string' => '[ph:string]'
        );

        $event = new OW_Event('feed.activity', $activityParams, $activityData);
        OW::getEventManager()->trigger($event);
    }

    public function onFollowRemove( OW_Event $event )
    {
        $params = $event->getParams();

        $activityParams = array(
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $params['questionId'],
            'activityType' => 'subscribe',
            'activityId' => $params['userId']
        );

        $event =new OW_Event('feed.delete_activity', $activityParams);
        OW::getEventManager()->trigger($event);

        $activityParams['activityType'] = EQUESTIONS_BOL_FeedService::ACTIVITY_FOLLOW;
        $activityParams['activityId'] = $params['id'];
        $activityParams['subscribe'] = false;

        $event =new OW_Event('feed.delete_activity', $activityParams);
        OW::getEventManager()->trigger($event);
    }

    public function onPostAdd( OW_Event $e )
    {
        $params = $e->getParams();

        $activityParams = array(
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => (int) $params['questionId'],
            'pluginKey' => 'equestions',
            'activityType' => EQUESTIONS_BOL_FeedService::ACTIVITY_POST,
            'activityId' => (int) $params['id'],
            'userId' => $params['userId']
        );

        $activityData = array(
            'text' => $params['text'],
            'string' => '[ph:string]'
        );

        $event = new OW_Event('feed.activity', $activityParams, $activityData);
        OW::getEventManager()->trigger($event);
    }

    public function onPostRemove( OW_Event $e )
    {
        $params = $e->getParams();

        $activityParams = array(
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $params['questionId'],
            'activityType' => EQUESTIONS_BOL_FeedService::ACTIVITY_POST,
            'activityId' => $params['id']
        );

        $event =new OW_Event('feed.delete_activity', $activityParams);
        OW::getEventManager()->trigger($event);
    }

    public function onQuestionRemove( OW_Event $event )
    {
        $params = $event->getParams();

        $questionId = $params['id'];

        $event = new OW_Event('feed.delete_item', array(
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $questionId
        ));

        OW::getEventManager()->trigger($event);
    }

    public function configurableActivity( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();
        $event->add(array(
            'label' => $language->text('equestions', 'feed_content_label'),
            'activity' => '*:' . EQUESTIONS_BOL_Service::ENTITY_TYPE
        ));
    }

    public function onAsk( OW_Event $event )
    {
        $params = $event->getParams();

        $activityParams = array(
            'pluginKey' => 'equestions',
            'userId' => $params['recipientId'],
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $params['questionId'],
            'activityId' => $params['id'],
            'activityType' => EQUESTIONS_BOL_FeedService::ACTIVITY_ASK,
            'visibility' => 4, // Visibility autor
            'time' => time()
        );

        $activityData = array(
            'action' => array(
                'userId' => $params['userId']
            ),
            'string' => '[ph:string]'
        );

        $event = new OW_Event('feed.activity', $activityParams, $activityData);
        OW::getEventManager()->trigger($event);
    }

    public function collectPrivacy( BASE_CLASS_EventCollector $event )
    {
        $event->add(array('*:' . EQUESTIONS_BOL_Service::ENTITY_TYPE, EQUESTIONS_Plugin::PRIVACY_ACTION_VIEW_MY_QUESTIONS));
    }
}
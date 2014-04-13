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
 * @package equestions.components
 */
class EQUESTIONS_CMP_FeedItem extends OW_Component
{
    private $activity = array();

    /**
     *
     * @var EQUESTIONS_BOL_Question
     */
    private $question;

    /**
     *
     * @var EQUESTIONS_BOL_Activity
     */
    private $bubbleActivity;

    private $uniqId, $lastItem = false;

    public function __construct( EQUESTIONS_BOL_Question $question, EQUESTIONS_BOL_Activity $bubbleActivity, $activity )
    {
        parent::__construct();

        $this->activity = $activity;
        $this->question = $question;
        $this->bubbleActivity = $bubbleActivity;
        $this->uniqId = uniqid('qi_' . $question->id . '_');
    }

    public function getUniqId()
    {
        return $this->uniqId;
    }

    public function setIsLastItem( $yes = true )
    {
        $this->lastItem = $yes;
    }

    private function getContextUserIds()
    {
        $out = array();

        foreach ( $this->activity as $activity )
        {
            $out[] = $activity->userId;
        }

        return $out;
    }

    /**
     *
     * @return EQUESTIONS_BOL_Activity
     */
    public function getBubbleActivity()
    {
        return $this->bubbleActivity;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $language = OW::getLanguage();
        $configs = OW::getConfig()->getValues('equestions');

        $optionTotal = EQUESTIONS_BOL_Service::getInstance()->findOptionCount($this->question->id);
        $answerCount = EQUESTIONS_BOL_Service::getInstance()->findTotalAnswersCount($this->question->id);
        $postCount = BOL_CommentService::getInstance()->findCommentCount(EQUESTIONS_BOL_Service::ENTITY_TYPE, $this->question->id);

        $questionUrl = OW::getRouter()->urlForRoute('equestions-question', array(
            'qid' => $this->question->id
        ));

        $count = EQUESTIONS_BOL_Service::DISPLAY_COUNT;
        if ( $optionTotal - $count < 2 )
        {
            $count = $optionTotal;
        }

        $answers = new EQUESTIONS_CMP_Answers($this->question, $optionTotal, array(0, $count));
        $answers->setTotalAnswerCount($answerCount);
        $answers->setUsersContext($this->getContextUserIds());

        $bubbleActivity = $this->getBubbleActivity();
        $jsSelector = 'QUESTIONS_AnswerListCollection.' . $answers->getUniqId();

        $text = $this->getItemString($bubbleActivity, $jsSelector, $questionUrl);

        $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($bubbleActivity->userId));
        $allowPopups = !isset($configs['allow_popups']) || $configs['allow_popups'];

        $features = array();

        $onClickStr = "window.location.href='$questionUrl'";

        if ( $configs['allow_comments'] )
        {
            if ( $allowPopups )
            {
                $onClickStr = "return {$jsSelector}.openQuestionDelegate(true);";
            }

            $features[] = array(
                'class' => 'q-' . $answers->getUniqId() . '-status-comments',
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

        $features[] = array(
            'class' => 'q-' . $answers->getUniqId() . '-status-votes',
            'iconClass' => 'questions_miniicon_check',
            'label' => $answerCount,
            'onclick' => $onClickStr,
            'string' => null
        );

        if ( $configs['enable_follow'] )
        {
            $onClickStr = "OW.error('" . $language->text('equestions', 'follow_not_allowed') . "')";
            $isFollowing = false;

            if ( EQUESTIONS_BOL_Service::getInstance()->isCurrentUserCanInteract($this->question) )
            {
                $userId = OW::getUser()->getId();

                $isFollowing = EQUESTIONS_BOL_Service::getInstance()->isFollow($userId, $this->question->id);
                $onClickStr = $isFollowing
                    ? $jsSelector . '.unfollowQuestion();'
                    : $jsSelector . '.followQuestion();';
            }
            else if ( OW::getUser()->isAuthenticated() )
            {
                $isFollowing = EQUESTIONS_BOL_Service::getInstance()->isFollow($userId, $this->question->id);

                if ( $isFollowing )
                {
                    $onClickStr = $jsSelector . '.unfollowQuestion();';
                }
            }

            $features[] = array(
                'class' => 'q-' . $answers->getUniqId() . '-status-follows',
                'iconClass' => 'questions_miniic_follow',
                'label' => EQUESTIONS_BOL_Service::getInstance()->findFollowsCount($this->question->id),
                'onclick' => $onClickStr,
                'active' => $isFollowing
            );
        }

        $settings = $this->question->getSettings();
        $context = empty($settings['context']['url']) || empty($settings['context']['label'])
            ? null
            : array(
                'url' => $settings['context']['url'],
                'label' => $settings['context']['label']
            );

        $tplQuestion = array(
            'questionId' => $this->question->id,
            'uniqId' => $this->getUniqId(),
            'text' => $text,
            'timeStamp' => UTIL_DateTime::formatDate($bubbleActivity->timeStamp),
            'lastItem' => $this->lastItem,
            'answers' => $answers->render(),
            'avatar' => $avatars[$bubbleActivity->userId],
            'settings' => $settings,
            'context' => $context,
            'features' => $features,
            'permalink' => $questionUrl
        );

        $this->assign('item', $tplQuestion);
    }

    private function getActivityList( $type = null, $userId = null )
    {
        $out = array();

        foreach ( $this->activity as $activity )
        {
            if ( $type !== null && $activity->activityType != $type )
            {
                continue;
            }

            if ( $userId !== null && $activity->userId != $userId )
            {
                continue;
            }

            $out[$activity->timeStamp] = $activity;
        }

        krsort($out);

        return $out;
    }

    private function getItemString( $bubbleActivity, $jsSelector, $questionUrl )
    {
        $activityType = $bubbleActivity->activityType;

        $configs = OW::getConfig()->getValues('equestions');

        $allowPopups = !isset($configs['allow_popups']) || $configs['allow_popups'];
        $onClickStr = $allowPopups ? 'onclick="return ' . $jsSelector . '.openQuestionDelegate();"' : '';

        $questionEmbed = '<a href="' . $questionUrl . '" ' . $onClickStr . '>' . $this->question->text . '</a>';

        if ( in_array($activityType, array(EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE, EQUESTIONS_BOL_FeedService::ACTIVITY_FOLLOW)) )
        {
            return OW::getLanguage()->text('equestions', 'item_text_' . $bubbleActivity->activityType, array(
                'question' => $questionEmbed
            ));
        }

        $buubleData = $bubbleActivity->getData();
        $with = '';
        if ( !empty($buubleData['text']) )
        {
            $text = UTIL_String::truncate($buubleData['text'], 50, '...');
            $with = '<a href="' . $questionUrl . '" ' . $onClickStr . '>' . $text . '</a>';
        }

        return OW::getLanguage()->text('equestions', 'item_text_' . $bubbleActivity->activityType, array(
            'question' => $questionEmbed,
            'with' => $with
        ));
    }
}

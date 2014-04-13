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
class EQUESTIONS_CMP_Question extends OW_Component
{
    public function __construct( $questionId, $userContext = null, $count = null, $options = null )
    {
        parent::__construct();

        $language = OW::getLanguage();

        $configs = OW::getConfig()->getValues('equestions');
        $count = empty($count) ? EQUESTIONS_BOL_Service::MORE_DISPLAY_COUNT : $count;

        $uniqId = uniqid('question_');
        $this->assign('uniqId', $uniqId);

        $service = EQUESTIONS_BOL_Service::getInstance();

        $userId = OW::getUser()->getId();
        $question = $service->findQuestion($questionId);

        if ( empty($question) )
        {
            $this->assign('noQuestion', true);

            return;
        }

        $settings = $question->getSettings();

        $isPoll = !$settings['allowAddOprions'];
        $optionTotal = $service->findOptionCount($questionId);
        $answerCount = $service->findTotalAnswersCount($questionId);
        $postCount = BOL_CommentService::getInstance()->findCommentCount('question', $questionId);
        $isAutor = $question->userId == $userId;

        if ( $optionTotal - $count < 10 )
        {
            $count = $optionTotal;
        }

        $limit = $count ? array(0, $count) : null;

        $answers = new EQUESTIONS_CMP_Answers($question, $optionTotal, $limit);
        $answers->setExpandedView();
        $answers->setSettings($options);


        if ( isset($options['inPopup']) && $options['inPopup'] === true )
        {
            $answers->setInPopupMode();
        }

        if ( isset($options['loadStatic']) && $options['loadStatic'] === false )
        {
            $answers->setDoNotLoadStatic();
        }

        $editable = $service->isCurrentUserCanInteract($question);
        $answers->setEditable($editable && $service->isCurrentUserCanAnswer($question));

        if ( $userContext !== null )
        {
            $answers->setUsersContext($userContext);
        }

        $answers->showAddNew();
        $this->addComponent('answers', $answers);

        $followsCount = $service->findFollowsCount($question->id, $userContext, array($question->userId));

        $statusCmp = new EQUESTIONS_CMP_QuestionStatus($answers->getUniqId(), $postCount, $answerCount, $followsCount);
        $plugin = OW::getPluginManager()->getPlugin('equestions');
        $statusCmp->setTemplate($plugin->getCmpViewDir() . 'question_static_status.html');
        $this->addComponent('questionStatus', $statusCmp);

        $tplQuestion = array(
            'text' => nl2br($question->text)
        );

        $this->assign('question', $tplQuestion);
        $js = UTIL_JsGenerator::newInstance()->newObject('question', 'QUESTIONS_Question', array($uniqId, $question->id));

        if ( $configs['allow_comments'] )
        {
            $commentsParams = new BASE_CommentsParams('equestions', EQUESTIONS_BOL_Service::ENTITY_TYPE);
            $commentsParams->setEntityId($question->id);
            $commentsParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_TOP_FORM_WITH_PAGING);
            $commentsParams->setCommentCountOnPage(5);
            $commentsParams->setOwnerId($question->userId);

            $commentsParams->setAddComment($editable);

            $commentCmp = new BASE_CMP_Comments($commentsParams);
            //$commentTemplate = OW::getPluginManager()->getPlugin('equestions')->getCmpViewDir() . 'comments.html';
            //$commentCmp->setTemplate($commentTemplate);

            $this->addComponent('comments', $commentCmp);

            if ( !empty($options['focusToPost']) )
            {
                $js->addScript('question.focusOnPostInput()');
            }
        }

        $jsSelector = 'QUESTIONS_AnswerListCollection.' . $answers->getUniqId();

        $js->addScript('question.setAnswerList(' . $jsSelector . ');');

        if ( !empty($options['relation']) )
        {
            $js->addScript($jsSelector . '.setRelation("' . $options['relation'] . '");');
        }

        $js->equateVarables(array('QUESTIONS_QuestionColletction', $uniqId), 'question');
        OW::getDocument()->addOnloadScript($js);

        $toolbar = array();

        if ( $service->isCurrentUserCanInteract($question) )
        {
            if ( $configs['enable_follow'] )
            {
                $this->assign('follow', array(
                    'isFollow' => $service->isFollow($userId, $question->id),
                    'followId' => $answers->getUniqId() . '-follow',
                    'unfollowId' => $answers->getUniqId() . '-unfollow',
                    'followClick' => $jsSelector . '.followQuestion()',
                    'unfollowClick' => $jsSelector . '.unfollowQuestion()'
                ));
            }

            if ( $configs['ask_friends'] )
            {
                $friendMode = (bool) OW::getEventManager()->call('plugin.friends');
                $askLabel = $friendMode ? $language->text('equestions', 'toolbar_ask_friends') : $language->text('equestions', 'toolbar_ask_users');

                $this->assign('ask', array(
                    'label' => $askLabel,
                    'onClick' => $jsSelector . '.showUserSelector()'
                ));
            }
        }

        if ( $isPoll )
        {
            $list = $service->findUserAnswerListByQuestionId($userId, $question->id);

            if ( count($list) )
            {
                $toolbar[] = array(
                    'label' => '<a id="' . $answers->getUniqId() . '-unvote" href="javascript://" onclick="' .$jsSelector . '.unvote()">' .
                        $language->text('equestions', 'toolbar_unvote_btn') . '</a>'
                );
            }
        }

        if ( $service->isCurrentUserCanEdit($question) )
        {
            $condEmbed = "confirm('" . $language->text('equestions', 'delete_question_confirm') . "')";
            $toolbar[] = array(
                'label' => '<a href="javascript://" onclick="if(' . $condEmbed . ') ' .$jsSelector . '.deleteQuestion();">' .
                        $language->text('equestions', 'toolbar_delete_btn') . '</a>'
            );
        }

        $userData = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($question->userId));
        $questionInfo = array(
            'avatar' => $userData[$question->userId],
            'profileUrl' => $userData[$question->userId]['url'],
            'displayName' => $userData[$question->userId]['title'],
            'content' => '',
            'toolbar' => $toolbar,
            'date' => UTIL_DateTime::formatDate($question->timeStamp)
        );

        $this->assign('questionInfo', $questionInfo);
    }
}
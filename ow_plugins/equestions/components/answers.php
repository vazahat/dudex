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
class EQUESTIONS_CMP_Answers extends OW_Component
{

    /**
     *
     * @var EQUESTIONS_BOL_Service
     */
    private $service;

    /**
     *
     * @var EQUESTIONS_BOL_Question
     */
    private $question;

    private $uniqId, $userId, $poll = false, $editable, $limit, $startStamp, $totalAnswerCount = null, $questionUrl,
            $userContext = null, $viewMore = 0, $optionTotal = 0, $showAddNew = null, $editMode = null, $settings = array(),
            $doNotLoadStatic = false, $attachments = array(), $expandedView = false, $inPopupMode = false;

    public function __construct( EQUESTIONS_BOL_Question $question, $optionTotal, array $listLimit = null, $uniqId = null )
    {
        parent::__construct();

        $this->uniqId = empty($uniqId) ? uniqid('questionsAnswers_') : $uniqId;
        $this->question = $question;
        $this->limit = $listLimit;
        $this->startStamp = time();
        $this->service = EQUESTIONS_BOL_Service::getInstance();
        $this->userId = OW::getUser()->getId();

        $this->attachment = $this->question->getAttachment();

        $this->editMode = $this->service->isCurrentUserCanEdit($question);
        $settings = json_decode($this->question->settings, true);

        $this->poll = !$settings['allowAddOprions'];

        $this->optionTotal = $optionTotal;
        $this->viewMore = $this->optionTotal - (empty($this->limit[1]) ? $this->optionTotal : $this->limit[1]);
        $this->viewMore = $this->viewMore > 0 ? $this->viewMore : 0;

        $jsConstructor = $this->poll ? 'QUESTIONS_PollAnswers' : 'QUESTIONS_QuestionAnswers';
        $js = UTIL_JsGenerator::newInstance()->newObject(array('QUESTIONS_AnswerListCollection', $this->uniqId), $jsConstructor);
        OW::getDocument()->addOnloadScript($js);

        $this->questionUrl = OW::getRouter()->urlForRoute('equestions-question', array(
            'qid' => $this->question->id
        ));
    }

    public function setExpandedView( $yes = true )
    {
        $this->expandedView = $yes;
    }

    public function setSettings( $settings )
    {
        $this->settings = $settings;
    }

    public function setInPopupMode( $yes = true )
    {
        $this->inPopupMode = $yes;
    }

    public function setDoNotLoadStatic( $yes = true )
    {
        $this->doNotLoadStatic = $yes;
    }

    public function setEditable( $yes = true )
    {
        $this->editable = $yes;
    }

    public function setStartStamp( $timeStamp )
    {
            $this->startStamp = $timeStamp;
    }

    public function setTotalAnswerCount( $count )
    {
            $this->totalAnswerCount = $count;
    }

    public function setUsersContext( $userIds )
    {
        $this->userContext = $userIds;
    }

    public function getUniqId()
    {
        return $this->uniqId;
    }

    public function showAddNew( $yes = true )
    {
        $this->showAddNew = $yes;
    }

    private function isAddNewAvaliable()
    {
        $defaultBeh = !$this->viewMore;
        $addNew = $this->service->isCurrentUserCanAddOptions($this->question);

        return $this->showAddNew !== null ? $this->showAddNew && $addNew : $defaultBeh && $addNew;
    }

    public function onBeforeRender()
    {
        if ( !empty($this->attachment) )
        {
            if ( $this->expandedView )
            {
                $this->attachment['onclick'] = null;
            }
            else
            {
                $this->attachment['onclick'] = 'QUESTIONS_AnswerListCollection.' . $this->uniqId . '.openQuestion(); return false;';

                if ( $this->attachment['type'] != 'link' )
                {
                    $this->attachment['href'] = $this->questionUrl;
                }
            }

            $attachCmp = new EQUESTIONS_CMP_Attachment($this->attachment, $this->expandedView);
            $this->addComponent('attachment', $attachCmp);
        }

        if ( !$this->doNotLoadStatic )
        {
            EQUESTIONS_Plugin::getInstance()->addStatic();
        }

        if ( $this->editable === null )
        {
            $this->editable = $this->service->isCurrentUserCanInteract($this->question) && $this->service->isCurrentUserCanAnswer($this->question);
        }

        $tmp = $this->service->findOptionListAndAnswerCountList($this->question->id, $this->startStamp, $this->userContext, $this->limit);

        $optionsDtoList = $tmp['optionList'];
        $countList = $tmp['countList'];

        $userContext = null;

        if ( is_array($this->userContext) )
        {
            $userContext = $this->userContext;
            $userContext[] = $this->userId;
            $userContext = array_unique($userContext);
        }


        $totalAnswerCount = $this->totalAnswerCount == null
                ? $this->service->findTotalAnswersCount($this->question->id)
                : $this->totalAnswerCount;

        $answerCount = $this->poll
                ? $totalAnswerCount
                : $this->service->findMaxAnswersCount($this->question->id);

        $optionList = new EQUESTIONS_CMP_OptionList($optionsDtoList, $this->uniqId, $this->userId);
        $optionList->setAnswerCount($answerCount);
        $optionList->setEditable($this->editable);
        $optionList->setIsPoll($this->poll);
        $optionList->setUsersContext($userContext);
	$optionList->setEditMode($this->editMode);

        $options = $optionList->initOption($countList);
        $shareData = array(
            'userId' => $this->userId,
            'ownerId' => $this->question->userId,
            'editable' => $this->editable,
            'editMode' => $this->editMode,
            'questionId' => $this->question->id,
            'totalAnswers' => $answerCount,
            'poll' => $this->poll,
            'uniqId' => $this->uniqId,
            'userContext' => $userContext,
            'displayedCount' => count($options),
            'optionTotal' => $this->optionTotal,
	        'startStamp' => $this->startStamp,
            'ignoreOptions' => array(),
            'expandedView' => $this->expandedView,
            'inPopupMode' => $this->inPopupMode,
            'ownerMode' => $this->userId == $this->question->userId
        );

        $shareData['offset'] = $shareData['displayedCount'];
        $shareData['st']['displayedCount'] = $shareData['displayedCount'];
        $shareData['st']['optionTotal'] = $shareData['optionTotal'];

        $jsAccessor = 'questionAnswers';
        $js = UTIL_JsGenerator::newInstance();
        $js->equateVarables($jsAccessor, array('QUESTIONS_AnswerListCollection', $this->uniqId));

        $js->callFunction(array($jsAccessor, 'init'), array($this->uniqId, $options, $shareData, !$this->editable) );
        $js->callFunction(array($jsAccessor, 'setResponder'), array( OW::getRouter()->urlFor('EQUESTIONS_CTRL_Questions', 'rsp')) );

        $this->assign('viewMore', $this->viewMore);
		$this->assign('editMode', $this->editMode);

        if ( $this->viewMore )
        {
            $js->callFunction(array($jsAccessor, 'initViewMore'), array() );

            $this->assign('viewMoreUrl', $this->questionUrl);
        }

        $addNewAvaliable = $this->editable && !$this->poll;
        $this->assign('addNew', $addNewAvaliable);
        $this->assign('hideAddNew', !$this->isAddNewAvaliable());
        if ( $addNewAvaliable )
        {
            $js->callFunction(array($jsAccessor, 'initAddNew'), array() );
        }

        OW::getDocument()->addOnloadScript($js);

        $this->assign('uniqId', $this->uniqId);
        $this->addComponent('list', $optionList);

        OW::getLanguage()->addKeyForJs('equestions', 'option_not_empty_delete_warning');
        OW::getLanguage()->addKeyForJs('equestions', 'question_fb_title');
        OW::getLanguage()->addKeyForJs('equestions', 'users_fb_title');
    }
}


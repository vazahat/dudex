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
class EQUESTIONS_CMP_OptionList extends OW_Component
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
    private $questionDto;

    private $uniqId, $userId, $editable = true, $editMode = false, $usersContext = null, $answerCount = 0,
            $poll = false, $optionList = array(), $optionIdList = array(), $optionDtoList = array();

    public function __construct( array $optionList, $uniqId, $userId )
    {
        parent::__construct();

        $this->optionDtoList = $optionList;

        $this->service = EQUESTIONS_BOL_Service::getInstance();
        $this->userId = $userId;
        $this->uniqId = $uniqId;
    }

    public function setIsPoll( $yes = true )
    {
        $this->poll = $yes;
    }

    public function setEditMode( $yes = true )
    {
            $this->editMode = $yes;
    }

    public function setEditable( $yes = true )
    {
        $this->editable = $yes;
    }

    public function setUsersContext( $userIds )
    {
        $this->usersContext = $userIds;
    }

    public function setAnswerCount( $count )
    {
        $this->answerCount = (int) $count;
    }

    public function initOption( $countList, $optionCount = null )
    {
        if ( empty($this->optionDtoList) )
        {
            return array();
        }

        foreach ( $this->optionDtoList as $opt )
        {
            $this->optionIdList[] = $opt->id;
            $cmp = new EQUESTIONS_CMP_Answer($opt, $this->uniqId);
            $this->optionList[$opt->id] = $cmp;

            $cmp->setDisbled( !$this->editable );
            $cmp->setIsMultiple(!$this->poll);
        }

        $checkedOptions = array();
        $optionsState = array();
        $totalAnswers = $this->answerCount;

        //$countList = $this->service->findAnswersCount($this->optionIdList);
        $answerDtoList = $this->service->findUserAnswerList($this->userId, $this->optionIdList);

        foreach ( $answerDtoList as $item )
        {
            $checkedOptions[] = $item->optionId;
            $this->getOption($item->optionId)->setVoted();
        }

        $optionCount = empty($optionCount) ? count($this->optionDtoList) : $optionCount;

        foreach ( $this->optionDtoList as $optionDto )
        {
            $optionId = $optionDto->id;
            $checked = in_array($optionId, $checkedOptions);

            $voteCount = $countList[$optionId];
            $users = $this->service->findAnsweredUserIdList($optionId, $this->usersContext, $checked ? 4 : 3);

            $optionsState[] = array(
                'id' => $optionId,
                'users' => $users,
                'voteCount' => $voteCount,
                'checked' => $checked
            );

            $this->getOption($optionId)->setVoteCount($voteCount);
            $this->getOption($optionId)->setUsers($users);

            $canEdit = $optionDto->userId == $this->userId
                    && ( $voteCount == 0 || $voteCount == 1 && $checked );

            $canEdit = $this->editMode || $canEdit;
            $canEdit = $this->poll ? $canEdit && $optionCount > 2 : $canEdit;

            $this->getOption($optionId)->setEditMode($canEdit);

            if ($totalAnswers)
            {
                $this->getOption($optionId)->setPercents($voteCount * 100 / $totalAnswers);
            }
        }

        return $optionsState;
    }

    /**
     *
     * @param $optionId
     * @return EQUESTIONS_CMP_Answer
     */
    public function getOption( $optionId )
    {
        return $this->optionList[$optionId];
    }

    public function getOptionList()
    {
        return $this->optionList;
    }

    public function onBeforeRender()
    {
        $list = array();
        foreach ( $this->optionIdList as $optionId )
        {
            if ( empty($this->optionList[$optionId]) )
            {
                continue;
            }

            $list[] = $this->getOption($optionId)->render();
        }

        $this->assign('list', $list);
    }
}
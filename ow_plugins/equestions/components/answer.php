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
class EQUESTIONS_CMP_Answer extends OW_Component
{
    /**
     *
     * @var EQUESTIONS_BOL_Service
     */
    private $service;

    /**
     *
     * @var EQUESTIONS_BOL_Option
     */
    private $option;

    private $voted = false, $voteCount = 0, $percents = 0, $userIds = array(), $multiple = true,
		$disabled = false, $editMode = false;

    public function __construct( EQUESTIONS_BOL_Option $opt, $uniqId)
    {
        parent::__construct();

        $this->option = $opt;

        $this->assign('questionUniqId', $uniqId);
    }

    public function setEditMode( $yes = true )
    {
            $this->editMode = $yes;
    }

    public function setVoteCount( $voteCount )
    {
        $this->voteCount = $voteCount;
    }

    public function setPercents( $percents )
    {
        $this->percents = $percents;
    }

    public function setVoted( $voted = true )
    {
        $this->voted = (bool) $voted;
    }

    public function setDisbled( $disabled = true )
    {
        $this->disabled = (bool) $disabled;
    }

    public function setUsers( $users )
    {
        $this->userIds = $users;
    }

    public function setIsMultiple( $multiple = true )
    {
        $this->multiple = $multiple;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $tplOption = array();
        $tplOption['id'] = $this->option->id;
        $tplOption['text'] = $this->option->text;
        $tplOption['count'] = $this->voteCount;
        $tplOption['percents'] = $this->percents;
        $tplOption['voted'] = $this->voted;
        $tplOption['multiple'] = $this->multiple;
        $tplOption['disabled'] = $this->disabled;
        $tplOption['editMode'] = $this->editMode;

        $avatarList = new EQUESTIONS_CMP_Avatars( $this->userIds, $this->voteCount );

        $tplOption['users'] = $avatarList->render();
        $this->assign('option', $tplOption);
    }

}
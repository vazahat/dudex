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
class EQUESTIONS_CMP_QuestionStatus extends OW_Component
{
    /**
     * Constructor.
     *
     * @param array $idList
     */
    public function __construct( $uniqId, $postCount, $voteCount, $followCount, $questionUrl = null )
    {
        parent::__construct();

        $configs = OW::getConfig()->getValues('equestions');

        $this->assign('allowPopups', !isset($configs['allow_popups']) || $configs['allow_popups']);

        $allowComments = OW::getConfig()->getValue('equestions', 'allow_comments');
        $allowFollows = OW::getConfig()->getValue('equestions', 'enable_follow');

        $this->assign('uniqId', $uniqId);
        $this->assign('questionUrl', $questionUrl);

        $this->assign('postCount', $allowComments ? $postCount : 0);
        $this->assign('voteCount', $voteCount);
        $this->assign('followCount', $allowFollows ? $followCount : 0);
    }

}
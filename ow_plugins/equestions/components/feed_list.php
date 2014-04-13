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
class EQUESTIONS_CMP_FeedList extends OW_Component
{
    private $feed = array(), $activityList = array(), $bubbleActivityList = array();

    public function __construct( $feed, $bubbleActivityList, $activityList )
    {
        parent::__construct();

        $this->bubbleActivityList = $bubbleActivityList;
        $this->activityList = $activityList;
        $this->feed = $feed;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $cmpList = array();
        $counter = 0;
        $questionCount = count($this->feed);

        foreach ( $this->feed as $question )
        {
            $bubbleActivity = $this->bubbleActivityList[$question->id];

            $counter++;
            $activityList = empty($this->activityList[$question->id])
                ? array()
                : $this->activityList[$question->id];

            $cmp = new EQUESTIONS_CMP_FeedItem($question, $bubbleActivity, $activityList);

            if ( $questionCount == $counter )
            {
                $cmp->setIsLastItem();
            }

            $cmpList[] = $cmp->render();
        }

        $this->assign('list', $cmpList);
    }
}

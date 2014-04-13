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
class EQUESTIONS_CMP_MainFeed extends EQUESTIONS_CMP_Feed
{
    public function findFeed( $startStamp, $count, $questionIds, $order )
    {
        if ( $order == EQUESTIONS_CMP_Feed::ORDER_LATEST )
        {
            return $this->service->findMainFeed($startStamp, $count, $questionIds);
        }

        return $this->service->findOrderedMainFeed($startStamp, $count, $questionIds, array(
            EQUESTIONS_BOL_FeedService::ACTIVITY_ANSWER,
            EQUESTIONS_BOL_FeedService::ACTIVITY_POST
        ));
    }

    public function findActivity( $startStamp, $questionIds )
    {
        return $this->service->findMainActivity($startStamp, $questionIds);
    }

    public function findFeedCount( $startStamp )
    {
        return $this->service->findMainFeedCount($startStamp);
    }
}


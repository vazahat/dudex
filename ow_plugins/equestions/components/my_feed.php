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
class EQUESTIONS_CMP_MyFeed extends EQUESTIONS_CMP_Feed
{
    public function findFeed( $startStamp, $count, $questionIds, $order )
    {
        if ( $order == EQUESTIONS_CMP_Feed::ORDER_LATEST )
        {
            return $this->service->findMyFeed($startStamp, $this->userId, $count, $questionIds);
        }

        return $this->service->findOrderedMyFeed($startStamp, $this->userId, $count, $questionIds, array(
            EQUESTIONS_BOL_FeedService::ACTIVITY_ANSWER,
            EQUESTIONS_BOL_FeedService::ACTIVITY_POST
        ));
    }

    public function findActivity( $startStamp, $questionIds )
    {
        return $this->service->findMyActivity($startStamp, $this->userId, $questionIds);
    }

    public function findFeedCount( $startStamp )
    {
        return $this->service->findMyFeedCount($startStamp, $this->userId);
    }

    public function getBubbleActivityList( $activityList )
    {
        $out = array();
        foreach ( $activityList as $questionId => $activity )
        {
            foreach ( $activity as $item )
            {
                if ( $item->userId == $this->userId )
                {
                    $out[$questionId] = $item;

                    break;
                }
            }
        }

        return $out;
    }

    /*public function getBubbleActivityList( $activityList )
    {
        $prior = array(
            EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE => 0,
            EQUESTIONS_BOL_FeedService::ACTIVITY_FOLLOW => 1,
            EQUESTIONS_BOL_FeedService::ACTIVITY_ANSWER => 2,
            EQUESTIONS_BOL_FeedService::ACTIVITY_POST => 2
        );

        $tmp = array();
        $out = array();
        foreach ( $activityList as $questionId => $activity )
        {
            foreach ( $activity as $item )
            {
                if ( $item->userId == $this->userId )
                {
                    $tmp[$questionId][$prior[$item->activityType]][] = $item;
                }
            }
        }

        foreach ( $tmp as $questionId => $item )
        {
            ksort($item);
            $priorActivities = reset($item);
            $out[$questionId] = reset($priorActivities);
        }

        return $out;
    }*/
}


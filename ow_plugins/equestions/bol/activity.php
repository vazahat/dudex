<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.bol
 * @since 1.0
 */
class EQUESTIONS_BOL_Activity extends OW_Entity
{
    public $questionId;

    public $activityType;

    public $activityId;

    public $userId;

    public $timeStamp;

    public $privacy;

    public $data;

    public function __construct()
    {
        $this->privacy = EQUESTIONS_BOL_FeedService::PRIVACY_EVERYBODY;
    }

    public function setData( $data )
    {
        $this->data = json_encode($data);
    }

    public function getData()
    {
        if ( empty($this->data) )
        {
            return array();
        }

        return json_decode($this->data, true);
    }
}

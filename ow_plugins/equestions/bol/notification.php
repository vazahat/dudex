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
class EQUESTIONS_BOL_Notification extends OW_Entity
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $senderId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $questionId;

    /**
     * @var int
     */
    public $timeStamp;

    /**
     * @var int
     */
    public $viewed;

    /**
     * @var int
     */
    public $special;

    /**
     * @var string
     */
    public $data;

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

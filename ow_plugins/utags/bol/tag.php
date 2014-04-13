<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.bol
 */
class UTAGS_BOL_Tag extends OW_Entity
{
    const STATUS_ACTIVE = 'active';
    const STATUS_APPROVAL = 'approval';
    
    public $userId;
    public $entityType;
    public $entityId;
    public $photoId;
    public $copyPhotoId;
    public $timeStamp;
    public $status = self::STATUS_ACTIVE;

    public $data = '{}';

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

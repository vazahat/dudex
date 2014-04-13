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
 * @package equestions.bol
 */
class EQUESTIONS_BOL_Question extends OW_Entity
{
    public $userId;

    public $text;

    public $settings;

    public $timeStamp;

    public $attachment;

    public function setSettings( $settings )
    {
        $this->settings = json_encode($settings);
    }

    public function getSettings()
    {
        if ( empty($this->settings) )
        {
            return array();
        }

        return json_decode($this->settings, true);
    }

    public function setAttachment( $oembed )
    {
        $this->attachment = json_encode($oembed);
    }

    public function getAttachment()
    {
        if ( empty($this->attachment) )
        {
            return array();
        }

        return json_decode($this->attachment, true);
    }
}

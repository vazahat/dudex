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
 * @package gheader.bol
 */
class GHEADER_BOL_Cover extends OW_Entity
{
    const STATUS_ACTIVE = 'active';
    const STATUS_TMP = 'tmp';

    public $groupId;

    public $file;

    public $settings = '{}';

    public $timeStamp;

    public $status;

    public function __construct()
    {

    }

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
}

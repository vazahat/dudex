<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo.bol
 */
class OASEO_BOL_Data extends OW_Entity
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $data;

    public function getKey()
    {
        return $this->key;
    }

    public function setKey( $key )
    {
        $this->key = $key;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData( $data )
    {
        $this->data = $data;
    }
}


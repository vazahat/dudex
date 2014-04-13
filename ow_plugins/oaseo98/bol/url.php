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
class OASEO_BOL_Url extends OW_Entity
{
    /**
     * @var string
     */
    public $routeName;
    /**
     * @var string
     */
    public $url;

    public function getRouteName()
    {
        return $this->routeName;
    }

    public function setRouteName( $routeName )
    {
        $this->routeName = $routeName;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl( $url )
    {
        $this->url = $url;
    }
}


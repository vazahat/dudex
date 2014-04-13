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
class OASEO_BOL_SitemapItem extends OW_Entity
{
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $value;
    /**
     * @var int
     */
    public $addTs;
    
    public function getType()
    {
        return $this->type;
    }

    public function setType( $type )
    {
        $this->type = $type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue( $value )
    {
        $this->value = $value;
    }
    
    public function getAddTs()
    {
        return $this->addTs;
    }

    public function setAddTs($addTs)
    {
        $this->addTs = (int)$addTs;
    }

}


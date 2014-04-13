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
class OASEO_BOL_Slug extends OW_Entity
{
    /**
     * @var string
     */
    public $entityType;
    /**
     * @var integer
     */
    public $entityId;
    /**
     * @var book
     */
    public $active;
    /**
     * @var string
     */
    public $string;

    /**
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * @param string $entityType
     */
    public function setEntityType( $entityType )
    {
        $this->entityType = $entityType;
    }

    /**
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param integer $entityId
     */
    public function setEntityId( $entityId )
    {
        $this->entityId = $entityId;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return (bool) $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive( $active )
    {
        $this->active = (bool) $active;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * @param string $string
     */
    public function setString( $string )
    {
        $this->string = $string;
    }
}


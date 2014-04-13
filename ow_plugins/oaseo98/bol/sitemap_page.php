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
class OASEO_BOL_SitemapPage extends OW_Entity
{
    /**
     * @var string
     */
    public $url;
    /**
     * @var string
     */
    public $meta;
    /**
     * @var string
     */
    public $title;
    /**
     * @var boolean
     */
    public $status;
    /**
     * @var int
     */
    public $processTs;
    /**
     * @var boolean
     */
    public $broken;

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl( $url )
    {
        $this->url = $url;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta( $meta )
    {
        $this->meta = $meta;
    }

    public function getStatus()
    {
        return (bool)$this->status;
    }

    public function setStatus( $status )
    {
        $this->status = (bool)$status;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle( $title )
    {
        $this->title = $title;
    }
    
    public function getProcessTs()
    {
        return $this->processTs;
    }

    public function setProcessTs($processTs)
    {
        $this->processTs = (int)$processTs;
    }

    public function getBroken()
    {
        return (bool)$this->broken;
    }

    public function setBroken( $broken )
    {
        $this->broken = (int)$broken ? 1 : 0;
    }
}


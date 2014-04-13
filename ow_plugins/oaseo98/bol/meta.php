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
class OASEO_BOL_Meta extends OW_Entity
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $meta;
    /**
     * @var string
     */
    public $uri;
    /**
     * @var string
     */
    public $dispatchAttrs;


    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key 
     */
    public function setKey( $key )
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param string $meta 
     */
    public function setMeta( $meta )
    {
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri( $uri )
    {
        $this->uri = $uri;
    }
    
    public function getDispatchAttrs()
    {
        return $this->dispatchAttrs;
    }

    public function setDispatchAttrs( $dispatchAttrs )
    {
        $this->dispatchAttrs = $dispatchAttrs;
    }
}


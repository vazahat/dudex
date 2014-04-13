<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Kairat Bakytow
 * @package ow_plugins.antibruteforce.bol
 * @since 1.0
 */
class ANTIBRUTEFORCE_BOL_BlockIp extends OW_Entity
{
    public $ip;
    
    public function getIp()
    {
        return (int)$this->ip;
    }
    
    public function setIp( $value )
    {
        $this->ip = (int)sprintf("%u", ip2long($value));
        
        return $this;
    }
    
    public $time;
    
    public function getTime()
    {
        return (int)$this->time;
    }
    
    public function setTime( $value )
    {
        $this->time = (int)$value;
        
        return $this;
    }
}

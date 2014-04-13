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
 * @package ow_plugins.antibruteforce
 * @since 1.0
 */
class ANTIBRUTEFORCE_Cron extends OW_Cron
{
    public function __construct()
    {
        parent::__construct();

        $this->addJob( 'deleteBlockIp', 1 );
    }
    
    public function run()
    {

    }
    
    public function deleteBlockIp()
    {
        ANTIBRUTEFORCE_BOL_Service::getInstance()->deleteBlockIp();
    }
}

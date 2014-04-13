<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

class PROFILEPROGRESSBAR_Cron extends OW_Cron
{
    public function __construct()
    {
        parent::__construct();

        $this->addJob('deleteFeature', 60);
    }

    public function run()
    {

    }

    public function deleteFeature()
    {
        PROFILEPROGRESSBAR_BOL_ActivityLogDao::getInstance()->deleteCompletedFeatures();
    }
}

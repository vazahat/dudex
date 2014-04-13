<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/** 
 * 
 *
 * @author Kairat Bakytow <kainisoft@gmail.com>
 * @package ow_plugins.profileprogressbar.components
 * @since 1.0
 */
class PROFILEPROGRESSBAR_CMP_Hint extends OW_Component
{
    public function __construct( $userId )
    {
        if ( empty($userId) )
        {
            $this->setVisible(FALSE);
            return;
        }
        
        $data = PROFILEPROGRESSBAR_BOL_Service::getInstance()->getProgressbarData($userId);
        
        OW::getDocument()->addOnloadScript(
            UTIL_JsGenerator::composeJsString(';var progressbar = $("#profile-progressbar-{$userId}");
                var complete = {$complete};

                progressbar.find(".profile-progressbar-caption").text(complete + "%");
                progressbar.find(".profile-progressbar-complete").animate({width: complete + "%"}, 
                {
                    duration: "slow",
                    specialEasing: {width: "easeOutBounce"},
                    queue: false
                });', array(
                    'userId' => (int)$userId, 
                    'complete' => round(($data[PROFILEPROGRESSBAR_BOL_Service::KEY_PROGRESSBAR][PROFILEPROGRESSBAR_BOL_Service::COUNT_COMPLETED_QUESTION] * 100) / $data[PROFILEPROGRESSBAR_BOL_Service::KEY_PROGRESSBAR][PROFILEPROGRESSBAR_BOL_Service::COUNT_QUESTION])
                )
            )
        );
        
        $this->assign('userId', $userId);
    }
}

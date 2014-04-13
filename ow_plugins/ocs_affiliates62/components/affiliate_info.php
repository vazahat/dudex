<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate edit component
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.components
 * @since 1.5.3
 */
class OCSAFFILIATES_CMP_AffiliateInfo extends OW_Component
{
    public function __construct( $affiliateId, $adminMode = false )
    {
        parent::__construct();

        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $affiliate = $service->findAffiliateById($affiliateId);

        if ( !$affiliate )
        {
            $this->setVisible(false);

            return;
        }

        $this->assign('affiliate', $affiliate);
        $this->assign('adminMode', $adminMode);
        $this->assign('creditsEnabled', OW::getPluginManager()->isPluginActive('usercredits'));

        if ( $adminMode )
        {
            $this->assign('assoc', $service->getAffiliateAssocUser($affiliateId));

            $script = '$("#assign-assoc").click(function(){
                assignUserFloatBox = OW.ajaxFloatBox(
                    "OCSAFFILIATES_CMP_AssignUser",
                    { affiliateId: ' . $affiliateId . ' },
                    { width: 500, title: ' . json_encode(OW::getLanguage()->text('ocsaffiliates', 'assign')) . ' }
                );
            });
            ';
            OW::getDocument()->addOnloadScript($script);
        }
    }
}
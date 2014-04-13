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
class OCSAFFILIATES_CMP_AffiliatePayouts extends OW_Component
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

        $this->assign('payoutList', $service->getPayoutListForAffiliate($affiliateId));

        $billingService = BOL_BillingService::getInstance();
        $this->assign('currency', $billingService->getActiveCurrency());

        $this->assign('adminMode', $adminMode);
    }
}
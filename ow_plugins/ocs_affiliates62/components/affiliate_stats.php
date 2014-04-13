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
class OCSAFFILIATES_CMP_AffiliateStats extends OW_Component
{
    public function __construct( $affiliateId )
    {
        parent::__construct();

        $service = OCSAFFILIATES_BOL_Service::getInstance();
        $affiliate = $service->findAffiliateById($affiliateId);

        if ( !$affiliate )
        {
            $this->setVisible(false);

            return;
        }

        $billingService = BOL_BillingService::getInstance();
        $this->assign('currency', $billingService->getActiveCurrency());

        $clicksCount = $service->countClicksForAffiliate($affiliateId);
        $this->assign('clicksCount', $clicksCount);

        $signupCount = $service->countRegistrationsForAffiliate($affiliateId);
        $this->assign('signupCount', $signupCount);

        $salesCount = $service->countSalesForAffiliate($affiliateId);
        $this->assign('salesCount', $salesCount);

        $clicksSum = $service->getClicksSumForAffiliate($affiliateId);
        $this->assign('clicksSum', $clicksSum);

        $signupSum = $service->getRegistrationsSumForAffiliate($affiliateId);
        $this->assign('signupSum', $signupSum);

        $salesSum = $service->getSalesSumForAffiliate($affiliateId);
        $this->assign('salesSum', $salesSum);

        $earnings = $clicksSum + $signupSum + $salesSum;
        $this->assign('earnings', $earnings);

        $payouts = $service->getPayoutSum($affiliateId);
        $this->assign('payouts', $payouts);

        $balance = $earnings - $payouts;
        $this->assign('balance', $balance);

        $this->assign('affiliate', $affiliate);
    }
}
<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate cron job
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates
 * @since 1.5.3
 */
class OCSAFFILIATES_Cron extends OW_Cron
{
    /**
     *
     * @var OCSAFFILIATES_BOL_Service
     */
    private $service;

    public function __construct()
    {
        parent::__construct();

        $this->addJob('checkSales', 5);
        $this->addJob('expireResetPasswords', 60);

        $this->service = OCSAFFILIATES_BOL_Service::getInstance();
    }


    public function run() { }


    public function checkSales()
    {
        $sales = $this->service->getUntrackedSales(10);

        if ( !$sales )
        {
            return;
        }

        foreach ( $sales as $sale )
        {
            $this->service->trackSale($sale->userId, $sale->id, $sale->totalAmount);
        }

        return;
    }

    public function expireResetPasswords()
    {
        $this->service->deleteExpiredResetPasswordCodes();
    }
}
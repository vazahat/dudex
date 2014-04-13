<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Transfer Object for `ocsaffiliates_payout` table.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
class OCSAFFILIATES_BOL_Payout extends OW_Entity
{
    /**
     * @var int
     */
    public $affiliateId;
    /**
     * @var float
     */
    public $amount;
    /**
     * @var int
     */
    public $paymentDate;
    /**
     * @var string
     */
    public $method;
}
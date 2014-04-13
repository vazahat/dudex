<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Transfer Object for `ocsaffiliates_affiliate` table.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
class OCSAFFILIATES_BOL_Affiliate extends OW_Entity
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $paymentDetails;
    /**
     * @var string
     */
    public $status;
    /**
     * @var int
     */
    public $emailVerified;
    /**
     * @var int
     */
    public $registerStamp;
    /**
     * @var int
     */
    public $activityStamp;
    /**
     * @var int
     */
    public $joinIp;
    /**
     * @var int
     */
    public $userId = null;
}
<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `ocsaffiliates_verification` table.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
class OCSAFFILIATES_BOL_VerificationDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var OCSAFFILIATES_BOL_VerificationDao
     */
    private static $classInstance;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns an instance of class.
     *
     * @return OCSAFFILIATES_BOL_VerificationDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'OCSAFFILIATES_BOL_Verification';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ocsaffiliates_verification';
    }

    /**
     * @param string $code
     * @return OCSAFFILIATES_BOL_Verification
     */
    public function findByCode( $code )
    {
        $example = new OW_Example();
        $example->andFieldEqual('code', $code);
        
        return $this->findObjectByExample($example);
    }

    /**
     * @param int $affiliateId
     */
    public function deleteByAffiliateId( $affiliateId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('affiliateId', $affiliateId);
        
        $this->deleteByExample($example);
    }

    /**
     * @param int $affiliateId
     * @return OCSAFFILIATES_BOL_Affiliate
     */
    public function findByAffiliateId( $affiliateId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('affiliateId', $affiliateId);
        
        return $this->findObjectByExample($example);
    }
}
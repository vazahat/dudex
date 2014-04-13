<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `membership_user` table.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.membership.bol
 * @since 1.0
 */
class MEMBERSHIP_BOL_MembershipUserDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var MEMBERSHIP_BOL_MembershipUserDao
     */
    private static $classInstance;

    const MEMBERSHIP_EXPIRATION_INTERVAL = 7200; // 2 hours

    /**
     * Class constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns an instance of class
     *
     * @return MEMBERSHIP_BOL_MembershipUserDao
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
     */
    public function getDtoClassName()
    {
        return 'MEMBERSHIP_BOL_MembershipUser';
    }

    /**
     * @see OW_BaseDao::getTableName()
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'membership_user';
    }

    /**
     * Finds user membership by user Id
     * 
     * @param int $userId
     * @return MEMBERSHIP_BOL_MembershipUser
     */
    public function findByUserId( $userId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);
        $example->setLimitClause(0, 1);

        return $this->findObjectByExample($example);
    }
    
    /**
     * Finds users by membership type
     * 
     * @param int $typeId
     * @param int $page
     * @param int $onPage
     */
    public function findByTypeId( $typeId, $page, $onPage )
    {
        $limit = (int) $onPage;
        $first = ( $page - 1 ) * $limit;
        
        $sql = "SELECT `m`.*
            FROM `".$this->getTableName()."` AS `m`
            LEFT JOIN `".BOL_UserDao::getInstance()->getTableName()."` AS `u` ON (`u`.`id` = `m`.`userId`)
            WHERE `m`.`typeId` = :typeId
            ORDER BY `u`.`activityStamp` DESC
            LIMIT :first, :limit";
        
        return $this->dbo->queryForList($sql, array('typeId' => $typeId, 'first' => $first, 'limit' => $limit));
    }
    
    public function countByTypeId( $typeId )
    {
        $example = new OW_Example();
        
        $example->andFieldEqual('typeId', $typeId);
        
        return $this->countByExample($example);
    }

    /**
     * Find users' expired memberships
     * 
     * @return boolean
     */
    public function findExpiredMemberships()
    {
        $sql = "SELECT * FROM `".$this->getTableName()."`
            WHERE `recurring` = 1 AND `expirationStamp` <= ?
            OR `recurring` = 0 AND `expirationStamp` <= ?";
        
        $now = time();
        
        return $this->dbo->queryForObjectList($sql, $this->getDtoClassName(), array($now - self::MEMBERSHIP_EXPIRATION_INTERVAL, $now));
    }
    
    public function deleteByTypeId( $typeId )
    {
        $example = new OW_Example();
        
        $example->andFieldEqual('typeId', $typeId);
        
        $this->deleteByExample($example);
    }
}
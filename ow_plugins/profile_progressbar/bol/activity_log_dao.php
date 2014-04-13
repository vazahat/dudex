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
 * @package ow_plugins.profileprogressbar.bol
 * @since 1.0
 */
class PROFILEPROGRESSBAR_BOL_ActivityLogDao extends OW_BaseDao
{
    CONST USER_ID     = 'userId';
    CONST ENTITY_TYPE = 'entityType';
    CONST TIME_STAMP  = 'timeStamp';
    CONST ENTITY_ID   = 'entityId';
    
    private static $classInstance;

    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    public function getDtoClassName() 
    {
        return 'PROFILEPROGRESSBAR_BOL_ActivityLog';
    }
    
    public function getTableName() 
    {
        return OW_DB_PREFIX . 'profileprogressbar_activity_log';
    }
    
    public function getCompletedFeaturesCount( $userId, array $features )
    {
        if ( empty($userId) || count($features) === 0 )
        {
            return NULL;
        }
        
        $perDay = OW::getConfig()->getValue('profileprogressbar', 'per_day');
        
        $example = new OW_Example();
        $example->andFieldEqual(self::USER_ID, $userId);
        $example->andFieldInArray(self::ENTITY_TYPE, $features);
        $example->andFieldBetween(self::TIME_STAMP, strtotime("-$perDay day"), time());
        
        return $this->countByExample($example);
    }
    
    public function getCompletedFeatures( $userId, array $features )
    {
        if ( empty($userId) || count($features) === 0 )
        {
            return array();
        }
        
        $perDay = OW::getConfig()->getValue('profileprogressbar', 'per_day');
        
        $sql = 'SELECT `'.self::ENTITY_TYPE.'`, COUNT(`' . self::ENTITY_TYPE . '`) AS `count`
            FROM `' . $this->getTableName() . '`
            WHERE `' . self::USER_ID . '` = :userId AND
                `' . self::ENTITY_TYPE . '` IN ("'. implode('","', $features) . '") AND
                `' . self::TIME_STAMP . '` BETWEEN :begin AND :end 
            GROUP BY `' . self::ENTITY_TYPE . '`';
        
        $resource = $this->dbo->queryForList($sql, 
            array(
                'userId' => $userId, 
                'begin' => strtolower("-$perDay day"), 
                'end' => time()
            )
        );
        
        $result = array();
        
        foreach ( $resource as $val )
        {
            $result[$val['entityType']] = $val['count'];
        }
        
        return $result;
    }
    
    public function deleteCompletedFeatures()
    {
        $perDay = OW::getConfig()->getValue('profileprogressbar', 'per_day');
        
        $example = new OW_Example();
        $example->andFieldLessOrEqual(self::TIME_STAMP, strtotime("-$perDay day"));
        
        return $this->deleteByExample($example);
    }
    
    public function findCompletedLog( $entityType, $entityId )
    {
        if ( empty($entityType) || empty($entityId) )
        {
            return NULL;
        }
        
        $sql = 'SELECT *
            FROM `' . $this->getTableName() . '`
            WHERE `' . self::ENTITY_TYPE . '` = :entityType AND
                `' . self::ENTITY_ID . '` = :entityId
            LIMIT 1';
        
        return $this->dbo->queryForObject($sql, $this->getDtoClassName(),
            array(
                'entityType' => $entityType,
                'entityId' => $entityId
            )
        );
    }
    
    public function deleteCompletedFriendLog( $userId )
    {
        if ( empty($userId) )
        {
            return NULL;
        }
        
        $sql = 'DELETE
            FROM `' . $this->getTableName() . '`
            WHERE `' . self::ENTITY_TYPE . '` = :entityType AND
                `' . self::USER_ID . '` = :userId
            LIMIT 1';
        
        return $this->dbo->query($sql,
            array(
                'entityType' => 'friend_add',
                'userId' => $userId
            )
        );
    }
    
    public function deleteCompletedEventLog( $userId, $entityId )
    {
        if ( empty($userId) || empty($entityId) )
        {
            return NULL;
        }
        
        $sql = 'DELETE
            FROM `' . $this->getTableName() . '`
            WHERE `' . self::ENTITY_TYPE . '` = :entityType AND
                `' . self::USER_ID . '` = :userId AND
                `' . self::ENTITY_ID . '` = :entityId
            LIMIT 1';
        
        return $this->dbo->query($sql,
            array(
                'entityType' => 'event',
                'userId' => $userId,
                'entityId' => $entityId
            )
        );
    }
    
    public function deleteCompletedLogByEntityId( $entityId )
    {
        if ( empty($entityId) )
        {
            return NULL;
        }
        
        $sql = 'DELETE
            FROM `' . $this->getTableName() . '`
            WHERE `' . self::ENTITY_TYPE . '` = :entityType AND
                `' . self::ENTITY_ID . '` = :entityId
            LIMIT 1';
        
        return $this->dbo->query($sql,
            array(
                'entityType' => 'group',
                'entityId' => $entityId
            )
        );
    }
}

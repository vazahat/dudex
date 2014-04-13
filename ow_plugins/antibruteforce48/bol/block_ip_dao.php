<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Kairat Bakytow
 * @package ow_plugins.antibruteforce.bol
 * @since 1.0
 */
class ANTIBRUTEFORCE_BOL_BlockIpDao extends OW_BaseDao
{
    CONST IP = 'ip';
    
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
        return 'ANTIBRUTEFORCE_BOL_BlockIp';
    }
    
    public function getTableName()
    {
        return OW_DB_PREFIX . 'antibruteforce_block_ip';
    }
    
    public function isLocked()
    {
        $query = 'SELECT *
            FROM `' . $this->getTableName() .'`
            WHERE `' . self::IP . '` = INET_ATON(:ip)';
        
        $blockIp = $this->dbo->queryForObject( $query, $this->getDtoClassName(), array('ip' => $_SERVER['REMOTE_ADDR']) );
        
        return ( !empty($blockIp) && $blockIp instanceof ANTIBRUTEFORCE_BOL_BlockIp ) ? TRUE : FALSE;
    }
    
    public function addBlockIp()
    {
        $newBlockIp = new ANTIBRUTEFORCE_BOL_BlockIp();
        $newBlockIp->setIp( $_SERVER['REMOTE_ADDR'] );
        $newBlockIp->setTime( time() );
        
        $this->save( $newBlockIp );
    }
    
    public function deleteBlockIp()
    {
        $expareTime = (int)OW::getConfig()->getValue( 'antibruteforce', ANTIBRUTEFORCE_BOL_Service::EXPIRE_TIME ) * 60;
        
        $query = 'DELETE FROM `' . $this->getTableName() . '`
            WHERE `time` <= :time';
        
        $this->dbo->query( $query, array('time' => time() - $expareTime) );
    }
}

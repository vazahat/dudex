<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `usercredits_action` table.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.user_credits.bol
 * @since 1.0
 */
class USERCREDITS_BOL_ActionDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var USERCREDITS_BOL_ActionDao
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
     * @return USERCREDITS_BOL_ActionDao
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
        return 'USERCREDITS_BOL_Action';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'usercredits_action';
    }

    /**
     * Finds action by plugin key and action name
     * 
     * @param string $pluginKey
     * @param string $actionKey
     */
    public function findAction( $pluginKey, $actionKey )
    {
    	$example = new OW_Example();
    	$example->andFieldEqual('pluginKey', $pluginKey);
    	$example->andFieldEqual('actionKey', $actionKey);
    	
    	return $this->findObjectByExample($example);
    }
    
    /**
     * Finds action list by type
     * 
     * @param string $type
     */
    public function findList( $type )
    {
        $example = new OW_Example();
        
        if ( $type == 'earn' )
        {
            $example->andFieldGreaterThan('amount', 0);
        }
        else if ( $type == 'lose' )
        {
            $example->andFieldLessThan('amount', 0);
        }
        else if ( $type == 'unset' )
        {
            $example->andFieldEqual('amount', 0);
        }

        $example->andFieldEqual('isHidden', 0);
        $example->andFieldEqual('active', 1);
        
        return $this->findListByExample($example);
    }
    
    /**
     * Finds actions by plugin key
     * 
     * @param string $pluginKey
     */
    public function findActionsByPluginKey( $pluginKey )
    {
        $example = new OW_Example();
        $example->andFieldEqual('pluginKey', $pluginKey);
        
        return $this->findListByExample($example);
    }
    
    public function findActionList( $keyList )
    {
        $sql = 'SELECT * FROM `'.$this->getTableName().'` WHERE ';
        
        foreach ( $keyList as $pluginKey => $actionKeys )
        {
            foreach ( $actionKeys as $actionKey )
            {
                $sql .= "`pluginKey`='".$pluginKey."' AND `actionKey`='".$actionKey."' OR ";
            }
        }
        
        $sql = substr($sql, 0, strlen($sql)-3);
        
        return $this->dbo->queryForObjectList($sql, 'USERCREDITS_BOL_Action');
    }
}
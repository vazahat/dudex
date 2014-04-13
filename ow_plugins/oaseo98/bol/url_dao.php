<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo.bol
 */
class OASEO_BOL_UrlDao extends OW_BaseDao
{
    const ROUTE_NAME = 'routeName';
    const URL = 'url';

    /**
     * Singleton instance.
     *
     * @var OASEO_BOL_UrlDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OASEO_BOL_UrlDao
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
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'OASEO_BOL_Url';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'oaseo_url';
    }

    /**
     * @param string  $name
     * @return OASEO_BOL_Url
     */
    public function findByRouteName( $name )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::ROUTE_NAME, $name);

        return $this->findObjectByExample($example);
    }
}

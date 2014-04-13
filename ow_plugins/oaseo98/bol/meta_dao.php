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
class OASEO_BOL_MetaDao extends OW_BaseDao
{
    const KEY = 'key';
    const META = 'meta';
    const URI = 'uri';

    /**
     * Singleton instance.
     *
     * @var OASEO_BOL_MetaDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OASEO_BOL_MetaDao
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
        return 'OASEO_BOL_Meta';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'oaseo_meta';
    }

    /**
     * @param string $key
     * @return OASEO_BOL_Meta
     */
    public function findEntryByKey( $key )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::KEY, trim($key));

        return $this->findObjectByExample($example);
    }
}

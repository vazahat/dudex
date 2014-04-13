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
class OASEO_BOL_SitemapPageItemDao extends OW_BaseDao
{
    const PAGE_ID = 'pageId';
    const ITEM_ID = 'itemId';
    const TYPE = 'type';
    
    const TYPE_VALUE_PAGE = 1;
    const TYPE_VALUE_ITEM = 2;

    /**
     * Singleton instance.
     *
     * @var OASEO_BOL_SitemapPageItemDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OASEO_BOL_SitemapPageItemDao
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
        return 'OASEO_BOL_SitemapPageItem';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'oaseo_sitemap_page_item';
    }

    public function clearTable()
    {
        $this->dbo->query("TRUNCATE TABLE `" . $this->getTableName() . "`");
    }
    
}

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
class OASEO_BOL_SitemapPageDao extends OW_BaseDao
{
    const URL = 'url';
    const META = 'meta';
    const TITLE = 'title';
    const STATUS = 'status';
    const PROCESS_TS = 'processTs';
    const BROKEN = 'broken';

    /**
     * Singleton instance.
     *
     * @var OASEO_BOL_SitemapPageDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OASEO_BOL_SitemapPageDao
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
        return 'OASEO_BOL_SitemapPage';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'oaseo_sitemap_page';
    }

    public function clearTable()
    {
        $this->dbo->query("TRUNCATE TABLE `" . $this->getTableName() . "`");
    }

    /**
     * @param string $url
     * @return OASEO_BOL_SitemapPage
     */
    public function findByUrl( $url, $status = null )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::URL, trim($url));

        if ( $status != null )
        {
            $example->andFieldEqual(self::STATUS, $status);
        }

        return $this->findObjectByExample($example);
    }

    /**
     * @param array $urlList
     * @return array
     */
    public function findByUrlList( array $urlList )
    {
        if ( empty($urlList) )
        {
            return array();
        }

        $urlList = array_unique($urlList);

        $example = new OW_Example();
        $example->andFieldInArray(self::URL, $urlList);

        return $this->findListByExample($example);
    }

    /**
     * @param int $count
     * @return array
     */
    public function getNextUrlList( $count )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::STATUS, 0);
        $example->setLimitClause(0, $count);

        return $this->findListByExample($example);
    }

    /**
     * @return type
     */
    public function findAllProcessedUrls( $start, $count )
    {
        $sql = "SELECT `".self::URL."`, `".self::PROCESS_TS."` FROM `" . $this->getTableName() . "` 
            WHERE `" . self::STATUS . "` = 1 AND `" . self::BROKEN . "` = 0 AND `" . self::TITLE . "` IS NOT NULL AND `" . self::META . "` IS NOT NULL 
            ORDER BY `id` LIMIT ?, ?";

        return $this->dbo->queryForList($sql, array($start, $count));
    }

    /**
     * @param int $first
     * @param int $count
     * @return array
     */
    public function findPages( $first, $count )
    {
        $query = "SELECT * from `" . $this->getTableName() . "` WHERE `" . self::BROKEN . "` = 0 LIMIT ?, ?";
        return $this->dbo->queryForList($query, array($first, $count));
    }

    /**
     * @return int
     */
    public function findPagesCount( $broken = null )
    {
        $example = new OW_Example();
        
        if( $broken != null )
        {
            $example->andFieldEqual(self::BROKEN, (int) $broken);
        }

        return $this->countByExample($example);
    }

    public function findBrokenPages( $first, $count )
    {
        $query = "SELECT `id` from `" . $this->getTableName() . "` WHERE `" . self::BROKEN . "` = 1 LIMIT ?, ?";
        $result = $this->dbo->queryForColumnList($query, array($first, $count));

        if ( !$result )
        {
            return array();
        }

        $query = "SELECT p.url as burl, p2.url FROM `" . $this->getTableName() . "` AS `p` 
            LEFT JOIN `" . OASEO_BOL_SitemapPageItemDao::getInstance()->getTableName() . "` AS `pi` ON (p.id = pi.itemId AND pi.type = 1)
            LEFT JOIN `" . $this->getTableName() . "` AS `p2` ON (`pi`.`pageId` = p2.id)
            WHERE  p.id IN (" . $this->dbo->mergeInClause($result) . ")";

        return $this->dbo->queryForList($query);
    }

    /**
     * @param bool $status
     * @return int
     */
    public function findProcessedCount( $status = false )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::STATUS, (int) $status);

        return $this->countByExample($example);
    }
}

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
class OASEO_BOL_SlugDao extends OW_BaseDao
{
    const ENTITY_TYPE = 'entityType';
    const ENTITY_ID = 'entityId';
    const STRING = 'string';
    const ACTIVE = 'active';

    /**
     * Singleton instance.
     *
     * @var OASEO_BOL_SlugDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OASEO_BOL_SlugDao
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
        return 'OASEO_BOL_Slug';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'oaseo_slug';
    }

    /**
     * @param string $entityType
     * @param integer $entityId
     */
    public function updateSlugStatus( $entityType, $entityId )
    {
        $query = "UPDATE `" . $this->getTableName() . "` SET `" . self::ACTIVE . "` = 0 WHERE `" . self::ENTITY_TYPE . "` = :et AND `" . self::ENTITY_ID . "` = :ei";
        $this->dbo->query($query, array('et' => $entityType, 'ei' => $entityId));
    }

    /**
     * @param string $entityType
     * @param integer $entityId
     * @param string $slug
     * @return OASEO_BOL_Slug
     */
    public function findSlug( $entityType, $entityId, $slug )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::ENTITY_TYPE, $entityType);
        $example->andFieldEqual(self::ENTITY_ID, $entityId);
        $example->andFieldEqual(self::STRING, $slug);

        return $this->findObjectByExample($example);
    }

    /**
     * @param string $entityType
     * @param string $string
     * @return OASEO_BOL_Slug
     */
    public function findOldSlug( $entityType, $string )
    {
        $query = "SELECT * FROM `" . $this->getTableName() . "` WHERE `" . self::ENTITY_TYPE . "` = :et AND `" . self::STRING . "` = :st AND `" . self::ACTIVE . "` = 0";
        return $this->dbo->queryForObject($query, $this->getDtoClassName(), array('et' => $entityType, 'st' => $string));
    }

    public function findDuplicateSlug( $entityType, $string )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::ENTITY_TYPE, $entityType);
        $example->andFieldEqual(self::STRING, $string);

        return $this->findObjectByExample($example);
    }

    /**
     * @param string $entityType
     * @param integer $entityId
     * @return OASEO_BOL_Slug
     */
    public function findActiveSlugForEntityItem( $entityType, $entityId )
    {
        $query = "SELECT * FROM `" . $this->getTableName() . "` WHERE `" . self::ENTITY_TYPE . "` = :et AND `" . self::ENTITY_ID . "` = :ei AND `" . self::ACTIVE . "` = 1";
        return $this->dbo->queryForObject($query, $this->getDtoClassName(), array('et' => $entityType, 'ei' => $entityId));
    }

    /**
     * @param string $entityType
     */
    public function deleteByEntityType( $entityType )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::ENTITY_TYPE, $entityType);

        $this->deleteByExample($example);
    }

    /**
     * @param array $entityTypes
     * @return array
     */
    public function findWorkingSlugs( array $entityTypes )
    {
        if ( empty($entityTypes) )
        {
            return array();
        }

        $query = "SELECT * FROM `" . $this->getTableName() . "` WHERE `" . self::ENTITY_TYPE . "` IN ( " . $this->dbo->mergeInClause($entityTypes) . " ) AND `" . self::ACTIVE . "` = 1";
        return $this->dbo->queryForObjectList($query, $this->getDtoClassName());
    }
}

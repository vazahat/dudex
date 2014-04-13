<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.bol
 */
class UTAGS_BOL_TagDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var UTAGS_BOL_TagDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return UTAGS_BOL_TagDao
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
        return 'UTAGS_BOL_Tag';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'utags_tag';
    }
    
    public function findByCopyPhotoId( $photoId )
    {
        $example = new OW_Example();
        $example->andFieldEqual("copyPhotoId", $photoId);
        
        return $this->findListByExample($example);
    }
    
    public function findByPhotoId( $photoId )
    {
        $example = new OW_Example();
        $example->andFieldEqual("photoId", $photoId);
        
        return $this->findListByExample($example);
    }
    
    public function findByCopyPhotoIdAndEntity( $photoId, $entityType, $entityId )
    {
        $example = new OW_Example();
        $example->andFieldEqual("copyPhotoId", $photoId);
        $example->andFieldEqual("entityType", $entityType);
        $example->andFieldEqual("entityId", $entityId);
        
        return $this->findListByExample($example);
    }
    
    public function findByPhotoIdAndEntity( $photoId, $entityType, $entityId )
    {
        $example = new OW_Example();
        $example->andFieldEqual("photoId", $photoId);
        $example->andFieldEqual("entityType", $entityType);
        $example->andFieldEqual("entityId", $entityId);
        
        return $this->findListByExample($example);
    }
    
    public function findByPhotoIdOrCopyPhotoId( $photoId, $type = null )
    {
        $addWhere = "1";
        if ( $type !== null )
        {
            $addWhere = "entityType='" . $type . "'";
        }
        
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE ( photoId=:pid OR copyPhotoId=:pid ) AND " . $addWhere;
        
        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            "pid" => $photoId
        ));
    }
    
    public function findByEntity( $entityType, $entityId )
    {
        $example = new OW_Example();
        $example->andFieldEqual("entityType", $entityType);
        $example->andFieldEqual("entityId", $entityId);
        
        return $this->findListByExample($example);
    }
}
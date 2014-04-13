<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class EVENTX_BOL_EventCategoryDao extends OW_BaseDao {

    protected function __construct() {
        parent::__construct();
    }

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function getDtoClassName() {
        return 'EVENTX_BOL_EventCategory';
    }

    public function getTableName() {
        return OW_DB_PREFIX . 'eventx_event_category';
    }

    public function getAllItemCategories($page, $limit) {
        $first = ($page - 1 ) * $limit;
        $categoryDao = EVENTX_BOL_CategoryDao::getInstance();
        $fileDao = EVENTX_BOL_EventDao::getInstance();

        $query = "
                    SELECT a.categoryId, b.name, b.description, count( * ) count
                    FROM " . $this->getTableName() . " AS a," . $categoryDao->getTableName() . " AS b," .
                $fileDao->getTableName() . " AS c " .
                "WHERE a.categoryId = b.id
                       AND c.id = a.eventId
                       AND c.status = 'approved'                       
                    GROUP BY a.categoryId
                    ORDER BY count DESC
                    LIMIT ?, ?
                ";

        return $this->dbo->queryForList($query, array($first, $limit));
    }

    public function setItemCategories($eventId, $categoryIds) {
        $ex = new OW_Example();
        $ex->andFieldEqual('eventId', (int) $eventId);
        $this->deleteByExample($ex);

        if (is_array($categoryIds) && count($categoryIds) > 0) {
            foreach ($categoryIds as $categoryId) {
                $category = new EVENTX_BOL_EventCategory();
                $category->eventId = $eventId;
                $category->categoryId = $categoryId;
                $this->save($category);
            }
        } else {
            $category = new EVENTX_BOL_EventCategory();
            $category->eventId = $eventId;
            $category->categoryId = $categoryIds;
            $this->save($category);
        }
        return $eventId;
    }

    public function getItemCategories($eventId) {
        $example = new OW_Example();
        $example->andFieldEqual('eventId', $eventId);

        $categories = $this->findListByExample($example);

        if (count($categories) > 0)
            return $categories;
        else
            return false;
    }

    public function getItemCategoryId($eventId) {
        $example = new OW_Example();
        $example->andFieldEqual('eventId', $eventId);

        $categories = $this->findListByExample($example);

        if (count($categories) > 0)
            return $categories;
        else
            return false;
    }

    public function reassignCategory($oldCategory, $newCategory) {
        $sql = "UPDATE `" . $this->getTableName() . "` SET `categoryId` = :newCategory WHERE `categoryId` = :oldCategory";

        $this->dbo->query($sql, array('oldCategory' => $oldCategory, 'newCategory' => $newCategory));
    }

}

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
class IVIDEO_BOL_VideoDao extends OW_BaseDao {

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
        return 'IVIDEO_BOL_Video';
    }

    public function getTableName() {
        return OW_DB_PREFIX . 'ivideo_videos';
    }

    public function findCategoryVideosList($category, $page, $limit) {
        $catId = IVIDEO_BOL_CategoryService::getInstance()->getCategoryId($category);

        $categoryDao = IVIDEO_BOL_VideoCategoryDao::getInstance();

        $first = ($page - 1 ) * $limit;

        $query = "SELECT a.* FROM " . $this->getTableName() . " a," . $categoryDao->getTableName() . " b
                  WHERE a.status = 'approved'
                    AND a.id = b.videoID
                    AND b.categoryId = :catId
                    AND a.privacy = 'everybody' 
                  ORDER BY a.timestamp DESC
                  LIMIT :first, :limit";

        $qParams = array('catId' => $catId, 'first' => $first, 'limit' => $limit);

        return $this->dbo->queryForObjectList($query, 'IVIDEO_BOL_Video', $qParams);
    }

    public function findCategoryVideosCount($category) {
        $catId = IVIDEO_BOL_CategoryService::getInstance()->getCategoryId($category);

        $categoryDao = IVIDEO_BOL_VideoCategoryDao::getInstance();

        $query = "SELECT COUNT(*) FROM " . $this->getTableName() . " a," . $categoryDao->getTableName() . " b
                  WHERE a.status = 'approved'
                    AND a.id = b.videoID
                    AND a.privacy = 'everybody' 
                    AND b.categoryId = " . $catId;

        return $this->dbo->queryForColumn($query);
    }

    public function getVideosList($listtype, $page, $limit) {
        $first = ($page - 1 ) * $limit;

        switch ($listtype) {
            case 'featured':
                $clipFeaturedDao = IVIDEO_BOL_VideoFeaturedDao::getInstance();

                $query = "
                    SELECT `c`.*
                    FROM `" . $this->getTableName() . "` AS `c`
                    LEFT JOIN `" . $clipFeaturedDao->getTableName() . "` AS `f` ON (`f`.`videoId`=`c`.`id`)
                    WHERE `c`.`status` = 'approved' AND `c`.`privacy` = 'everybody' AND `f`.`id` IS NOT NULL
                    ORDER BY `c`.`timestamp` DESC
                    LIMIT :first, :limit
                ";

                $qParams = array('first' => $first, 'limit' => $limit);

                return $this->dbo->queryForObjectList($query, 'IVIDEO_BOL_Video', $qParams);

                break;

            case 'latest':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'approved');
                $example->andFieldEqual('privacy', 'everybody');
                $example->setOrder('timestamp DESC');
                $example->setLimitClause($first, $limit);

                return $this->findListByExample($example);

                break;

            case 'pending':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'pending');
                $example->setOrder('timestamp DESC');
                $example->setLimitClause($first, $limit);

                return $this->findListByExample($example);

                break;
        }
    }

    public function getUserVideosList($userId, $page, $itemsNum, $exclude) {
        $first = ($page - 1 ) * $itemsNum;

        $example = new OW_Example();

        $example->andFieldEqual('status', 'approved');
        $example->andFieldEqual('owner', $userId);

        if ($exclude) {
            $example->andFieldNotEqual('id', $exclude);
        }

        $example->setOrder('`timestamp` DESC');
        $example->setLimitClause($first, $itemsNum);

        return $this->findListByExample($example);
    }

    public function countVideos($listtype) {
        switch ($listtype) {
            case 'featured':
                $featuredDao = IVIDEO_BOL_VideoFeaturedDao::getInstance();

                $query = "
                    SELECT COUNT(`c`.`id`)       
                    FROM `" . $this->getTableName() . "` AS `c`
                    LEFT JOIN `" . $featuredDao->getTableName() . "` AS `f` ON ( `c`.`id` = `f`.`videoId` )
                    WHERE `c`.`status` = 'approved' AND `c`.`privacy` = 'everybody' AND `f`.`id` IS NOT NULL
                ";

                return $this->dbo->queryForColumn($query);

                break;

            case 'latest':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'approved');
                $example->andFieldEqual('privacy', 'everybody');

                return $this->countByExample($example);

                break;

            case 'pending':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'pending');

                return $this->countByExample($example);

                break;
        }
    }

    public function countUserVideos($userId) {
        $example = new OW_Example();

        $example->andFieldEqual('owner', $userId);
        $example->andFieldEqual('status', 'approved');

        return $this->countByExample($example);
    }

    public function findByUserId($userId) {
        $example = new OW_Example();

        $example->andFieldEqual('owner', $userId);
        $example->andFieldEqual('status', 'approved');

        return $this->findIdListByExample($example);
    }

    public function updatePrivacyByUserId($userId, $privacy) {
        $sql = "UPDATE `" . $this->getTableName() . "` SET `privacy` = :privacy 
            WHERE `userId` = :userId";

        $this->dbo->query($sql, array('privacy' => $privacy, 'userId' => $userId));
    }

}
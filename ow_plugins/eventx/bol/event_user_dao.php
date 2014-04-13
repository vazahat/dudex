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
class EVENTX_BOL_EventUserDao extends OW_BaseDao {

    const EVENTX_ID = 'eventId';
    const USER_ID = 'userId';
    const TIME_STAMP = 'timeStamp';
    const STATUS = 'status';
    const VALUE_STATUS_YES = 1;
    const VALUE_STATUS_MAYBE = 2;
    const VALUE_STATUS_NO = 3;
    const CACHE_TAG_EVENTX_USER_LIST = 'event_users_list_event_id_';
    const CACHE_LIFE_TIME = 86400; //24 hour

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    protected function __construct() {
        parent::__construct();
    }

    public function getDtoClassName() {
        return 'EVENTX_BOL_EventUser';
    }

    public function getTableName() {
        return OW_DB_PREFIX . 'eventx_user';
    }

    public function deleteByEventId($id) {
        $example = new OW_Example();
        $example->andFieldEqual(self::EVENTX_ID, (int) $id);

        $this->deleteByExample($example);
    }

    public function findListByEventIdAndStatus($eventId, $status, $first, $count) {
        $query = " SELECT e.* FROM  " . $this->getTableName() . " e
            		LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` as `s` ON( `e`.`userId` = `s`.`userId` )
                    LEFT JOIN `" . BOL_UserApproveDao::getInstance()->getTableName() . "` as `d` ON( `e`.`userId` = `d`.`userId` )
                    WHERE s.Id IS NULL AND d.id IS NULL AND e.`" . self::EVENTX_ID . "` = :eventId AND e.`" . self::STATUS . "` = :status
                    LIMIT :first, :count ";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array('eventId' => (int) $eventId, 'status' => (int) $status, 'first' => (int) $first, 'count' => (int) $count));
    }

    public function findCountByEventIdAndStatus($eventId, $status) {
        $query = " SELECT count(e.id) FROM  " . $this->getTableName() . " e
            		LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` as `s` ON( `e`.`userId` = `s`.`userId` )
                    LEFT JOIN `" . BOL_UserApproveDao::getInstance()->getTableName() . "` as `d` ON( `e`.`userId` = `d`.`userId` )
                    WHERE s.Id IS NULL AND d.id IS NULL AND e.`" . self::EVENTX_ID . "` = :eventId AND e.`" . self::STATUS . "` = :status ";

        return $this->dbo->queryForColumn($query, array('eventId' => (int) $eventId, 'status' => (int) $status), self::CACHE_LIFE_TIME, array(self::CACHE_TAG_EVENTX_USER_LIST . $eventId));
    }

    public function findObjectByEventIdAndUserId($eventId, $userId) {
        $example = new OW_Example();
        $example->andFieldEqual(self::EVENTX_ID, (int) $eventId);
        $example->andFieldEqual(self::USER_ID, (int) $userId);

        return $this->findObjectByExample($example);
    }

    public function findByUserId($userId, $first, $count) {
        $example = new OW_Example();
        $example->andFieldEqual(self::USER_ID, (int) $userId);
        $example->setLimitClause($first, $count);

        return $this->findListByExample($example);
    }

}

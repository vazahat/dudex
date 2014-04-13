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
class EVENTX_BOL_EventInviteDao extends OW_BaseDao {

    const USER_ID = 'userId';
    const INVITER_ID = 'inviterId';
    const TIME_STAMP = 'timeStamp';
    const EVENTX_ID = 'eventId';

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
        return 'EVENTX_BOL_EventInvite';
    }

    public function getTableName() {
        return OW_DB_PREFIX . 'eventx_invite';
    }

    public function findObjectByUserIdAndEventId($eventId, $userId) {
        $example = new OW_Example();
        $example->andFieldEqual(self::EVENTX_ID, (int) $eventId);
        $example->andFieldEqual(self::USER_ID, (int) $userId);

        return $this->findObjectByExample($example);
    }

    public function hideInvitationByUserId($userId) {
        $query = "UPDATE `" . EVENTX_BOL_EventInviteDao::getInstance()->getTableName() . "` SET `displayInvitation` = false 
            WHERE `" . EVENTX_BOL_EventInviteDao::USER_ID . "` = :userId AND `displayInvitation` = true ";

        return $this->dbo->update($query, array('userId' => (int) $userId));
    }

    public function deleteByEventId($eventId) {
        $example = new OW_Example();
        $example->andFieldEqual(self::EVENTX_ID, (int) $eventId);

        $this->deleteByExample($example);
    }

    public function deleteByUserIdAndEventId($eventId, $userId) {
        $example = new OW_Example();
        $example->andFieldEqual(self::EVENTX_ID, (int) $eventId);
        $example->andFieldEqual(self::USER_ID, (int) $userId);

        $this->deleteByExample($example);
    }

    public function findInviteListByEventId($eventId) {
        $example = new OW_Example();
        $example->andFieldEqual(self::EVENTX_ID, (int) $eventId);

        return $this->findListByExample($example);
    }

    public function findUserListForInvite($eventId, $first, $count, $friendList = null) {

        $userDao = BOL_UserDao::getInstance();
        $eventDao = EVENTX_BOL_EventDao::getInstance();
        $eventUserDao = EVENTX_BOL_EventUserDao::getInstance();

        $where = "";
        if (isset($friendList) && empty($friendList)) {
            return array();
        } else if (!empty($friendList)) {
            $where = " AND `u`.id IN ( " . $this->dbo->mergeInClause($friendList) . " ) ";
        }

        $query = "SELECT `u`.`id`
    		FROM `{$userDao->getTableName()}` as `u`
            LEFT JOIN `" . $eventDao->getTableName() . "` as `e`
    			ON( `u`.`id` = `e`.`userId` AND e.id = :event )
            LEFT JOIN `" . $this->getTableName() . "` as `ei`
    			ON( `u`.`id` = `ei`.`userId` AND `ei`.eventId = :event )

            LEFT JOIN `" . $eventUserDao->getTableName() . "` as `eu`
    			ON( `u`.`id` = `eu`.`userId` AND `eu`.eventId = :event )

    		LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` as `s`
    			ON( `u`.`id` = `s`.`userId` )

    		LEFT JOIN `" . BOL_UserApproveDao::getInstance()->getTableName() . "` as `d`
    			ON( `u`.`id` = `d`.`userId` )

    		WHERE `e`.`id` IS NULL AND `ei`.`id` IS NULL AND `s`.`id` IS NULL AND `d`.`id` IS NULL AND `eu`.`id` IS NULL " . $where . "
    		ORDER BY `u`.`activityStamp` DESC
    		LIMIT :first, :count ";

        return $this->dbo->queryForColumnList($query, array('event' => $eventId, 'first' => $first, 'count' => $count));
    }

}

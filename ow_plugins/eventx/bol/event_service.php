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
final class EVENTX_BOL_EventService {

    const USER_STATUS_YES = EVENTX_BOL_EventUserDao::VALUE_STATUS_YES;
    const USER_STATUS_MAYBE = EVENTX_BOL_EventUserDao::VALUE_STATUS_MAYBE;
    const USER_STATUS_NO = EVENTX_BOL_EventUserDao::VALUE_STATUS_NO;
    const CAN_INVITE_PARTICIPANT = EVENTX_BOL_EventDao::VALUE_WHO_CAN_INVITE_PARTICIPANT;
    const CAN_INVITE_CREATOR = EVENTX_BOL_EventDao::VALUE_WHO_CAN_INVITE_CREATOR;
    const CAN_VIEW_ANYBODY = EVENTX_BOL_EventDao::VALUE_WHO_CAN_VIEW_ANYBODY;
    const CAN_VIEW_INVITATION_ONLY = EVENTX_BOL_EventDao::VALUE_WHO_CAN_VIEW_INVITATION_ONLY;
    const CONF_EVENTX_USERS_COUNT = 'event_users_count';
    const CONF_EVENTX_USERS_COUNT_ON_PAGE = 'event_users_count_on_page';
    const CONF_EVENTS_COUNT_ON_PAGE = 'events_count_on_page';
    const CONF_WIDGET_EVENTS_COUNT = 'events_widget_count';
    const CONF_WIDGET_EVENTS_COUNT_OPTION_LIST = 'events_widget_count_select_set';
    const CONF_DASH_WIDGET_EVENTS_COUNT = 'events_dash_widget_count';
    const EVENTX_AFTER_EVENTX_EDIT = 'event_after_event_edit';
    const EVENTX_ON_DELETE_EVENT = 'event_on_delete_event';
    const EVENTX_ON_CREATE_EVENT = 'event_on_create_event';
    const EVENTX_ON_CHANGE_USER_STATUS = 'event_on_change_user_status';
    const EVENTX_AFTER_CREATE_EVENT = 'event_after_create_event';
    const EVENTX_BEFORE_EVENTX_CREATE = 'events.before_event_create';
    const EVENTX_BEFORE_EVENTX_EDIT = 'events.before_event_edit';

    private $configs = array();
    private $eventDao;
    private $eventUserDao;
    private $eventInviteDao;
    private $categoryDao;
    private $eventCategoryDao;
    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() {
        $this->eventDao = EVENTX_BOL_EventDao::getInstance();
        $this->eventUserDao = EVENTX_BOL_EventUserDao::getInstance();
        $this->eventInviteDao = EVENTX_BOL_EventInviteDao::getInstance();
        $this->categoryDao = EVENTX_BOL_CategoryDao::getInstance();
        $this->eventCategoryDao = EVENTX_BOL_EventCategoryDao::getInstance();

        $this->configs[self::CONF_EVENTX_USERS_COUNT] = 10;
        $this->configs[self::CONF_EVENTS_COUNT_ON_PAGE] = 15;
        $this->configs[self::CONF_DASH_WIDGET_EVENTS_COUNT] = 3;
        $this->configs[self::CONF_WIDGET_EVENTS_COUNT] = 3;
        $this->configs[self::CONF_EVENTX_USERS_COUNT_ON_PAGE] = OW::getConfig()->getValue('eventx', 'resultsPerPage');
        $this->configs[self::CONF_WIDGET_EVENTS_COUNT_OPTION_LIST] = array(3 => 3, 5 => 5, 10 => 10, 15 => 15, 20 => 20);
    }

    public function getConfigs() {
        return $this->configs;
    }

    public function saveEvent(EVENTX_BOL_Event $event) {
        $this->eventDao->save($event);
    }

    public function saveEventImage($imagePath, $imageId) {
        $storage = OW::getStorage();

        if ($storage->fileExists($this->generateImagePath($imageId))) {
            $storage->removeFile($this->generateImagePath($imageId));
            $storage->removeFile($this->generateImagePath($imageId, false));
        }

        $pluginfilesDir = Ow::getPluginManager()->getPlugin('eventx')->getPluginFilesDir();

        $tmpImgPath = $pluginfilesDir . 'img_' . uniqid() . '.jpg';
        $tmpIconPath = $pluginfilesDir . 'icon_' . uniqid() . '.jpg';

        $image = new UTIL_Image($imagePath);
        $image->resizeImage(295, null)->saveImage($tmpImgPath)
                ->resizeImage(100, 100, true)->saveImage($tmpIconPath);

        unlink($imagePath);

        $storage->copyFile($tmpIconPath, $this->generateImagePath($imageId));
        $storage->copyFile($tmpImgPath, $this->generateImagePath($imageId, false));

        unlink($tmpImgPath);
        unlink($tmpIconPath);
    }

    public function deleteEvent($eventId) {
        $eventDto = $this->eventDao->findById((int) $eventId);

        if ($eventDto === null) {
            return FALSE;
        }

        $e = new OW_Event(self::EVENTX_ON_DELETE_EVENT, array('eventId' => (int) $eventId));
        OW::getEventManager()->trigger($e);

        if (!empty($eventDto->image)) {
            $storage = OW::getStorage();
            $storage->removeFile($this->generateImagePath($eventDto->image));
            $storage->removeFile($this->generateImagePath($eventDto->image, false));
        }

        $this->eventUserDao->deleteByEventId($eventDto->getId());
        $this->eventDao->deleteById($eventDto->getId());
        $this->eventInviteDao->deleteByEventId($eventDto->getId());
        BOL_InvitationService::getInstance()->deleteInvitationByEntity('eventx', $eventId);
        BOL_InvitationService::getInstance()->deleteInvitationByEntity('event-invitation', $eventId);

        return TRUE;
    }

    public function updateEventStatus($id, $status) {
        $event = $this->eventDao->findById($id);

        $newStatus = $status == 'approved' ? 'approved' : 'pending';

        $event->status = $newStatus;

        $this->eventDao->save($event);

        return $event->id ? true : false;
    }

    public function generateImagePath($imageId, $icon = true) {
        $imagesDir = OW::getPluginManager()->getPlugin('eventx')->getUserFilesDir();
        return $imagesDir . ( $icon ? 'event_icon_' : 'event_image_' ) . $imageId . '.jpg';
    }

    public function generateImageUrl($imageId, $icon = true) {
        return OW::getStorage()->getFileUrl($this->generateImagePath($imageId, $icon));
    }

    public function generateDefaultImageUrl() {
        return OW::getThemeManager()->getCurrentTheme()->getStaticImagesUrl() . 'no-picture.png';
    }

    public function findEvent($id) {
        return $this->eventDao->findById((int) $id);
    }

    public function findPendingEvents($page, $count) {
        $first = ( $page - 1 ) * $count;
        return $this->eventDao->findPendingEvents($first, $count);
    }

    public function findPendingEventsCount() {
        return $this->eventDao->findPendingEventsCount();
    }

    public function findCategoryEvents($category, $page, $count) {

        $catId = $this->categoryDao->getCategoryId($category);

        $first = ($page - 1 ) * $count;

        $query = "SELECT a.* FROM " . $this->eventDao->getTableName() . " a," . $this->eventCategoryDao->getTableName() . " b
                  WHERE a.status = 'approved'             
                    AND a.id = b.eventId
                    AND b.categoryId = :catId
                  ORDER BY a.startTimeStamp DESC
                  LIMIT :first, :limit";

        $qParams = array('catId' => $catId, 'first' => $first, 'limit' => $count);

        return OW::getDbo()->queryForObjectList($query, 'EVENTX_BOL_Event', $qParams);
    }

    public function findCategoryEventsCount($category) {

        $catId = $this->categoryDao->getCategoryId($category);

        $query = "SELECT count(a.id) FROM " . $this->eventDao->getTableName() . " a," . $this->eventCategoryDao->getTableName() . " b
                  WHERE a.status = 'approved'             
                    AND a.id = b.eventId
                    AND b.categoryId = :catId";

        $qParams = array('catId' => $catId);

        return OW::getDbo()->queryForColumn($query, $qParams);
    }

    public function findEventUsers($eventId, $status, $page, $usersCount = null) {
        if ($page === null) {
            $first = 0;
            $count = (int) $usersCount;
        } else {
            $page = ( $page === null ) ? 1 : (int) $page;
            $count = $this->configs[self::CONF_EVENTX_USERS_COUNT_ON_PAGE];
            $first = ( $page - 1 ) * $count;
        }

        return $this->eventUserDao->findListByEventIdAndStatus($eventId, $status, $first, $count);
    }

    public function findEventUsersCount($eventId, $status) {
        return (int) $this->eventUserDao->findCountByEventIdAndStatus($eventId, $status);
    }

    public function saveEventUser(EVENTX_BOL_EventUser $eventUser) {
        $this->eventUserDao->save($eventUser);
    }

    public function addEventUser($userId, $eventId, $status, $timestamp = null) {
        $statusList = array(EVENTX_BOL_EventUserDao::VALUE_STATUS_YES, EVENTX_BOL_EventUserDao::VALUE_STATUS_MAYBE, EVENTX_BOL_EventUserDao::VALUE_STATUS_NO);

        if ((int) $userId <= 0 || $eventId <= 0 || !in_array($status, $statusList)) {
            return null;
        }

        $event = $this->findEvent($eventId);

        if (empty($event)) {
            return null;
        }

        if (!isset($timestamp)) {
            $timestamp = time();
        }

        $eventUser = $this->findEventUser($eventId, $userId);

        if (empty($eventUser)) {
            $eventUser = new EVENTX_BOL_EventUser();

            $eventUser->eventId = $eventId;
            $eventUser->userId = $userId;
            $eventUser->timeStamp = $timestamp;
        }

        $eventUser->status = $status;

        $this->eventUserDao->save($eventUser);

        return $eventUser;
    }

    public function findEventUser($eventId, $userId) {
        return $this->eventUserDao->findObjectByEventIdAndUserId($eventId, $userId);
    }

    public function canUserView($eventId, $userId) {
        $event = $this->eventDao->findById($eventId);

        if ($event === null) {
            return false;
        }

        $userEvent = $this->eventUserDao->findObjectByEventIdAndUserId($eventId, $userId);

        if ($event->getWhoCanView() === self::CAN_VIEW_INVITATION_ONLY && $userEvent === null) {
            return false;
        }

        return true;
    }

    public function canUserInvite($eventId, $userId) {
        $event = $this->eventDao->findById($eventId);
        /* @var $event EVENTX_BOL_Event */
        if ($event === null || ( $event->getWhoCanInvite() == self::CAN_INVITE_CREATOR && $userId != $event->getUserId() )) {
            return false;
        }

        $userEvent = $this->eventUserDao->findObjectByEventIdAndUserId($eventId, $userId);

        if ($userEvent === null || $userEvent->getStatus() != self::USER_STATUS_YES) {
            return false;
        }

        return true;
    }

    public function findPublicEvents($page, $eventsCount = null, $past = false) {
        if ($page === null) {
            $first = 0;
            $count = (int) $eventsCount;
        } else {
            $page = ( $page === null ) ? 1 : (int) $page;
            $count = $this->configs[self::CONF_EVENTS_COUNT_ON_PAGE];
            $first = ( $page - 1 ) * $count;
        }

        return $this->eventDao->findPublicEvents($first, $count, $past);
    }

    public function findPublicEventsCount($past = false) {
        return $this->eventDao->findPublicEventsCount($past);
    }

    public function inviteUser($eventId, $userId, $inviterId) {
        $event = $this->findEvent($eventId);

        if ($event === null) {
            return false;
        }

        $eventInvite = new EVENTX_BOL_EventInvite();
        $eventInvite->setEventId($eventId);
        $eventInvite->setInviterId($inviterId);
        $eventInvite->setUserId($userId);
        $eventInvite->setTimeStamp(time());
        $eventInvite->setDisplayInvitation(true);

        $this->eventInviteDao->save($eventInvite);

        return $eventInvite;
    }

    public function findEventInvite($eventId, $userId) {
        return $this->eventInviteDao->findObjectByUserIdAndEventId($eventId, $userId);
    }

    public function findUserEvents($userId, $page, $eventsCount = null) {
        if ($page === null) {
            $first = 0;
            $count = (int) $eventsCount;
        } else {
            $page = ( $page === null ) ? 1 : (int) $page;
            $count = $this->configs[self::CONF_EVENTS_COUNT_ON_PAGE];
            $first = ( $page - 1 ) * $count;
        }

        return $this->eventDao->findUserCreatedEvents($userId, $first, $count);
    }

    public function findUsersEventsCount($userId) {
        return $this->eventDao->findUserCretedEventsCount($userId);
    }

    public function findUserParticipatedEvents($userId, $page, $eventsCount = null) {
        if ($page === null) {
            $first = 0;
            $count = (int) $eventsCount;
        } else {
            $page = ( $page === null ) ? 1 : (int) $page;
            $count = $this->configs[self::CONF_EVENTS_COUNT_ON_PAGE];
            $first = ( $page - 1 ) * $count;
        }

        return $this->eventDao->findUserEventsWithStatus($userId, self::USER_STATUS_YES, $first, $count);
    }

    public function findUserParticipatedEventsCount($userId) {
        return $this->eventDao->findUserEventsCountWithStatus($userId, self::USER_STATUS_YES);
    }

    public function findUserParticipatedPublicEvents($userId, $page, $eventsCount = null) {
        if ($page === null) {
            $first = 0;
            $count = (int) $eventsCount;
        } else {
            $page = ( $page === null ) ? 1 : (int) $page;
            $count = $this->configs[self::CONF_EVENTS_COUNT_ON_PAGE];
            $first = ( $page - 1 ) * $count;
        }

        return $this->eventDao->findPublicUserEventsWithStatus($userId, self::USER_STATUS_YES, $first, $count);
    }

    public function findUserParticipatedPublicEventsCount($userId) {
        return $this->eventDao->findPublicUserEventsCountWithStatus($userId, self::USER_STATUS_YES);
    }

    public function hideInvitationByUserId($userId) {
        return $this->eventInviteDao->hideInvitationByUserId($userId);
    }

    public function getListingData(array $events) {
        $resultArray = array();

        foreach ($events as $eventItem) {
            $title = UTIL_String::truncate(strip_tags($eventItem->getTitle()), 80, "...");
            $content = UTIL_String::truncate(strip_tags($eventItem->getDescription()), 100, "...");

            $resultArray[$eventItem->getId()] = array(
                'content' => $content,
                'title' => $title,
                'eventUrl' => OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $eventItem->getId())),
                'imageSrc' => ( $eventItem->getImage() ? $this->generateImageUrl($eventItem->getImage(), true) : $this->generateDefaultImageUrl() ),
                'imageTitle' => $title
            );
        }

        return $resultArray;
    }

    public function getListingDataWithToolbar(array $events, $toolbarList = array()) {
        $resultArray = $this->getListingData($events);
        $userService = BOL_UserService::getInstance();

        $idArray = array();

        foreach ($events as $event) {
            $idArray[] = $event->getUserId();
        }

        $usernames = $userService->getDisplayNamesForList($idArray);
        $urls = $userService->getUserUrlsForList($idArray);

        foreach ($events as $eventItem) {
            $resultArray[$eventItem->getId()]['toolbar'][] = array('label' => $usernames[$eventItem->getUserId()], 'href' => $urls[$eventItem->getUserId()], 'class' => 'ow_icon_control ow_ic_user');
            $resultArray[$eventItem->getId()]['toolbar'][] = array('label' => $this->formatSimpleDate($eventItem->getStartTimeStamp(), $eventItem->getStartTimeDisable()), 'class' => 'ow_ipc_date');

            if (!empty($toolbarList[$eventItem->getId()])) {
                $resultArray[$eventItem->getId()]['toolbar'] = array_merge($resultArray[$eventItem->getId()]['toolbar'], $toolbarList[$eventItem->getId()]);
            }
        }

        return $resultArray;
    }

    public function getUserListsArray() {
        return array(
            self::USER_STATUS_YES => 'yes',
            self::USER_STATUS_MAYBE => 'maybe',
            self::USER_STATUS_NO => 'no'
        );
    }

    public function findUserInvitedEvents($userId, $page, $eventsCount = null) {
        if ($page === null) {
            $first = 0;
            $count = (int) $eventsCount;
        } else {
            $page = ( $page === null ) ? 1 : (int) $page;
            $count = $this->configs[self::CONF_EVENTS_COUNT_ON_PAGE];
            $first = ( $page - 1 ) * $count;
        }

        return $this->eventDao->findUserInvitedEvents($userId, $first, $count);
    }

    public function findUserInvitedEventsCount($userId) {
        return $this->eventDao->findUserInvitedEventsCount($userId);
    }

    public function findDispaledUserInvitationCount($userId) {
        return $this->eventDao->findDispaledUserInvitationCount($userId);
    }

    public function deleteUserEventInvites($eventId, $userId) {
        $this->eventInviteDao->deleteByUserIdAndEventId($eventId, $userId);
    }

    public function deleteUserEvents($userId) {
        $events = $this->eventDao->findAllUserEvents($userId);

        foreach ($events as $event) {
            $this->deleteEvent($event->getId());
        }
    }

    public function findInviteUserListByEventId($eventId) {
        $inviteList = $this->eventInviteDao->findInviteListByEventId($eventId);

        $userList = array();

        foreach ($inviteList as $invite) {
            $userList[] = $invite->userId;
        }

        return $userList;
    }

    public function findUserListForInvite($eventId, $first, $count, $friendList = array()) {
        return $this->eventInviteDao->findUserListForInvite($eventId, $first, $count, $friendList);
    }

    public function getContentMenu() {
        $menuItems = array();

        if (OW::getUser()->isAuthenticated()) {
            $listNames = array(
                'invited' => array('iconClass' => 'ow_ic_bookmark'),
                'joined' => array('iconClass' => 'ow_ic_friends'),
                'past' => array('iconClass' => 'ow_ic_reply'),
                'latest' => array('iconClass' => 'ow_ic_calendar')
            );
        } else {
            $listNames = array(
                'past' => array('iconClass' => 'ow_ic_reply'),
                'latest' => array('iconClass' => 'ow_ic_calendar')
            );
        }

        if (OW::getConfig()->getValue('eventx', 'enableTagsList') == '1') {
            $menuItem = new BASE_MenuItem();
            $menuItem->setKey('tagged');
            $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx_tag_list'));
            $menuItem->setLabel(OW::getLanguage()->text('eventx', 'common_list_type_tagged_label'));
            $menuItem->setIconClass('ow_ic_tag');
            $menuItems[] = $menuItem;
        }

        if (OW::getConfig()->getValue('eventx', 'enableCategoryList') == '1') {
            $menuItem = new BASE_MenuItem();
            $menuItem->setKey('categories');
            $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx_list_category'));
            $menuItem->setLabel(OW::getLanguage()->text('eventx', 'common_list_type_categories_label'));
            $menuItem->setIconClass('ow_ic_folder');
            $menuItems[] = $menuItem;
        }

        foreach ($listNames as $listKey => $listArr) {
            $menuItem = new BASE_MenuItem();
            $menuItem->setKey($listKey);
            $menuItem->setUrl(OW::getRouter()->urlForRoute('eventx.view_event_list', array('list' => $listKey)));
            $menuItem->setLabel(OW::getLanguage()->text('eventx', 'common_list_type_' . $listKey . '_label'));
            $menuItem->setIconClass($listArr['iconClass']);
            $menuItems[] = $menuItem;
        }

        $event = new BASE_CLASS_EventCollector('eventx.add_content_menu_item');
        OW::getEventManager()->getInstance()->trigger($event);

        $data = $event->getData();

        if (!empty($data) && is_array($data)) {
            $menuItems = array_merge($menuItems, $data);
        }

        return new BASE_CMP_ContentMenu($menuItems);
    }

    public function findByIdList($idList) {
        return $this->eventDao->findByIdList($idList);
    }

    /* Category Services */

    public function getCategoriesList() {
        return $this->categoryDao->getCategoriesList();
    }

    public function findCategoryById($id) {
        return $this->categoryDao->findById($id);
    }

    public function addCategory($category) {
        if ($this->categoryDao->isDuplicate($category->name) && $category->id == 0) {
            return false;
        } else {
            $this->categoryDao->save($category);

            if ($category->master == 0) {
                $category->master = $category->id;
                $this->categoryDao->save($category);
            }
            return $category->id;
        }
    }

    public function deleteCategory($id) {
        $this->categoryDao->deleteById($id);

        $sql = "UPDATE `" . $this->eventCategoryDao->getTableName() . "` SET `categoryId` = 1 
            WHERE `categoryId` = :categoryId";

        OW::getDbo()->query($sql, array('categoryId' => $id));
    }

    public function getCategoryName($id) {
        return $this->categoryDao->findById($id)->name;
    }

    public function getCategoryId($category) {
        return $this->categoryDao->getCategoryId($category);
    }

    /*     * * item category * */

    public function setItemCategories($itemId, $categoryId) {
        return $this->eventCategoryDao->setItemCategories($itemId, $categoryId);
    }

    public function reassignCategory($oldCategory, $newCategory) {
        return $this->eventCategoryDao->reassignCategory($oldCategory, $newCategory);
    }

    public function getItemCategories($itemId) {
        return $this->eventCategoryDao->getItemCategories($itemId);
    }

    public function getAllItemCategories($page, $limit) {
        return $this->eventCategoryDao->getAllItemCategories($page, $limit);
    }

    public function getItemCategoryId($itemId) {
        return $this->eventCategoryDao->getItemCategoryId($itemId);
    }

    public static function formatSimpleDate($timeStamp, $onlyDate = false) {
        $language = OW::getLanguage();
        $militaryTime = (bool) OW::getConfig()->getValue('base', 'military_time');

        if (!$timeStamp) {
            return '_INVALID_TS_';
        }

        $month = $language->text('base', 'date_time_month_short_' . date('n', $timeStamp));
        $day = $language->text('base', 'date_time_week_' . (int) date('w', $timeStamp));

        if ($onlyDate) {
            return $month . strftime(" %d ", $timeStamp) . strftime(" %Y, ", $timeStamp) . $day;
        }

        return $month . strftime(" %d ", $timeStamp) . strftime(" %Y, ", $timeStamp) . $day . ( $militaryTime ? strftime(" %H:%M", $timeStamp) : strftime(" %I:%M %p", $timeStamp));
    }

    public function findTaggedItemsList($tag, $page, $limit) {
        $first = ($page - 1 ) * $limit;

        $itemIdList = BOL_TagService::getInstance()->findEntityListByTag('eventx', $tag, $first, $limit);

        $items = $this->eventDao->findByIdList($itemIdList);

        return $items;
    }

    public function findTaggedItemsCount($tag) {
        return BOL_TagService::getInstance()->findEntityCountByTag('eventx', $tag);
    }

    public function importAll() {

        $query = "SELECT * FROM " . OW_DB_PREFIX . "eventx_item WHERE importStatus = 1 LIMIT 0,:limit";

        $qParams = array('limit' => 2);
        $allEvents = OW::getDbo()->queryForObjectList($query, 'EVENTX_BOL_Event', $qParams);

        foreach ($allEvents as $event) {
            $this->setItemCategories($event->id, 1);

            $sql = "INSERT INTO " . OW_DB_PREFIX . "eventx_invite (eventId,userId,inviterId,displayInvitation,timeStamp)  
                        SELECT " . $event->id . ",userId,inviterId,displayInvitation,timeStamp FROM " . OW_DB_PREFIX . "event_invite
                           WHERE eventId =" . $event->importId;

            OW::getDbo()->query($sql);

            $sql = "INSERT INTO " . OW_DB_PREFIX . "eventx_user (eventId,userId,timeStamp,status)  
                        SELECT " . $event->id . ",userId,timeStamp,status FROM " . OW_DB_PREFIX . "event_user
                           WHERE eventId =" . $event->importId;

            OW::getDbo()->query($sql);

            $sql = "SELECT c.userId, c.message, c.createStamp, c.attachment, e.entityId
                      FROM " . OW_DB_PREFIX . "base_comment_entity e, " . OW_DB_PREFIX . "base_comment c
                     WHERE c.commentEntityId = e.id
                       AND entityType = 'event'
                       AND pluginKey = 'event'
                       AND entityId = " . $event->importId;

            $allComments = OW::getDbo()->queryForList($sql);

            foreach ($allComments as $comments) {
                BOL_CommentService::getInstance()->addComment('eventx', $event->id, 'eventx', $comments['userId'], $comments['message'], $comments['attachment']);
            }

            $sql = "UPDATE " . OW_DB_PREFIX . "eventx_item SET importStatus = 0 WHERE id = " . $event->id;
            OW::getDbo()->query($sql);
        }
    }

}

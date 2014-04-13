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
$router = OW::getRouter();
$router->addRoute(new OW_Route('eventx_admin_index', 'admin/extended-event/settings', "EVENTX_CTRL_Admin", 'index'));
$router->addRoute(new OW_Route('eventx_admin_categories', 'admin/extended-event/categories', "EVENTX_CTRL_Admin", 'categories'));
$router->addRoute(new OW_Route('eventx_admin_approval', 'admin/extended-event/approve', "EVENTX_CTRL_Admin", 'approve'));
$router->addRoute(new OW_Route('eventx_admin_calendar', 'admin/extended-event/calendar', "EVENTX_CTRL_Admin", 'calendar'));
$router->addRoute(new OW_Route('eventx_admin_import', 'admin/extended-event/import', "EVENTX_CTRL_Admin", 'import'));

$router->addRoute(new OW_Route('eventx.add', 'event/add', 'EVENTX_CTRL_Base', 'add'));
$router->addRoute(new OW_Route('eventx.edit', 'event/edit/:eventId', 'EVENTX_CTRL_Base', 'edit'));
$router->addRoute(new OW_Route('eventx.delete', 'event/delete/:eventId', 'EVENTX_CTRL_Base', 'delete'));
$router->addRoute(new OW_Route('eventx.view', 'event/:eventId', 'EVENTX_CTRL_Base', 'view'));
$router->addRoute(new OW_Route('eventx.main_menu_route', 'events', 'EVENTX_CTRL_Base', 'eventsList', array('list' => array(OW_Route::PARAM_OPTION_HIDDEN_VAR => 'latest'))));
$router->addRoute(new OW_Route('eventx.view_event_list', 'events/:list', 'EVENTX_CTRL_Base', 'eventsList'));
$router->addRoute(new OW_Route('eventx.main_user_list', 'event/:eventId/users', 'EVENTX_CTRL_Base', 'eventUserLists', array('list' => array(OW_Route::PARAM_OPTION_HIDDEN_VAR => 'yes'))));
$router->addRoute(new OW_Route('eventx.user_list', 'event/:eventId/users/:list', 'EVENTX_CTRL_Base', 'eventUserLists'));
$router->addRoute(new OW_Route('eventx.private_event', 'event/:eventId/private', 'EVENTX_CTRL_Base', 'privateEvent'));
$router->addRoute(new OW_Route('eventx.invite_accept', 'event/:eventId/:list/invite_accept', 'EVENTX_CTRL_Base', 'inviteListAccept'));
$router->addRoute(new OW_Route('eventx.invite_decline', 'event/:eventId/:list/invite_decline', 'EVENTX_CTRL_Base', 'inviteListDecline'));

$router->addRoute(new OW_Route('eventx_list_category', 'events/category', "EVENTX_CTRL_Base", 'listCategory'));
$router->addRoute(new OW_Route('eventx_category_items', 'events/category/:category', "EVENTX_CTRL_Base", 'listCategoryItems'));
$router->addRoute(new OW_Route('eventx_tag_list', 'event/tagged', "EVENTX_CTRL_Base", 'taglist'));
$router->addRoute(new OW_Route('eventx_view_tagged_list', 'event/tagged/:tag', "EVENTX_CTRL_Base", 'taglist'));
$router->addRoute(new OW_Route('eventx_view_calendar', 'event/calendar', "EVENTX_CTRL_Base", 'calendar'));

function eventx_add_auth_labels(BASE_CLASS_EventCollector $event) {
    $language = OW::getLanguage();
    $event->add(
            array(
                'eventx' => array(
                    'label' => $language->text('eventx', 'auth_group_label'),
                    'actions' => array(
                        'add_event' => $language->text('eventx', 'auth_action_label_add_event'),
                        'view_event' => $language->text('eventx', 'auth_action_label_view_event'),
                        'add_comment' => $language->text('eventx', 'auth_action_label_add_comment')
                    )
                )
            )
    );
}

OW::getEventManager()->bind('admin.add_auth_labels', 'eventx_add_auth_labels');

EVENTX_CLASS_InvitationHandler::getInstance()->init();

$credits = new EVENTX_CLASS_Credits();
OW::getEventManager()->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));

function eventx_on_notify_actions(BASE_CLASS_EventCollector $e) {
    $e->add(array(
        'section' => 'eventx',
        'action' => 'event-invitation',
        'sectionIcon' => 'ow_ic_calendar',
        'sectionLabel' => OW::getLanguage()->text('eventx', 'notifications_section_label'),
        'description' => OW::getLanguage()->text('eventx', 'notifications_new_message'),
        'selected' => true
    ));

    $e->add(array(
        'section' => 'eventx',
        'sectionIcon' => 'ow_ic_files',
        'sectionLabel' => OW::getLanguage()->text('eventx', 'notifications_section_label'),
        'action' => 'event-add_comment',
        'description' => OW::getLanguage()->text('eventx', 'email_notification_comment_setting'),
        'selected' => true
    ));
}

OW::getEventManager()->bind('notifications.collect_actions', 'eventx_on_notify_actions');

function eventx_on_user_invite(OW_Event $e) {
    $params = $e->getParams();

    OW::getCacheManager()->clean(array(EVENTX_BOL_EventUserDao::CACHE_TAG_EVENTX_USER_LIST . $params['eventId']));
}

OW::getEventManager()->bind('eventx.invite_user', 'eventx_on_user_invite');

function eventx_feed_entity_add(OW_Event $e) {
    $params = $e->getParams();

    if ($params['entityType'] != 'eventx') {
        return;
    }

    $eventService = EVENTX_BOL_EventService::getInstance();
    $event = $eventService->findEvent($params['entityId']);

    $url = OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $event->getId()));

    $title = UTIL_String::truncate(strip_tags($event->getTitle()), 100, "...");

    $data = array(
        'time' => $event->getCreateTimeStamp(),
        'ownerId' => $event->getUserId(),
        'string' => OW::getLanguage()->text('eventx', 'feed_add_item_label'),
        'content' => '<div class="clearfix"><div class="ow_newsfeed_item_picture">
            <a href="' . $url . '"><img src="' . ( $event->getImage() ? $eventService->generateImageUrl($event->getImage(), true) : $eventService->generateDefaultImageUrl() ) . '" /></a>
            </div><div class="ow_newsfeed_item_content">
            <a class="ow_newsfeed_item_title" href="' . $url . '">' . $title . '</a><div class="ow_remark ow_smallmargin">' . UTIL_String::truncate(strip_tags($event->getDescription()), 200, '...') . '</div><div class="ow_newsfeed_action_activity event_newsfeed_activity">[ph:activity]</div></div></div>',
        'view' => array(
            'iconClass' => 'ow_ic_calendar'
        )
    );

    if ($event->getWhoCanView() == EVENTX_BOL_EventService::CAN_VIEW_INVITATION_ONLY) {
        $data['params']['visibility'] = 4;
    }

    $e->setData($data);
}

OW::getEventManager()->bind('feed.on_entity_add', 'eventx_feed_entity_add');

function eventx_after_event_edit(OW_Event $event) {
    $params = $event->getParams();
    $evemtId = (int) $params['eventId'];

    $eventService = EVENTX_BOL_EventService::getInstance();
    $event = $eventService->findEvent($evemtId);

    $url = OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $event->getId()));
    $thumb = $eventService->generateImageUrl($event->image, true);

    $data = array(
        'time' => $event->getCreateTimeStamp(),
        'ownerId' => $event->getUserId(),
        'string' => OW::getLanguage()->text('eventx', 'feed_add_item_label'),
        'content' => '<div class="clearfix"><div class="ow_newsfeed_item_picture">
            <a href="' . $url . '"><img src="' . $thumb . '" /></a>
            </div><div class="ow_newsfeed_item_content">
            <a class="ow_newsfeed_item_title" href="' . $url . '">' . $event->getTitle() . '</a><div class="ow_remark ow_smallmargin">' . UTIL_String::truncate(strip_tags($event->getDescription()), 200, '...') . '</div><div class="ow_newsfeed_action_activity event_newsfeed_activity">[ph:activity]</div></div></div>',
        'view' => array(
            'iconClass' => 'ow_ic_calendar'
        )
    );

    if ($event->getWhoCanView() == EVENTX_BOL_EventService::CAN_VIEW_INVITATION_ONLY) {
        $data['params']['visibility'] = 4;
    }

    $event = new OW_Event('feed.action', array(
        'entityType' => 'eventx',
        'entityId' => $evemtId,
        'pluginKey' => 'eventx'
            ), $data);

    OW::getEventManager()->trigger($event);
}

OW::getEventManager()->bind(EVENTX_BOL_EventService::EVENTX_AFTER_EVENTX_EDIT, 'eventx_after_event_edit');

function eventx_elst_add_new_content_item(BASE_CLASS_EventCollector $event) {
    if (!OW::getUser()->isAuthorized('eventx', 'add_event')) {
        return;
    }

    $resultArray = array(
        BASE_CMP_AddNewContent::DATA_KEY_ICON_CLASS => 'ow_ic_calendar',
        BASE_CMP_AddNewContent::DATA_KEY_URL => OW::getRouter()->urlForRoute('eventx.add'),
        BASE_CMP_AddNewContent::DATA_KEY_LABEL => OW::getLanguage()->text('eventx', 'add_new_link_label')
    );

    $event->add($resultArray);
}

OW::getEventManager()->bind(BASE_CMP_AddNewContent::EVENT_NAME, 'eventx_elst_add_new_content_item');

function eventx_ads_enabled(BASE_EventCollector $event) {
    $event->add('eventx');
}

OW::getEventManager()->bind('ads.enabled_plugins', 'eventx_ads_enabled');

function eventx_plugin_is_active(OW_Event $event) {
    $event->setData(true);
}

OW::getEventManager()->bind('eventx.is_plugin_active', 'eventx_plugin_is_active');

function eventx_on_user_delete(OW_Event $event) {
    $params = $event->getParams();

    if (empty($params['deleteContent'])) {
        return;
    }

    $userId = $params['userId'];

    EVENTX_BOL_EventService::getInstance()->deleteUserEvents($userId);
}

OW::getEventManager()->bind(OW_EventManager::ON_USER_UNREGISTER, 'eventx_on_user_delete');

function eventx_privacy_add_action(BASE_CLASS_EventCollector $event) {
    $language = OW::getLanguage();

    $action = array(
        'key' => 'event_view_attend_events',
        'pluginKey' => 'eventx',
        'label' => $language->text('eventx', 'privacy_action_view_attend_events'),
        'description' => '',
        'defaultValue' => 'everybody'
    );

    $event->add($action);
}

OW::getEventManager()->bind('plugin.privacy.get_action_list', 'eventx_privacy_add_action');

function eventx_feed_on_item_render_activity(OW_Event $event) {
    $params = $event->getParams();
    $data = $event->getData();

    if ($params['action']['entityType'] != 'eventx') {
        return;
    }

    $eventId = $params['action']['entityId'];
    $usersCount = EVENTX_BOL_EventService::getInstance()->findEventUsersCount($eventId, EVENTX_BOL_EventService::USER_STATUS_YES);

    if ($usersCount == 1) {
        return;
    }

    $users = EVENTX_BOL_EventService::getInstance()->findEventUsers($eventId, EVENTX_BOL_EventService::USER_STATUS_YES, null, 6);

    $userIds = array();

    foreach ($users as $user) {
        $userIds[] = $user->getUserId();
    }

    $activityUserIds = array();

    foreach ($params['activity'] as $activity) {
        if ($activity['activityType'] == 'event-join') {
            $activityUserIds[] = $activity['data']['userId'];
        }
    }

    $lastUserId = reset($activityUserIds);
    $follows = array_intersect($activityUserIds, $userIds);
    $notFollows = array_diff($userIds, $activityUserIds);
    $idlist = array_merge($follows, $notFollows);

    $avatarList = new BASE_CMP_MiniAvatarUserList(array_slice($idlist, 0, 5));
    $avatarList->setEmptyListNoRender(true);

    if (count($idlist) > 5) {
        $avatarList->setViewMoreUrl(OW::getRouter()->urlForRoute('eventx.main_user_list', array('eventId' => $eventId)));
    }

    $language = OW::getLanguage();

    $avatarList = new BASE_CMP_MiniAvatarUserList($idlist);
    $content = $avatarList->render();

    if ($lastUserId) {
        $userName = BOL_UserService::getInstance()->getDisplayName($lastUserId);
        $userUrl = BOL_UserService::getInstance()->getUserUrl($lastUserId);
        $content .= $language->text('eventx', 'feed_activity_joined', array('user' => '<a href="' . $userUrl . '">' . $userName . '</a>'));
    }

    $data['assign']['activity'] = array('template' => 'activity', 'vars' => array(
            'title' => $language->text('eventx', 'feed_activity_users', array('usersCount' => $usersCount)),
            'content' => $content
    ));

    $event->setData($data);
}

OW::getEventManager()->bind('feed.on_item_render', 'eventx_feed_on_item_render_activity');

function eventx_feed_collect_privacy(BASE_CLASS_EventCollector $event) {
    $event->add(array('event-join', 'event_view_attend_events'));
}

OW::getEventManager()->bind('feed.collect_privacy', 'eventx_feed_collect_privacy');

function eventx_feed_collect_configurable_activity(BASE_CLASS_EventCollector $event) {
    $language = OW::getLanguage();
    $event->add(array(
        'label' => $language->text('eventx', 'feed_content_label'),
        'activity' => '*:event'
    ));
}

OW::getEventManager()->bind('feed.collect_configurable_activity', 'eventx_feed_collect_configurable_activity');

function eventx_add_comment(OW_Event $e) {
    $params = $e->getParams();

    if (empty($params['entityType']) || $params['entityType'] != 'eventx') {
        return;
    }

    $entityId = $params['entityId'];
    $userId = $params['userId'];
    $commentId = $params['commentId'];
    $event = EVENTX_BOL_EventService::getInstance()->findEvent($entityId);

    $comment = BOL_CommentService::getInstance()->findComment($commentId);
    $eventUrl = OW::getRouter()->urlForRoute('eventx.view', array('eventId' => $event->id));

    $eventImage = null;
    if (!empty($event->image)) {
        $eventImage = EVENTX_BOL_EventService::getInstance()->generateImageUrl($event->image, true);
    }

    $string = OW::getLanguage()->text('eventx', 'feed_activity_comment_string');

    OW::getEventManager()->trigger(new OW_Event('feed.activity', array(
        'activityType' => 'comment',
        'activityId' => $commentId,
        'entityId' => $entityId,
        'entityType' => $params['entityType'],
        'userId' => $userId,
        'pluginKey' => 'eventx'
            ), array(
        'string' => $string,
        'line' => null
    )));

    if ($userId != $event->userId) {
        $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($userId), true, true, false, false);
        $avatar = $avatars[$userId];

        $contentImage = array();

        if (!empty($eventImage)) {
            $contentImage = array('src' => $eventImage);
        }

        $event = new OW_Event('notifications.add', array(
            'pluginKey' => 'eventx',
            'entityType' => $params['entityType'],
            'entityId' => $params['entityId'],
            'action' => 'event-add_comment',
            'userId' => $event->userId,
            'time' => time()
                ), array(
            'avatar' => $avatar,
            'string' => array(
                'key' => 'eventx+email_notification_comment',
                'vars' => array(
                    'userName' => BOL_UserService::getInstance()->getDisplayName($userId),
                    'userUrl' => BOL_UserService::getInstance()->getUserUrl($userId),
                    'url' => $eventUrl,
                    'title' => strip_tags($event->title)
                )
            ),
            'url' => $eventUrl,
            'contentImage' => $contentImage
        ));

        OW::getEventManager()->trigger($event);
    }
}

OW::getEventManager()->bind('base_add_comment', 'eventx_add_comment');

function eventx_feed_like(OW_Event $event) {
    $params = $event->getParams();

    if ($params['entityType'] != 'eventx') {
        return;
    }

    $userId = (int) $params['userId'];
    $entityId = $params['entityId'];

    $string = OW::getLanguage()->text('eventx', 'feed_activity_event_string_like');

    if ($userId == OW::getUser()->getId()) {
        $string = OW::getLanguage()->text('eventx', 'feed_activity_event_string_like_own');
    }

    OW::getEventManager()->trigger(new OW_Event('feed.activity', array(
        'activityType' => 'like',
        'activityId' => $params['userId'],
        'entityId' => $params['entityId'],
        'entityType' => $params['entityType'],
        'userId' => $params['userId'],
        'pluginKey' => 'eventx'
            ), array(
        'string' => $string,
        'line' => null
    )));
}

OW::getEventManager()->bind('feed.after_like_added', 'eventx_feed_like');

function eventx_quick_links(BASE_CLASS_EventCollector $event) {
    $service = EVENTX_BOL_EventService::getInstance();
    $userId = OW::getUser()->getId();

    $eventsCount = $service->findUserParticipatedEventsCount($userId);
    $invitesCount = $service->findUserInvitedEventsCount($userId);

    if ($eventsCount > 0 || $invitesCount > 0) {
        $event->add(array(
            BASE_CMP_QuickLinksWidget::DATA_KEY_LABEL => OW::getLanguage()->text('eventx', 'common_list_type_joined_label'),
            BASE_CMP_QuickLinksWidget::DATA_KEY_URL => OW::getRouter()->urlForRoute('eventx.view_event_list', array('list' => 'joined')),
            BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT => $eventsCount,
            BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT_URL => OW::getRouter()->urlForRoute('eventx.view_event_list', array('list' => 'joined')),
            BASE_CMP_QuickLinksWidget::DATA_KEY_ACTIVE_COUNT => $invitesCount,
            BASE_CMP_QuickLinksWidget::DATA_KEY_ACTIVE_COUNT_URL => OW::getRouter()->urlForRoute('eventx.view_event_list', array('list' => 'invited'))
        ));
    }
}

OW::getEventManager()->bind(BASE_CMP_QuickLinksWidget::EVENT_NAME, 'eventx_quick_links');

function eventx_on_add_event(OW_Event $event) {
    OW::getCacheManager()->clean(array(EVENTX_BOL_EventDao::CACHE_TAG_EVENTX_LIST));
}

OW::getEventManager()->bind(EVENTX_BOL_EventService::EVENTX_ON_CREATE_EVENT, 'eventx_on_add_event');

function eventx_on_delete_event(OW_Event $event) {
    $params = $event->getParams();
    $eventId = !empty($params['eventId']) ? $params['eventId'] : null;

    OW::getCacheManager()->clean(array(EVENTX_BOL_EventDao::CACHE_TAG_EVENTX_LIST));

    if (isset($eventId)) {
        OW::getCacheManager()->clean(array(EVENTX_BOL_EventUserDao::CACHE_TAG_EVENTX_USER_LIST . $eventId));
    }

    OW::getEventManager()->trigger(new OW_Event('feed.delete_item', array(
        'entityType' => 'eventx',
        'entityId' => $eventId
    )));
}

OW::getEventManager()->bind(EVENTX_BOL_EventService::EVENTX_ON_DELETE_EVENT, 'eventx_on_delete_event');

function eventx_on_edit_event(OW_Event $event) {
    OW::getCacheManager()->clean(array(EVENTX_BOL_EventDao::CACHE_TAG_EVENTX_LIST));
}

OW::getEventManager()->bind(EVENTX_BOL_EventService::EVENTX_AFTER_EVENTX_EDIT, 'eventx_on_edit_event');

function eventx_on_change_user_status(OW_Event $event) {
    $params = $event->getParams();
    $eventId = !empty($params['eventId']) ? $params['eventId'] : null;
    $userId = !empty($params['userId']) ? $params['userId'] : null;

    if (!isset($eventId)) {
        return;
    }

    OW::getCacheManager()->clean(array(EVENTX_BOL_EventUserDao::CACHE_TAG_EVENTX_USER_LIST . $eventId));

    if (!isset($userId)) {
        return;
    }

    $eventDto = EVENTX_BOL_EventService::getInstance()->findEvent($eventId);

    $eventUser = EVENTX_BOL_EventService::getInstance()->findEventUser($eventId, $userId);

    if (empty($eventDto) || empty($eventUser)) {
        return;
    }

    if ($eventUser->getStatus() == EVENTX_BOL_EventService::USER_STATUS_YES && $eventDto->getWhoCanView() == EVENTX_BOL_EventService::CAN_VIEW_ANYBODY) {
        $userName = BOL_UserService::getInstance()->getDisplayName($eventDto->getUserId());
        $userUrl = BOL_UserService::getInstance()->getUserUrl($eventDto->getUserId());
        $userEmbed = '<a href="' . $userUrl . '">' . $userName . '</a>';

        OW::getEventManager()->trigger(new OW_Event('feed.activity', array(
            'activityType' => 'event-join',
            'activityId' => $eventUser->getId(),
            'entityId' => $eventDto->getId(),
            'entityType' => 'eventx',
            'userId' => $eventUser->getUserId(),
            'pluginKey' => 'eventx'
                ), array(
            'eventId' => $eventDto->getId(),
            'userId' => $eventUser->getUserId(),
            'eventUserId' => $eventUser->getId(),
            'string' => OW::getLanguage()->text('eventx', 'feed_actiovity_attend_string', array('user' => $userEmbed)),
            'feature' => array()
        )));
    }
}

OW::getEventManager()->bind(EVENTX_BOL_EventService::EVENTX_ON_CHANGE_USER_STATUS, 'eventx_on_change_user_status');

function eventx_get_content_menu(OW_Event $event) {
    $event->setData(EVENTX_BOL_EventService::getInstance()->getContentMenu());
}

OW::getEventManager()->bind('eventx.get_content_menu', 'eventx_get_content_menu');

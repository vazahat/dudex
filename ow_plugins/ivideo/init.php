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
OW::getRouter()->addRoute(new OW_Route('ivideo_admin', 'admin/ivideo/settings', "IVIDEO_CTRL_Admin", 'index'));
OW::getRouter()->addRoute(new OW_Route('ivideo_categories', 'admin/ivideo/categories', "IVIDEO_CTRL_Admin", 'categories'));
OW::getRouter()->addRoute(new OW_Route('ivideo_admin_approval', 'admin/ivideo/approve-videos', "IVIDEO_CTRL_Admin", 'approve'));

OW::getRouter()->addRoute(new OW_Route('ivideo_upload', 'upload-your-video', "IVIDEO_CTRL_Action", 'upload'));
OW::getRouter()->addRoute(new OW_Route('ivideo_view_list_main', 'uploaded-video', "IVIDEO_CTRL_Action", 'viewList'));
OW::getRouter()->addRoute(new OW_Route('ivideo_view_list', 'uploaded-video/:type', "IVIDEO_CTRL_Action", 'viewList'));
OW::getRouter()->addRoute(new OW_Route('ivideo_tag_list', 'uploaded-video/tagged', "IVIDEO_CTRL_Action", 'taglist'));
OW::getRouter()->addRoute(new OW_Route('ivideo_view_tagged_list', 'uploaded-video/tagged/:tag', "IVIDEO_CTRL_Action", 'taglist'));
OW::getRouter()->addRoute(new OW_Route('ivideo_view_video', 'uploaded-video/view/:id', "IVIDEO_CTRL_Action", 'viewvideo'));
OW::getRouter()->addRoute(new OW_Route('ivideo_edit_video', 'uploaded-video/edit/:id', "IVIDEO_CTRL_Action", 'editvideo'));
OW::getRouter()->addRoute(new OW_Route('ivideo_user_video_list', 'uploaded-video/user/:user', 'IVIDEO_CTRL_Action', 'viewUserVideoList'));
OW::getRouter()->addRoute(new OW_Route('ivideo_list_category', 'uploaded-video/category', "IVIDEO_CTRL_Action", 'listCategory'));
OW::getRouter()->addRoute(new OW_Route('ivideo_category_items', 'uploaded-video/category/:category', "IVIDEO_CTRL_Action", 'listCategoryVideos'));

function ivideo_after_route(OW_Event $event) {

    $handlerAttributes = OW::getRequestHandler()->getHandlerAttributes();
    $attrKey = $handlerAttributes[OW_RequestHandler::ATTRS_KEY_CTRL];
    $attrAction = $handlerAttributes[OW_RequestHandler::ATTRS_KEY_ACTION];

    if (( $attrKey == 'VIDEO_CTRL_Video' && ( $attrAction == 'viewList' || $attrAction == 'viewTaggedList' ) ) ||
            ( $attrKey == 'VIDEOPLUS_CTRL_Video' && ( $attrAction == 'viewList' || $attrAction == 'viewTaggedList' || $attrAction == 'listCategory' || $attrAction == 'listCategoryItems' ) )) {

        $code = '<li class=\"uploads\"><a href=\"' . OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'latest')) . '\"><span class=\"ow_ic_video\">' . OW::getLanguage()->text('ivideo', 'video_uploads') . '</span></a></li>';

        $textLocation = OW::getConfig()->getValue('ivideo', 'makeUploaderMain') == '1' ? 'prepend' : 'append';

        $headSrc = '$(document).ready(function()
                    {
                       $(".ow_content_menu ").' . $textLocation . '("' . $code . '");        
                    });';

        OW::getDocument()->addCustomHeadInfo('<script type="text/javascript">' . $headSrc . '</script>');
    }
}

OW::getEventManager()->bind(OW_EventManager::ON_AFTER_ROUTE, 'ivideo_after_route');

function ivideo_dashboard_menu_item(BASE_CLASS_EventCollector $event) {
    if (!OW::getUser()->isAuthenticated()) {
        return;
    }

    $event->add(array('label' => OW::getLanguage()->text('ivideo', 'ivideo_dashboard_index_text'), 'url' => OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'latest'))));
}

OW::getEventManager()->bind('base.add_main_console_item', 'ivideo_dashboard_menu_item');

function ivideo_add_auth_labels(BASE_CLASS_EventCollector $event) {
    $language = OW::getLanguage();
    $event->add(
            array(
                'ivideo' => array(
                    'label' => $language->text('ivideo', 'auth_group_label'),
                    'actions' => array(
                        'add' => $language->text('ivideo', 'auth_action_label_add'),
                        'view' => $language->text('ivideo', 'auth_action_label_view'),
                        'add_comment' => $language->text('ivideo', 'auth_action_label_add_comment'),
                        'delete_comment_by_content_owner' => $language->text('ivideo', 'auth_action_label_delete_comment_by_content_owner')
                    )
                )
            )
    );
}

OW::getEventManager()->bind('admin.add_auth_labels', 'ivideo_add_auth_labels');

function ivideo_add_new_content_item(BASE_CLASS_EventCollector $event) {

    if (OW::getUser()->isAuthenticated() && OW::getUser()->isAuthorized('ivideo', 'add')) {
        $resultArray = array(
            BASE_CMP_AddNewContent::DATA_KEY_ICON_CLASS => 'ow_ic_video',
            BASE_CMP_AddNewContent::DATA_KEY_URL => OW::getRouter()->urlForRoute('ivideo_upload'),
            BASE_CMP_AddNewContent::DATA_KEY_LABEL => OW::getLanguage()->text('ivideo', 'upload_new_video')
        );

        $event->add($resultArray);
    }
}

OW::getEventManager()->bind(BASE_CMP_AddNewContent::EVENT_NAME, 'ivideo_add_new_content_item');

$credits = new IVIDEO_CLASS_Credits();
OW::getEventManager()->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));

OW::getThemeManager()->addDecorator('ivideo_list_item', 'ivideo');

function ivideo_quick_links(BASE_CLASS_EventCollector $event) {
    $service = IVIDEO_BOL_Service::getInstance();
    $userId = OW::getUser()->getId();
    $username = OW::getUser()->getUserObject()->getUsername();

    $videosCount = (int) $service->findUserVideosCount($userId);

    if ($videosCount > 0) {
        $event->add(array(
            BASE_CMP_QuickLinksWidget::DATA_KEY_LABEL => OW::getLanguage()->text('ivideo', 'my_video'),
            BASE_CMP_QuickLinksWidget::DATA_KEY_URL => OW::getRouter()->urlForRoute('ivideo_user_video_list', array('user' => $username)),
            BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT => $videosCount,
            BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT_URL => OW::getRouter()->urlForRoute('ivideo_user_video_list', array('user' => $username))
        ));
    }
}

OW::getEventManager()->bind(BASE_CMP_QuickLinksWidget::EVENT_NAME, 'ivideo_quick_links');

function ivideo_delete_user_content(OW_Event $event) {
    $params = $event->getParams();

    if (!isset($params['deleteContent']) || !(bool) $params['deleteContent']) {
        return;
    }

    $userId = (int) $params['userId'];

    if ($userId > 0) {
        IVIDEO_BOL_Service::getInstance()->deleteUserVideos($userId);
    }
}

OW::getEventManager()->bind(OW_EventManager::ON_USER_UNREGISTER, 'ivideo_delete_user_content');

function ivideo_on_notify_actions(BASE_CLASS_EventCollector $e) {
    $e->add(array(
        'section' => 'ivideo',
        'action' => 'ivideo-add_comment',
        'description' => OW::getLanguage()->text('ivideo', 'email_notifications_setting_comment'),
        'sectionIcon' => 'ow_ic_video',
        'sectionLabel' => OW::getLanguage()->text('ivideo', 'email_notifications_section_label'),
        'selected' => true
    ));
}

OW::getEventManager()->bind('base.notify_actions', 'ivideo_on_notify_actions');

function ivideo_ads_enabled(BASE_EventCollector $event) {
    $event->add('ivideo');
}

OW::getEventManager()->bind('ads.enabled_plugins', 'ivideo_ads_enabled');

function ivideo_privacy_add_action(BASE_CLASS_EventCollector $event) {
    $language = OW::getLanguage();

    $action = array(
        'key' => 'ivideo_view_video',
        'pluginKey' => 'ivideo',
        'label' => $language->text('ivideo', 'privacy_action_view_video'),
        'description' => '',
        'defaultValue' => 'everybody'
    );

    $event->add($action);
}

OW::getEventManager()->bind('plugin.privacy.get_action_list', 'ivideo_privacy_add_action');

function ivideo_on_change_privacy(OW_Event $e) {
    $params = $e->getParams();
    $userId = (int) $params['userId'];

    $actionList = $params['actionList'];

    if (empty($actionList['ivideo_view_video'])) {
        return;
    }

    IVIDEO_BOL_Service::getInstance()->updateUserVideosPrivacy($userId, $actionList['ivideo_view_video']);
}

OW::getEventManager()->bind('plugin.privacy.on_change_action_privacy', 'ivideo_on_change_privacy');

function ivideo_feed_collect_configurable_activity(BASE_CLASS_EventCollector $event) {
    $language = OW::getLanguage();
    $event->add(array(
        'label' => $language->text('ivideo', 'feed_content_label'),
        'activity' => '*:ivideo-comments'
    ));
}

OW::getEventManager()->bind('feed.collect_configurable_activity', 'ivideo_feed_collect_configurable_activity');

function ivideo_add_comment_notification(OW_Event $event) {
    $params = $event->getParams();

    if (empty($params['entityType']) || $params['entityType'] !== 'ivideo-comments') {
        return;
    }

    $entityId = $params['entityId'];
    $userId = $params['userId'];
    $commentId = $params['commentId'];

    $clipService = IVIDEO_BOL_Service::getInstance();
    $userService = BOL_UserService::getInstance();

    $video = $clipService->findVideoById($entityId);

    if ($video->owner != $userId) {
        $comment = BOL_CommentService::getInstance()->findComment($commentId);
        $url = OW::getRouter()->urlForRoute('view_clip', array('id' => $entityId));

        $event = new OW_Event('base.notify', array(
            'plugin' => 'ivideo',
            'pluginIcon' => 'ow_ic_video',
            'action' => 'ivideo-add_comment',
            'userId' => $clip->userId,
            'string' => OW::getLanguage()->text('ivideo', 'email_notifications_comment', array(
                'userName' => $userService->getDisplayName($userId),
                'userUrl' => $userService->getUserUrl($userId),
                'videoUrl' => $url,
                'videoTitle' => strip_tags($video->name)
            )),
            'content' => $comment->getMessage(),
            'url' => $url
        ));

        OW::getEventManager()->trigger($event);
    }
}

OW::getEventManager()->bind('base_add_comment', 'ivideo_add_comment_notification');

function ivideo_feed_entity_add(OW_Event $e) {
    $params = $e->getParams();
    $data = $e->getData();

    if ($params['entityType'] != 'ivideo-comments') {
        return;
    }

    $videoService = IVIDEO_BOL_Service::getInstance();
    $video = $videoService->findVideoById($params['entityId']);

    $url = OW::getRouter()->urlForRoute('ivideo_view_video', array('id' => $video->id));

    $content = UTIL_String::truncate(strip_tags($video->description), 150, '...');
    $title = UTIL_String::truncate(strip_tags($video->name), 100, '...');

    $thumbImgUrl = OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir() . $video->filename . ".png";

    if (file_exists($thumbImgUrl)) {
        $thumb = $thumbImgUrl;
    } else {
        $thumb = OW::getPluginManager()->getPlugin('ivideo')->getStaticUrl() . 'video.png';
    }

    $string = OW::getLanguage()->text('ivideo', 'feed_entity_entry_string', array('title' => $title));

    $markup = '<div class="clearfix"><div class="ow_newsfeed_item_picture">';
    $markup .= '<a style="display: block;" href="' . $url . '"><div style="width: 75px; height: 60px; background: url(' . $thumb . ') center center;"></div></a>';
    $markup .= '</div><div class="ow_newsfeed_item_content"><a href="' . $url . '">' . $string . '</a><div class="ow_remark">';
    $markup .= $content . '</div></div></div>';

    $data = array(
        'time' => (int) $video->timestamp,
        'ownerId' => $video->owner,
        'content' => '<div class="clearfix">' . $markup . '</div>',
        'view' => array(
            'iconClass' => 'ow_ic_video'
        )
    );

    $e->setData($data);
}

OW::getEventManager()->bind('feed.on_entity_add', 'ivideo_feed_entity_add');

function ivideo_feed_video_comment(OW_Event $event) {
    $params = $event->getParams();

    if ($params['entityType'] != 'ivideo-comments') {
        return;
    }

    $service = IVIDEO_BOL_Service::getInstance();
    $userId = $service->findVideoOwner($params['entityId']);

    if ($userId == $params['userId']) {
        $string = OW::getLanguage()->text('ivideo', 'feed_activity_owner_video_string');
    } else {
        $userName = BOL_UserService::getInstance()->getDisplayName($userId);
        $userUrl = BOL_UserService::getInstance()->getUserUrl($userId);
        $userEmbed = '<a href="' . $userUrl . '">' . $userName . '</a>';
        $string = OW::getLanguage()->text('ivideo', 'feed_activity_video_string', array('user' => $userEmbed));
    }

    OW::getEventManager()->trigger(new OW_Event('feed.activity', array(
        'activityType' => 'comment',
        'activityId' => $params['commentId'],
        'entityId' => $params['entityId'],
        'entityType' => $params['entityType'],
        'userId' => $params['userId'],
        'pluginKey' => 'ivideo'
            ), array(
        'string' => $string
    )));
}

OW::getEventManager()->bind('feed.after_comment_add', 'ivideo_feed_video_comment');

function ivideo_feed_video_like(OW_Event $event) {
    $params = $event->getParams();

    if ($params['entityType'] != 'ivideo-comments') {
        return;
    }

    $service = IVIDEO_BOL_Service::getInstance();
    $userId = $service->findVideoOwner($params['entityId']);

    $userName = BOL_UserService::getInstance()->getDisplayName($userId);
    $userUrl = BOL_UserService::getInstance()->getUserUrl($userId);
    $userEmbed = '<a href="' . $userUrl . '">' . $userName . '</a>';

    OW::getEventManager()->trigger(new OW_Event('feed.activity', array(
        'activityType' => 'like',
        'activityId' => $params['userId'],
        'entityId' => $params['entityId'],
        'entityType' => $params['entityType'],
        'userId' => $params['userId'],
        'pluginKey' => 'ivideo'
            ), array(
        'string' => OW::getLanguage()->text('ivideo', 'feed_activity_video_string_like', array(
            'user' => $userEmbed
        ))
    )));
}

OW::getEventManager()->bind('feed.after_like_added', 'ivideo_feed_video_like');

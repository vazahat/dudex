<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
$plugin = OW::getPluginManager()->getPlugin('vwvr');
/**
$classesToAutoload = array(
    'VwvrProviders' => $plugin->getRootDir() . 'classes' . DS . 'vwvr_providers.php'
);

OW::getAutoloader()->addClassArray($classesToAutoload);
*/
OW::getRouter()->addRoute(
    new OW_Route(
        'vwvr_vwview_list_vr',
        'vwvr/viewlist/:listType/',
        'VWVR_CTRL_Vwvr',
        'viewList',
        array('listType' => array('default' => 'latest'))
    )
);

OW::getRouter()->addRoute(new OW_Route('vwvr_list_index', 'vwvr/', 'VWVR_CTRL_Vwvr', 'viewList'));

OW::getRouter()->addRoute(new OW_Route('vwrecord_clip_vr', 'vwvr/record/', 'VWVR_CTRL_Vwvr', 'record'));
OW::getRouter()->addRoute(new OW_Route('vwview_clip_vr', 'vwvr/view/:id/', 'VWVR_CTRL_Vwvr', 'view'));
OW::getRouter()->addRoute(new OW_Route('vwedit_clip_vr', 'vwvr/edit/:id/', 'VWVR_CTRL_Vwvr', 'edit'));
OW::getRouter()->addRoute(new OW_Route('vwview_list_vr', 'vwvr/viewlist/:listType/', 'VWVR_CTRL_Vwvr', 'viewList'));
OW::getRouter()->addRoute(new OW_Route('vwview_taggedlist_st_vr', 'vwvr/viewlist/tagged/', 'VWVR_CTRL_Vwvr', 'viewTaggedList'));
OW::getRouter()->addRoute(new OW_Route('vwview_tagged_list_vr', 'vwvr/viewlist/tagged/:tag', 'VWVR_CTRL_Vwvr', 'viewTaggedList'));
OW::getRouter()->addRoute(new OW_Route('vwvr_user_vwvr_list', 'vwvr/user-vwvr/:user', 'VWVR_CTRL_Vwvr', 'viewUserVwvrList'));

OW::getRouter()->addRoute(new OW_Route('vwvr_admin_config', 'admin/vwvr/', 'VWVR_CTRL_Admin', 'index'));

OW::getThemeManager()->addDecorator('vwvr_list_item', $plugin->getKey());
OW::getThemeManager()->addDecorator('vwvr_online_list_item', $plugin->getKey());

function vwvr_elst_add_new_content_item( BASE_CLASS_EventCollector $event )
{
    $resultArray = array(
        BASE_CMP_AddNewContent::DATA_KEY_ICON_CLASS => 'ow_ic_vwvr',
        BASE_CMP_AddNewContent::DATA_KEY_URL => OW::getRouter()->urlFor('VWVR_CTRL_Add', 'index'),
        BASE_CMP_AddNewContent::DATA_KEY_LABEL => OW::getLanguage()->text('vwvr', 'vwvr')
    );

    $event->add($resultArray);
}

OW::getEventManager()->bind(BASE_CMP_AddNewContent::EVENT_NAME, 'vwvr_elst_add_new_content_item');


function vwvr_delete_user_content( OW_Event $event )
{
    $params = $event->getParams();

    if ( !isset($params['deleteContent']) || !(bool) $params['deleteContent'] )
    {
        return;
    }

    $userId = (int) $params['userId'];

    if ( $userId > 0 )
    {
        VWVR_BOL_ClipService::getInstance()->deleteUserClips($userId);
    }
}
OW::getEventManager()->bind(OW_EventManager::ON_USER_UNREGISTER, 'vwvr_delete_user_content');


function vwvr_on_notify_actions( BASE_CLASS_EventCollector $e )
{
    $e->add(array(
        'section' => 'vwvr',
        'action' => 'vwvr-add_comment',
        'description' => OW::getLanguage()->text('vwvr', 'email_notifications_setting_comment'),
        'sectionIcon' => 'ow_ic_vwvr',
        'sectionLabel' => OW::getLanguage()->text('vwvr', 'email_notifications_section_label'),
        'selected' => true
    ));
}
OW::getEventManager()->bind('base.notify_actions', 'vwvr_on_notify_actions');

function vwvr_add_comment_notification( OW_Event $event )
{
    $params = $event->getParams();

    if ( empty($params['entityType']) || $params['entityType'] !== 'vwvr_comments' )
    {
        return;
    }

    $entityId = $params['entityId'];
    $userId = $params['userId'];
    $commentId = $params['commentId'];

    $clipService = VWVR_BOL_ClipService::getInstance();
    $userService = BOL_UserService::getInstance();

    $clip = $clipService->findClipById($entityId);

    if ( $clip->userId != $userId )
    {
        $comment = BOL_CommentService::getInstance()->findComment($commentId);
        $url = OW::getRouter()->urlForRoute('vwview_clip_vr', array('id' => $entityId));

        $event = new OW_Event('base.notify', array(
                'plugin' => 'vwvr',
                'pluginIcon' => 'ow_ic_vwvr',
                'action' => 'vwvr-add_comment',
                'userId' => $clip->userId,
                'string' => OW::getLanguage()->text('vwvr', 'email_notifications_comment', array(
                    'userName' => $userService->getDisplayName($userId),
                    'userUrl' => $userService->getUserUrl($userId),
                    'vwvrUrl' => $url,
                    'vwvrTitle' => strip_tags($clip->room_name)
                )),
                'content' => $comment->getMessage(),
                'url' => $url
            ));

        OW::getEventManager()->trigger($event);
    }
}
OW::getEventManager()->bind('base_add_comment', 'vwvr_add_comment_notification');

function vwvr_feed_entity_add( OW_Event $e )
{
    $params = $e->getParams();
    $data = $e->getData();
    
    if ( $params['entityType'] != 'vwvr_comments' )
    {
        return;
    }
    
    $vwvrService = VWVR_BOL_ClipService::getInstance();
    $clip = $vwvrService->findClipById($params['entityId']);
    $thumb = $vwvrService->getClipThumbUrl($clip->id);
    
    $url = OW::getRouter()->urlForRoute('vwview_clip_vr', array('id' => $clip->id));

    $content = UTIL_String::truncate(strip_tags($clip->description), 150, '...');
    $title = UTIL_String::truncate(strip_tags($clip->room_name), 100, '...');

    if ( $thumb == "undefined" )
    {
        $thumb = $vwvrService->getClipDefaultThumbUrl();

        $markup  = '<div class="clearfix"><div class="ow_newsfeed_item_picture">';
        $markup .= '<a style="display: block;" href="' . $url . '"><div style="width: 75px; height: 60px; background: url('.$thumb.') no-repeat center center;"></div></a>';
        $markup .= '</div><div class="ow_newsfeed_item_content"><a href="' . $url . '">' . $title . '</a><div class="ow_remark">'; 
        $markup .= $content . '</div></div></div>';       
    }
    else
    {
        $markup  = '<div class="clearfix ow_newsfeed_large_image"><div class="ow_newsfeed_item_picture">';
        $markup .= '<a href="' . $url . '"><img src="' . $thumb . '" /></a>';
        $markup .= '</div><div class="ow_newsfeed_item_content"><a href="' . $url . '">' . $title . '</a><div class="ow_remark">'; 
        $markup .= $content . '</div></div></div>';
    }
    
    $data = array(
        'time' => (int) $clip->addDatetime,
        'ownerId' => $clip->userId,
        'content' => '<div class="clearfix">' . $markup . '</div>',
        'view' => array(
            'iconClass' => 'ow_ic_vwvr'
        )
    );
    
    $e->setData($data);
}

OW::getEventManager()->bind('feed.on_entity_add', 'vwvr_feed_entity_add');


function vwvr_ads_enabled( BASE_EventCollector $event )
{
    $event->add('vwvr');
}

OW::getEventManager()->bind('ads.enabled_plugins', 'vwvr_ads_enabled');


$credits = new VWVR_CLASS_Credits();
OW::getEventManager()->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));


function vwvr_add_auth_labels( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(
        array(
            'vwvr' => array(
                'label' => $language->text('vwvr', 'auth_group_label'),
                'actions' => array(
                    'add' => $language->text('vwvr', 'auth_action_label_add'),
                    'view' => $language->text('vwvr', 'auth_action_label_view'),
                    'add_comment' => $language->text('vwvr', 'auth_action_label_add_comment'),
                    'delete_comment_by_content_owner' => $language->text('vwvr', 'auth_action_label_delete_comment_by_content_owner')
                )
            )
        )
    );
}

OW::getEventManager()->bind('admin.add_auth_labels', 'vwvr_add_auth_labels');


function vwvr_privacy_add_action( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();

    $action = array(
        'key' => 'vwvr_view_vwvr',
        'pluginKey' => 'vwvr',
        'label' => $language->text('vwvr', 'privacy_action_view_vwvr'),
        'description' => '',
        'defaultValue' => 'everybody'
    );

    $event->add($action);
}

OW::getEventManager()->bind('plugin.privacy.get_action_list', 'vwvr_privacy_add_action');


function vwvr_on_change_privacy( OW_Event $e )
{
    $params = $e->getParams();
    $userId = (int) $params['userId'];
    
    $actionList = $params['actionList'];
    
    if ( empty($actionList['vwvr_view_vwvr']) )
    {
        return;
    }
    
    VWVR_BOL_ClipService::getInstance()->updateUserClipsPrivacy($userId, $actionList['vwvr_view_vwvr']);
}

OW::getEventManager()->bind('plugin.privacy.on_change_action_privacy', 'vwvr_on_change_privacy');

function vwvr_feed_collect_configurable_activity( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(array(
        'label' => $language->text('vwvr', 'feed_content_label'),
        'activity' => '*:vwvr_comments'
    ));
}

OW::getEventManager()->bind('feed.collect_configurable_activity', 'vwvr_feed_collect_configurable_activity');

function vwvr_feed_collect_privacy( BASE_CLASS_EventCollector $event )
{
    $event->add(array('create:vwvr_comments', 'vwvr_view_vwvr'));
}

OW::getEventManager()->bind('feed.collect_privacy', 'vwvr_feed_collect_privacy');

function vwvr_add_admin_notification( BASE_CLASS_EventCollector $e )
{
    $language = OW::getLanguage();
    $configs = OW::getConfig()->getValues('vwvr');

    if ( ($configs['server'] == "rtmp://localhost/videowhisper") )
    {
        $e->add($language->text('vwvr', 'admin_configuration_required_notification', array( 'href' => OW::getRouter()->urlForRoute('vwvr_admin_config') )));
    }
}
OW::getEventManager()->bind('admin.add_admin_notification', 'vwvr_add_admin_notification');

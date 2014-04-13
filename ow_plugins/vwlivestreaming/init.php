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
$plugin = OW::getPluginManager()->getPlugin('vwls');
/**
$classesToAutoload = array(
    'VwlsProviders' => $plugin->getRootDir() . 'classes' . DS . 'vwls_providers.php'
);

OW::getAutoloader()->addClassArray($classesToAutoload);
*/
OW::getRouter()->addRoute(
    new OW_Route(
        'vwls_vwview_list_ls',
        'vwls/viewlist/:listType/',
        'VWLS_CTRL_Vwls',
        'viewList',
        array('listType' => array('default' => 'latest'))
    )
);

OW::getRouter()->addRoute(new OW_Route('vwls_list_index', 'vwls/', 'VWLS_CTRL_Vwls', 'viewList'));

OW::getRouter()->addRoute(new OW_Route('vwview_clip_ls', 'vwls/view/:id/', 'VWLS_CTRL_Vwls', 'view'));
OW::getRouter()->addRoute(new OW_Route('vwview_clip_ls_w', 'vwls/view_watch/:id/', 'VWLS_CTRL_Vwls', 'view'));
OW::getRouter()->addRoute(new OW_Route('vwview_clip_ls_v', 'vwls/view_video/:id/', 'VWLS_CTRL_Vwls', 'viewVideo'));
OW::getRouter()->addRoute(new OW_Route('vwedit_clip_ls', 'vwls/edit/:id/', 'VWLS_CTRL_Vwls', 'edit'));
OW::getRouter()->addRoute(new OW_Route('vwview_list_ls', 'vwls/viewlist/:listType/', 'VWLS_CTRL_Vwls', 'viewList'));
OW::getRouter()->addRoute(new OW_Route('vwview_online_ls', 'vwls/viewlist/online/', 'VWLS_CTRL_Vwls', 'viewOnlineList'));
OW::getRouter()->addRoute(new OW_Route('vwview_taggedlist_st_ls', 'vwls/viewlist/tagged/', 'VWLS_CTRL_Vwls', 'viewTaggedList'));
OW::getRouter()->addRoute(new OW_Route('vwview_tagged_list_ls', 'vwls/viewlist/tagged/:tag', 'VWLS_CTRL_Vwls', 'viewTaggedList'));
OW::getRouter()->addRoute(new OW_Route('vwls_user_vwls_list', 'vwls/user-vwls/:user', 'VWLS_CTRL_Vwls', 'viewUserVwlsList'));

OW::getRouter()->addRoute(new OW_Route('vwls_admin_config', 'admin/vwls/', 'VWLS_CTRL_Admin', 'index'));

OW::getThemeManager()->addDecorator('vwls_list_item', $plugin->getKey());
OW::getThemeManager()->addDecorator('vwls_online_list_item', $plugin->getKey());

function vwls_elst_add_new_content_item( BASE_CLASS_EventCollector $event )
{
    $resultArray = array(
        BASE_CMP_AddNewContent::DATA_KEY_ICON_CLASS => 'ow_ic_vwls',
        BASE_CMP_AddNewContent::DATA_KEY_URL => OW::getRouter()->urlFor('VWLS_CTRL_Add', 'index'),
        BASE_CMP_AddNewContent::DATA_KEY_LABEL => OW::getLanguage()->text('vwls', 'vwls')
    );

    $event->add($resultArray);
}

OW::getEventManager()->bind(BASE_CMP_AddNewContent::EVENT_NAME, 'vwls_elst_add_new_content_item');


function vwls_delete_user_content( OW_Event $event )
{
    $params = $event->getParams();

    if ( !isset($params['deleteContent']) || !(bool) $params['deleteContent'] )
    {
        return;
    }

    $userId = (int) $params['userId'];

    if ( $userId > 0 )
    {
        VWLS_BOL_ClipService::getInstance()->deleteUserClips($userId);
    }
}
OW::getEventManager()->bind(OW_EventManager::ON_USER_UNREGISTER, 'vwls_delete_user_content');


function vwls_on_notify_actions( BASE_CLASS_EventCollector $e )
{
    $e->add(array(
        'section' => 'vwls',
        'action' => 'vwls-add_comment',
        'description' => OW::getLanguage()->text('vwls', 'email_notifications_setting_comment'),
        'sectionIcon' => 'ow_ic_vwls',
        'sectionLabel' => OW::getLanguage()->text('vwls', 'email_notifications_section_label'),
        'selected' => true
    ));
}
OW::getEventManager()->bind('base.notify_actions', 'vwls_on_notify_actions');

function vwls_add_comment_notification( OW_Event $event )
{
    $params = $event->getParams();

    if ( empty($params['entityType']) || $params['entityType'] !== 'vwls_comments' )
    {
        return;
    }

    $entityId = $params['entityId'];
    $userId = $params['userId'];
    $commentId = $params['commentId'];

    $clipService = VWLS_BOL_ClipService::getInstance();
    $userService = BOL_UserService::getInstance();

    $clip = $clipService->findClipById($entityId);

    if ( $clip->userId != $userId )
    {
        $comment = BOL_CommentService::getInstance()->findComment($commentId);
        $url = OW::getRouter()->urlForRoute('vwview_clip_ls', array('id' => $entityId));

        $event = new OW_Event('base.notify', array(
                'plugin' => 'vwls',
                'pluginIcon' => 'ow_ic_vwls',
                'action' => 'vwls-add_comment',
                'userId' => $clip->userId,
                'string' => OW::getLanguage()->text('vwls', 'email_notifications_comment', array(
                    'userName' => $userService->getDisplayName($userId),
                    'userUrl' => $userService->getUserUrl($userId),
                    'vwlsUrl' => $url,
                    'vwlsTitle' => strip_tags($clip->title)
                )),
                'content' => $comment->getMessage(),
                'url' => $url
            ));

        OW::getEventManager()->trigger($event);
    }
}
OW::getEventManager()->bind('base_add_comment', 'vwls_add_comment_notification');

function vwls_feed_entity_add( OW_Event $e )
{
    $params = $e->getParams();
    $data = $e->getData();
    
    if ( $params['entityType'] != 'vwls_comments' )
    {
        return;
    }
    
    $vwlsService = VWLS_BOL_ClipService::getInstance();
    $clip = $vwlsService->findClipById($params['entityId']);
    $thumb = $vwlsService->getClipThumbUrl($clip->id);
    
    $url = OW::getRouter()->urlForRoute('vwview_clip_ls', array('id' => $clip->id));

    $content = UTIL_String::truncate(strip_tags($clip->description), 150, '...');
    $title = UTIL_String::truncate(strip_tags($clip->title), 100, '...');

    if ( $thumb == "undefined" )
    {
        $thumb = $vwlsService->getClipDefaultThumbUrl();
		
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
            'iconClass' => 'ow_ic_vwls'
        )
    );
    
    $e->setData($data);
}

OW::getEventManager()->bind('feed.on_entity_add', 'vwls_feed_entity_add');


function vwls_ads_enabled( BASE_EventCollector $event )
{
    $event->add('vwls');
}

OW::getEventManager()->bind('ads.enabled_plugins', 'vwls_ads_enabled');


$credits = new VWLS_CLASS_Credits();
OW::getEventManager()->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));


function vwls_add_auth_labels( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(
        array(
            'vwls' => array(
                'label' => $language->text('vwls', 'auth_group_label'),
                'actions' => array(
                    'add' => $language->text('vwls', 'auth_action_label_add'),
                    'view' => $language->text('vwls', 'auth_action_label_view'),
                    'add_comment' => $language->text('vwls', 'auth_action_label_add_comment'),
                    'delete_comment_by_content_owner' => $language->text('vwls', 'auth_action_label_delete_comment_by_content_owner')
                )
            )
        )
    );
}

OW::getEventManager()->bind('admin.add_auth_labels', 'vwls_add_auth_labels');


function vwls_privacy_add_action( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();

    $action = array(
        'key' => 'vwls_view_vwls',
        'pluginKey' => 'vwls',
        'label' => $language->text('vwls', 'privacy_action_view_vwls'),
        'description' => '',
        'defaultValue' => 'everybody'
    );

    $event->add($action);
}

OW::getEventManager()->bind('plugin.privacy.get_action_list', 'vwls_privacy_add_action');


function vwls_on_change_privacy( OW_Event $e )
{
    $params = $e->getParams();
    $userId = (int) $params['userId'];
    
    $actionList = $params['actionList'];
    
    if ( empty($actionList['vwls_view_vwls']) )
    {
        return;
    }
    
    VWLS_BOL_ClipService::getInstance()->updateUserClipsPrivacy($userId, $actionList['vwls_view_vwls']);
}

OW::getEventManager()->bind('plugin.privacy.on_change_action_privacy', 'vwls_on_change_privacy');

function vwls_feed_collect_configurable_activity( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(array(
        'label' => $language->text('vwls', 'feed_content_label'),
        'activity' => '*:vwls_comments'
    ));
}

OW::getEventManager()->bind('feed.collect_configurable_activity', 'vwls_feed_collect_configurable_activity');

function vwls_feed_collect_privacy( BASE_CLASS_EventCollector $event )
{
    $event->add(array('create:vwls_comments', 'vwls_view_vwls'));
}

OW::getEventManager()->bind('feed.collect_privacy', 'vwls_feed_collect_privacy');

function vwls_add_admin_notification( BASE_CLASS_EventCollector $e )
{
    $language = OW::getLanguage();
    $configs = OW::getConfig()->getValues('vwls');

    if ( $configs['server'] == "rtmp://localhost/videowhisper" )
    {
        $e->add($language->text('vwls', 'admin_configuration_required_notification', array( 'href' => OW::getRouter()->urlForRoute('vwls_admin_config') )));
    }
}
OW::getEventManager()->bind('admin.add_admin_notification', 'vwls_add_admin_notification');

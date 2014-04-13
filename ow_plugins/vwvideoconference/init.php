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
$plugin = OW::getPluginManager()->getPlugin('vwvc');
/**
$classesToAutoload = array(
    'VwvcProviders' => $plugin->getRootDir() . 'classes' . DS . 'vwvc_providers.php'
);

OW::getAutoloader()->addClassArray($classesToAutoload);
*/
OW::getRouter()->addRoute(
    new OW_Route(
        'vwvc_vwview_list',
        'vwvc/viewlist/:listType/',
        'VWVC_CTRL_Vwvc',
        'viewList',
        array('listType' => array('default' => 'latest'))
    )
);

OW::getRouter()->addRoute(new OW_Route('vwvc_list_index', 'vwvc/', 'VWVC_CTRL_Vwvc', 'viewList'));

OW::getRouter()->addRoute(new OW_Route('vwview_clip', 'vwvc/view/:id/', 'VWVC_CTRL_Vwvc', 'view'));
OW::getRouter()->addRoute(new OW_Route('vwedit_clip', 'vwvc/edit/:id/', 'VWVC_CTRL_Vwvc', 'edit'));
OW::getRouter()->addRoute(new OW_Route('vwview_list', 'vwvc/viewlist/:listType/', 'VWVC_CTRL_Vwvc', 'viewList'));
OW::getRouter()->addRoute(new OW_Route('vwview_online', 'vwvc/viewlist/online/', 'VWVC_CTRL_Vwvc', 'viewOnlineList'));
OW::getRouter()->addRoute(new OW_Route('vwview_taggedlist_st', 'vwvc/viewlist/tagged/', 'VWVC_CTRL_Vwvc', 'viewTaggedList'));
OW::getRouter()->addRoute(new OW_Route('vwview_tagged_list', 'vwvc/viewlist/tagged/:tag', 'VWVC_CTRL_Vwvc', 'viewTaggedList'));
OW::getRouter()->addRoute(new OW_Route('vwvc_user_vwvc_list', 'vwvc/user-vwvc/:user', 'VWVC_CTRL_Vwvc', 'viewUserVwvcList'));

OW::getRouter()->addRoute(new OW_Route('vwvc_admin_config', 'admin/vwvc/', 'VWVC_CTRL_Admin', 'index'));

OW::getThemeManager()->addDecorator('vwvc_list_item', $plugin->getKey());
OW::getThemeManager()->addDecorator('vwvc_online_list_item', $plugin->getKey());

function vwvc_elst_add_new_content_item( BASE_CLASS_EventCollector $event )
{
    $resultArray = array(
        BASE_CMP_AddNewContent::DATA_KEY_ICON_CLASS => 'ow_ic_vwvc',
        BASE_CMP_AddNewContent::DATA_KEY_URL => OW::getRouter()->urlFor('VWVC_CTRL_Add', 'index'),
        BASE_CMP_AddNewContent::DATA_KEY_LABEL => OW::getLanguage()->text('vwvc', 'vwvc')
    );

    $event->add($resultArray);
}

OW::getEventManager()->bind(BASE_CMP_AddNewContent::EVENT_NAME, 'vwvc_elst_add_new_content_item');


function vwvc_delete_user_content( OW_Event $event )
{
    $params = $event->getParams();

    if ( !isset($params['deleteContent']) || !(bool) $params['deleteContent'] )
    {
        return;
    }

    $userId = (int) $params['userId'];

    if ( $userId > 0 )
    {
        VWVC_BOL_ClipService::getInstance()->deleteUserClips($userId);
    }
}
OW::getEventManager()->bind(OW_EventManager::ON_USER_UNREGISTER, 'vwvc_delete_user_content');


function vwvc_on_notify_actions( BASE_CLASS_EventCollector $e )
{
    $e->add(array(
        'section' => 'vwvc',
        'action' => 'vwvc-add_comment',
        'description' => OW::getLanguage()->text('vwvc', 'email_notifications_setting_comment'),
        'sectionIcon' => 'ow_ic_vwvc',
        'sectionLabel' => OW::getLanguage()->text('vwvc', 'email_notifications_section_label'),
        'selected' => true
    ));
}
OW::getEventManager()->bind('base.notify_actions', 'vwvc_on_notify_actions');

function vwvc_add_comment_notification( OW_Event $event )
{
    $params = $event->getParams();

    if ( empty($params['entityType']) || $params['entityType'] !== 'vwvc_comments' )
    {
        return;
    }

    $entityId = $params['entityId'];
    $userId = $params['userId'];
    $commentId = $params['commentId'];

    $clipService = VWVC_BOL_ClipService::getInstance();
    $userService = BOL_UserService::getInstance();

    $clip = $clipService->findClipById($entityId);

    if ( $clip->userId != $userId )
    {
        $comment = BOL_CommentService::getInstance()->findComment($commentId);
        $url = OW::getRouter()->urlForRoute('vwview_clip', array('id' => $entityId));

        $event = new OW_Event('base.notify', array(
                'plugin' => 'vwvc',
                'pluginIcon' => 'ow_ic_vwvc',
                'action' => 'vwvc-add_comment',
                'userId' => $clip->userId,
                'string' => OW::getLanguage()->text('vwvc', 'email_notifications_comment', array(
                    'userName' => $userService->getDisplayName($userId),
                    'userUrl' => $userService->getUserUrl($userId),
                    'vwvcUrl' => $url,
                    'vwvcTitle' => strip_tags($clip->title)
                )),
                'content' => $comment->getMessage(),
                'url' => $url
            ));

        OW::getEventManager()->trigger($event);
    }
}
OW::getEventManager()->bind('base_add_comment', 'vwvc_add_comment_notification');

function vwvc_feed_entity_add( OW_Event $e )
{
    $params = $e->getParams();
    $data = $e->getData();
    
    if ( $params['entityType'] != 'vwvc_comments' )
    {
        return;
    }
    
    $vwvcService = VWVC_BOL_ClipService::getInstance();
    $clip = $vwvcService->findClipById($params['entityId']);
    $thumb = $vwvcService->getClipThumbUrl($clip->id);
    
    $url = OW::getRouter()->urlForRoute('vwview_clip', array('id' => $clip->id));

    $content = UTIL_String::truncate(strip_tags($clip->description), 150, '...');
    $title = UTIL_String::truncate(strip_tags($clip->title), 100, '...');

    if ( $thumb == "undefined" )
    {
        $thumb = $vwvcService->getClipDefaultThumbUrl();

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
            'iconClass' => 'ow_ic_vwvc'
        )
    );
    
    $e->setData($data);
}

OW::getEventManager()->bind('feed.on_entity_add', 'vwvc_feed_entity_add');


function vwvc_ads_enabled( BASE_EventCollector $event )
{
    $event->add('vwvc');
}

OW::getEventManager()->bind('ads.enabled_plugins', 'vwvc_ads_enabled');


$credits = new VWVC_CLASS_Credits();
OW::getEventManager()->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));


function vwvc_add_auth_labels( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(
        array(
            'vwvc' => array(
                'label' => $language->text('vwvc', 'auth_group_label'),
                'actions' => array(
                    'add' => $language->text('vwvc', 'auth_action_label_add'),
                    'view' => $language->text('vwvc', 'auth_action_label_view'),
                    'add_comment' => $language->text('vwvc', 'auth_action_label_add_comment'),
                    'delete_comment_by_content_owner' => $language->text('vwvc', 'auth_action_label_delete_comment_by_content_owner')
                )
            )
        )
    );
}

OW::getEventManager()->bind('admin.add_auth_labels', 'vwvc_add_auth_labels');


function vwvc_privacy_add_action( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();

    $action = array(
        'key' => 'vwvc_view_vwvc',
        'pluginKey' => 'vwvc',
        'label' => $language->text('vwvc', 'privacy_action_view_vwvc'),
        'description' => '',
        'defaultValue' => 'everybody'
    );

    $event->add($action);
}

OW::getEventManager()->bind('plugin.privacy.get_action_list', 'vwvc_privacy_add_action');


function vwvc_on_change_privacy( OW_Event $e )
{
    $params = $e->getParams();
    $userId = (int) $params['userId'];
    
    $actionList = $params['actionList'];
    
    if ( empty($actionList['vwvc_view_vwvc']) )
    {
        return;
    }
    
    VWVC_BOL_ClipService::getInstance()->updateUserClipsPrivacy($userId, $actionList['vwvc_view_vwvc']);
}

OW::getEventManager()->bind('plugin.privacy.on_change_action_privacy', 'vwvc_on_change_privacy');

function vwvc_feed_collect_configurable_activity( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(array(
        'label' => $language->text('vwvc', 'feed_content_label'),
        'activity' => '*:vwvc_comments'
    ));
}

OW::getEventManager()->bind('feed.collect_configurable_activity', 'vwvc_feed_collect_configurable_activity');

function vwvc_feed_collect_privacy( BASE_CLASS_EventCollector $event )
{
    $event->add(array('create:vwvc_comments', 'vwvc_view_vwvc'));
}

OW::getEventManager()->bind('feed.collect_privacy', 'vwvc_feed_collect_privacy');

function vwvc_add_admin_notification( BASE_CLASS_EventCollector $e )
{
    $language = OW::getLanguage();
    $configs = OW::getConfig()->getValues('vwvc');

    if ( $configs['server'] == "rtmp://localhost/videowhisper" )
    {
        $e->add($language->text('vwvc', 'admin_configuration_required_notification', array( 'href' => OW::getRouter()->urlForRoute('vwvc_admin_config') )));
    }
}
OW::getEventManager()->bind('admin.add_admin_notification', 'vwvc_add_admin_notification');

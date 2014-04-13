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

/**
 * Vwls action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow_plugins.vwls.controllers
 * @since 1.0
 */
class VWLS_CTRL_Vwls extends OW_ActionController
{
    /**
     * @var OW_Plugin
     */
    private $plugin;
    /**
     * @var string
     */
    private $pluginJsUrl;
    /**
     * @var string
     */
    private $ajaxResponder;
    /**
     * @var VWLS_BOL_ClipService
     */
    private $clipService;
    /**
     * @var BASE_CMP_ContentMenu
     */
    private $menu;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->plugin = OW::getPluginManager()->getPlugin('vwls');
        $this->pluginJsUrl = $this->plugin->getStaticJsUrl();
        $this->ajaxResponder = OW::getRouter()->urlFor('VWLS_CTRL_Vwls', 'ajaxResponder');

        $this->clipService = VWLS_BOL_ClipService::getInstance();

        $this->menu = $this->getMenu();

        if ( !OW::getRequest()->isAjax() )
        {
            OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'vwls', 'vwls');
        }
    }

    /**
     * Returns menu component
     *
     * @return BASE_CMP_ContentMenu
     */
    private function getMenu()
    {
        $validLists = array('online', 'latest', 'toprated', 'tagged');
        $classes = array('ow_ic_push_pin', 'ow_ic_clock', 'ow_ic_star', 'ow_ic_tag');

/**        if ( !VWLS_BOL_ClipService::getInstance()->findClipsCount('featured') )
        {
            array_shift($validLists);
            array_shift($classes);
        }
*/        
        $language = OW::getLanguage();

        $menuItems = array();

        $order = 0;
        foreach ( $validLists as $type )
        {
            $item = new BASE_MenuItem();
            $item->setLabel($language->text('vwls', 'menu_' . $type));
            $item->setUrl(OW::getRouter()->urlForRoute('vwview_list_ls', array('listType' => $type)));
            $item->setKey($type);
            $item->setIconClass($classes[$order]);
            $item->setOrder($order);

            array_push($menuItems, $item);

            $order++;
        }

        $menu = new BASE_CMP_ContentMenu($menuItems);

        return $menu;
    }

    /**
     * Vwls view action
     *
     * @param array $params
     */
    public function view( array $params )
    {
        if ( !isset($params['id']) || !($id = (int) $params['id']) )
        {
            throw new Redirect404Exception();
            return;
        }

        $clip = $this->clipService->findClipById($id);

        if ( !$clip )
        {
            throw new Redirect404Exception();
        }

        $contentOwner = (int) $this->clipService->findClipOwner($id);

        $language = OW_Language::getInstance();

        $description = $clip->description;
        $clip->description = UTIL_HtmlTag::autoLink($clip->description);
        $this->assign('clip', $clip);
//        $is_featured = VWLS_BOL_ClipFeaturedService::getInstance()->isFeatured($clip->id);
//        $this->assign('featured', $is_featured);

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwls');
        $this->assign('moderatorMode', $modPermissions);

        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        if ( !$ownerMode && !OW::getUser()->isAuthorized('vwls', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

//        $this->assign('auth_msg', null);
        
        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'vwls_view_vwls', 'ownerId' => $contentOwner, 'viewerId' => $userId);
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $cmtParams = new BASE_CommentsParams('vwls', 'vwls_comments');
        $cmtParams->setEntityId($id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);

        $vwlsCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $vwlsCmts);

        $vwlsRates = new BASE_CMP_Rate('vwls', 'vwls_rates', $id, $contentOwner);
        $this->addComponent('rate', $vwlsRates);

        $vwlsTags = new BASE_CMP_EntityTagCloud('vwls');
        $vwlsTags->setEntityId($id);
        $vwlsTags->setRouteName('vwview_tagged_list_ls');
        $this->addComponent('tags', $vwlsTags);

        $username = BOL_UserService::getInstance()->getUserName($clip->userId);
        $this->assign('username', $username);

        $displayName = BOL_UserService::getInstance()->getDisplayName($clip->userId);
        $this->assign('displayName', $displayName);

        OW::getDocument()->addScript($this->pluginJsUrl . 'vwls.js');

        $objParams = array(
            'ajaxResponder' => $this->ajaxResponder,
            'clipId' => $id,
            'txtDelConfirm' => OW::getLanguage()->text('vwls', 'confirm_delete'),
            'txtMarkFeatured' => OW::getLanguage()->text('vwls', 'mark_featured'),
            'txtRemoveFromFeatured' => OW::getLanguage()->text('vwls', 'remove_from_featured'),
            'txtApprove' => OW::getLanguage()->text('base', 'approve'),
            'txtDisapprove' => OW::getLanguage()->text('base', 'disapprove')
        );

        $script =
            "$(document).ready(function(){
                var clip = new vwlsClip( " . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        OW::getDocument()->setHeading($clip->title);
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');

        $toolbar = array();

        array_push($toolbar, array(
            'href' => 'javascript://',
            'id' => 'btn-vwls-flag',
            'label' => $language->text('base', 'flag')
        ));

        if ( $ownerMode || $modPermissions )
        {
            array_push($toolbar, array(
                'href' => OW::getRouter()->urlForRoute('vwedit_clip_ls', array('id' => $clip->id)),
                'label' => $language->text('base', 'edit')
            ));

            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'clip-delete',
                'label' => $language->text('base', 'delete')
            ));
        }
/**
        if ( $modPermissions )
        {
            if ( $is_featured )
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'remove_from_featured',
                    'label' => $language->text('vwls', 'remove_from_featured')
                ));
            }
            else
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'mark_featured',
                    'label' => $language->text('vwls', 'mark_featured')
                ));
            }
            
            if ( $clip->status == 'approved' )
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-set-approval-staus',
                    'rel' => 'disapprove',
                    'label' => $language->text('base', 'disapprove')
                ));
            }
            else
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-set-approval-staus',
                    'rel' => 'approve',
                    'label' => $language->text('base', 'approve')
                ));
            }

        }
*/
        $this->assign('toolbar', $toolbar);

        $js = UTIL_JsGenerator::newInstance()
                ->jQueryEvent('#btn-vwls-flag', 'click', 'document.flag(e.data.entity, e.data.id, e.data.title, e.data.href, "vwls+flags");', array('e'),
                    array('entity' => 'vwls_clip', 'id' => $clip->id, 'title' => $clip->title, 'href' => OW::getRouter()->urlForRoute('vwview_clip_ls', array('id' => $clip->id))
                ));

        OW::getDocument()->addOnloadScript($js, 1001);
        
        OW::getDocument()->setTitle($language->text('vwls', 'meta_title_vwls_view', array('title' => $clip->title)));
        $tagsArr = BOL_TagService::getInstance()->findEntityTags($clip->id, 'vwls');
    
        foreach ( $tagsArr as $t )
        {
            $labels[] = $t->label;
        }
        $tagStr = $tagsArr ? implode(', ', $labels) : '';
        OW::getDocument()->setDescription($language->text('vwls', 'meta_description_vwls_view', array('title' => $clip->title, 'tags' => $tagStr)));
    }
    
    /**
     * Vwls view_video action
     *
     * @param array $params
     */
    public function viewVideo( array $params )
    {
        if ( !isset($params['id']) || !($id = (int) $params['id']) )
        {
            throw new Redirect404Exception();
            return;
        }

        $clip = $this->clipService->findClipById($id);

        if ( !$clip )
        {
            throw new Redirect404Exception();
        }

        $contentOwner = (int) $this->clipService->findClipOwner($id);

        $language = OW_Language::getInstance();

        $description = $clip->description;
        $clip->description = UTIL_HtmlTag::autoLink($clip->description);
        $this->assign('clip', $clip);
//        $is_featured = VWLS_BOL_ClipFeaturedService::getInstance()->isFeatured($clip->id);
//        $this->assign('featured', $is_featured);

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwls');
        $this->assign('moderatorMode', $modPermissions);

        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        if ( !$ownerMode && !OW::getUser()->isAuthorized('vwls', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

//        $this->assign('auth_msg', null);
        
        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'vwls_view_vwls', 'ownerId' => $contentOwner, 'viewerId' => $userId);
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $cmtParams = new BASE_CommentsParams('vwls', 'vwls_comments');
        $cmtParams->setEntityId($id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);

        $vwlsCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $vwlsCmts);

        $vwlsRates = new BASE_CMP_Rate('vwls', 'vwls_rates', $id, $contentOwner);
        $this->addComponent('rate', $vwlsRates);

        $vwlsTags = new BASE_CMP_EntityTagCloud('vwls');
        $vwlsTags->setEntityId($id);
        $vwlsTags->setRouteName('vwview_tagged_list_ls');
        $this->addComponent('tags', $vwlsTags);

        $username = BOL_UserService::getInstance()->getUserName($clip->userId);
        $this->assign('username', $username);

        $displayName = BOL_UserService::getInstance()->getDisplayName($clip->userId);
        $this->assign('displayName', $displayName);

        OW::getDocument()->addScript($this->pluginJsUrl . 'vwls.js');

        $objParams = array(
            'ajaxResponder' => $this->ajaxResponder,
            'clipId' => $id,
            'txtDelConfirm' => OW::getLanguage()->text('vwls', 'confirm_delete'),
            'txtMarkFeatured' => OW::getLanguage()->text('vwls', 'mark_featured'),
            'txtRemoveFromFeatured' => OW::getLanguage()->text('vwls', 'remove_from_featured'),
            'txtApprove' => OW::getLanguage()->text('base', 'approve'),
            'txtDisapprove' => OW::getLanguage()->text('base', 'disapprove')
        );

        $script =
            "$(document).ready(function(){
                var clip = new vwlsClip( " . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        OW::getDocument()->setHeading($clip->title);
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');

        $toolbar = array();

        array_push($toolbar, array(
            'href' => 'javascript://',
            'id' => 'btn-vwls-flag',
            'label' => $language->text('base', 'flag')
        ));

        if ( $ownerMode || $modPermissions )
        {
            array_push($toolbar, array(
                'href' => OW::getRouter()->urlForRoute('vwedit_clip_ls', array('id' => $clip->id)),
                'label' => $language->text('base', 'edit')
            ));

            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'clip-delete',
                'label' => $language->text('base', 'delete')
            ));
        }
/**
        if ( $modPermissions )
        {
            if ( $is_featured )
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'remove_from_featured',
                    'label' => $language->text('vwls', 'remove_from_featured')
                ));
            }
            else
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'mark_featured',
                    'label' => $language->text('vwls', 'mark_featured')
                ));
            }
            
            if ( $clip->status == 'approved' )
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-set-approval-staus',
                    'rel' => 'disapprove',
                    'label' => $language->text('base', 'disapprove')
                ));
            }
            else
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-set-approval-staus',
                    'rel' => 'approve',
                    'label' => $language->text('base', 'approve')
                ));
            }

        }
*/
        $this->assign('toolbar', $toolbar);

        $js = UTIL_JsGenerator::newInstance()
                ->jQueryEvent('#btn-vwls-flag', 'click', 'document.flag(e.data.entity, e.data.id, e.data.title, e.data.href, "vwls+flags");', array('e'),
                    array('entity' => 'vwls_clip', 'id' => $clip->id, 'title' => $clip->title, 'href' => OW::getRouter()->urlForRoute('vwview_clip_ls', array('id' => $clip->id))
                ));

        OW::getDocument()->addOnloadScript($js, 1001);
        
        OW::getDocument()->setTitle($language->text('vwls', 'meta_title_vwls_view', array('title' => $clip->title)));
        $tagsArr = BOL_TagService::getInstance()->findEntityTags($clip->id, 'vwls');
    
        foreach ( $tagsArr as $t )
        {
            $labels[] = $t->label;
        }
        $tagStr = $tagsArr ? implode(', ', $labels) : '';
        OW::getDocument()->setDescription($language->text('vwls', 'meta_description_vwls_view', array('title' => $clip->title, 'tags' => $tagStr)));
    }

    public function edit( array $params )
    {
        if ( !isset($params['id']) || !($id = (int) $params['id']) )
        {
            throw new Redirect404Exception();
            return;
        }

        $clip = $this->clipService->findClipById($id);

        if ( !$clip )
        {
            throw new Redirect404Exception();
        }

        $language = OW_Language::getInstance();
        
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwls');
        $this->assign('moderatorMode', $modPermissions);

        $contentOwner = (int) $this->clipService->findClipOwner($id);
        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        if ( !$ownerMode && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
        
        $videoEditForm = new vwlsEditForm($clip->id);
        $this->addForm($videoEditForm);

        $videoEditForm->getElement('id')->setValue($clip->id);
        $videoEditForm->getElement('room_name')->setValue($clip->title);
        $videoEditForm->getElement('description')->setValue($clip->description);
        $videoEditForm->getElement('room_limit')->setValue($clip->roomLimit);
        $videoEditForm->getElement('user_list')->setValue($clip->user_list);
        $videoEditForm->getElement('moderator_list')->setValue($clip->moderator_list);

        $videoEditForm->getElement('welcome')->setValue($clip->welcome);
        $resolution = $clip->camWidth."x".$clip->camHeight;
        $videoEditForm->getElement('resolution')->setValue($resolution);
        $videoEditForm->getElement('camera_fps')->setValue($clip->camFPS);
        $videoEditForm->getElement('microphone_rate')->setValue($clip->micRate);
        $videoEditForm->getElement('soundQuality')->setValue($clip->soundQuality);
        $videoEditForm->getElement('bandwidth')->setValue($clip->camBandwidth);
        $videoEditForm->getElement('flood_protection')->setValue($clip->floodProtection);
        $videoEditForm->getElement('label_color')->setValue($clip->labelColor);

        $videoEditForm->getElement('welcome2')->setValue($clip->welcome2);
        $videoEditForm->getElement('offline_message')->setValue($clip->offlineMessage);
        $videoEditForm->getElement('flood_protection2')->setValue($clip->floodProtection2);
        $videoEditForm->getElement('filter_regex')->setValue($clip->filterRegex);
        $videoEditForm->getElement('filter_replace')->setValue($clip->filterReplace);
        $videoEditForm->getElement('layout_code')->setValue($clip->layoutCode);

        $permissions = $clip->permission;
        $permission = explode("|", $permissions);
        $videoEditForm->getElement('show_camera_settings')->setValue($permission [0]);
        $videoEditForm->getElement('advanced_camera_settings')->setValue($permission [1]);
        $videoEditForm->getElement('configure_source')->setValue($permission [2]);
        $videoEditForm->getElement('only_video')->setValue($permission [3]);
        $videoEditForm->getElement('no_video')->setValue($permission [4]);
        $videoEditForm->getElement('no_embeds')->setValue($permission [5]);
        $videoEditForm->getElement('show_timer')->setValue($permission [6]);
        $videoEditForm->getElement('write_text')->setValue($permission [7]);
        $videoEditForm->getElement('private_textchat')->setValue($permission [8]);
        $videoEditForm->getElement('fill_window')->setValue($permission [9]);
        $videoEditForm->getElement('write_text2')->setValue($permission [10]);
        $videoEditForm->getElement('enable_video')->setValue($permission [11]);
        $videoEditForm->getElement('enable_chat')->setValue($permission [12]);
        $videoEditForm->getElement('enable_users')->setValue($permission [13]);
        $videoEditForm->getElement('fill_window2')->setValue($permission [14]);
        $videoEditForm->getElement('verbose_level')->setValue($permission [15]);

        if ( OW::getRequest()->isPost() && $videoEditForm->isValid($_POST) )
        {
            $res = $videoEditForm->process();
            OW::getFeedback()->info($language->text('vwls', 'clip_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('vwview_clip_ls', array('id' => $res['id'])));
        }
        
        OW::getDocument()->setHeading($language->text('vwls', 'tb_vwedit_clip_ls'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');
        OW::getDocument()->setTitle($language->text('vwls', 'tb_vwedit_clip_ls'));
    }

    /**
     * Vwls list view action
     *
     * @param array $params
     */
    public function viewList( array $params )
    {
        $listType = isset($params['listType']) ? trim($params['listType']) : 'latest';

        $validLists = array('online', 'latest', 'toprated', 'tagged');

        if ( !in_array($listType, $validLists) )
        {
            $this->redirect(OW::getRouter()->urlForRoute('view_photo_list', array('listType' => 'latest')));
        }
        
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwls');

        if ( !OW::getUser()->isAuthorized('vwls', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $this->addComponent('vwlsMenu', $this->menu);

        $el = $this->menu->getElement($listType);
        if ( $el )
        {
            $el->setActive(true);
        }

        $this->assign('listType', $listType);

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwls', 'page_title_browse_vwls'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwls', 'meta_title_vwls_'.$listType));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwls', 'meta_description_vwls_'.$listType));

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWLS_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwls', 'click', 'document.location.href = addNewUrl');

        OW::getDocument()->addOnloadScript($js);
    }

    /**
     * User vwls list view action
     *
     * @param array $params
     */
    public function viewUserVwlsList( array $params )
    {
        if ( !isset($params['user']) || !strlen($userName = trim($params['user'])) )
        {
            throw new Redirect404Exception();
            return;
        }

        $user = BOL_UserService::getInstance()->findByUsername($userName);
        if ( !$user )
        {
            throw new Redirect404Exception();
            return;
        }

        $ownerMode = $user->id == OW::getUser()->getId();
        
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwls');

        if ( !OW::getUser()->isAuthorized('vwls', 'view') && !$modPermissions && !$ownerMode )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
        
        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'vwls_view_vwls', 'ownerId' => $user->id, 'viewerId' => OW::getUser()->getId());
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $this->assign('userId', $user->id);

        $clipCount = VWLS_BOL_ClipService::getInstance()->findUserClipsCount($user->id);
        $this->assign('total', $clipCount);

        $displayName = BOL_UserService::getInstance()->getDisplayName($user->id);
        $this->assign('userName', $displayName);

        $heading = OW::getLanguage()->text('vwls', 'page_title_vwls_by', array('user' => $displayName));

        OW::getDocument()->setHeading($heading);
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwls', 'meta_title_user_vwls', array('displayName' => $displayName)));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwls', 'meta_description_user_vwls', array('displayName' => $displayName)));
    }


    /**
     * Onilne vwls list view action
     *
     * @param array $params
     */
    public function viewOnlineList( array $params = null )
    {
        $listType = 'online';

        $validLists = array('online', 'latest', 'toprated', 'tagged');
        
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwls');

        if ( !OW::getUser()->isAuthorized('vwls', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $this->addComponent('vwlsMenu', $this->menu);

        $el = $this->menu->getElement($listType);
        if ( $el )
        {
            $el->setActive(true);
        }

        $this->assign('listType', $listType);

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwls', 'page_title_browse_vwls'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwls', 'meta_title_vwls_'.$listType));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwls', 'meta_description_vwls_'.$listType));

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWLS_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwls', 'click', 'document.location.href = addNewUrl');

        OW::getDocument()->addOnloadScript($js);
    }



    /**
     * Tagged vwls list view action
     *
     * @param array $params
     */
    public function viewTaggedList( array $params = null )
    {
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwls');

        if ( !OW::getUser()->isAuthorized('vwls', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
        
        $tag = !empty($params['tag']) ? trim(htmlspecialchars(urldecode($params['tag']))) : '';

        $this->addComponent('vwlsMenu', $this->menu);

        $this->menu->getElement('tagged')->setActive(true);

        $this->setTemplate(OW::getPluginManager()->getPlugin('vwls')->getCtrlViewDir() . 'vwls_view_list-tagged.html');

        $listUrl = OW::getRouter()->urlForRoute('vwview_taggedlist_st_ls');

        OW::getDocument()->addScript($this->pluginJsUrl . 'vwls_tag_search.js');

        $objParams = array(
            'listUrl' => $listUrl
        );

        $script =
            "$(document).ready(function(){
                var vwlsSearch = new vwlsTagSearch(" . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        if ( strlen($tag) )
        {
            $this->assign('tag', $tag);
            
            OW::getDocument()->setTitle(OW::getLanguage()->text('vwls', 'meta_title_vwls_tagged_as', array('tag' => $tag)));
            OW::getDocument()->setDescription(OW::getLanguage()->text('vwls', 'meta_description_vwls_tagged_as', array('tag' => $tag)));
        }
        else
        {
            $tags = new BASE_CMP_EntityTagCloud('vwls');
            $tags->setRouteName('vwview_tagged_list_ls');
            $this->addComponent('tags', $tags);
            
            OW::getDocument()->setTitle(OW::getLanguage()->text('vwls', 'meta_title_vwls_tagged'));
            $tagsArr = BOL_TagService::getInstance()->findMostPopularTags('vwls', 20);
    
            foreach ( $tagsArr as $t )
            {
                $labels[] = $t['label'];
            }
            $tagStr = $tagsArr ? implode(', ', $labels) : '';
            OW::getDocument()->setDescription(OW::getLanguage()->text('vwls', 'meta_description_vwls_tagged', array('topTags' => $tagStr)));
        }

        $this->assign('listType', 'tagged');

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwls', 'page_title_browse_vwls'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwls');

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWLS_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwls', 'click', 'document.location.href = addNewUrl');

        OW::getDocument()->addOnloadScript($js);
    }

    /**
     * Method acts as ajax responder. Calls methods using ajax
     * 
     * @return JSON encoded string
     *
     */
    public function ajaxResponder()
    {
        if ( isset($_POST['ajaxFunc']) && OW::getRequest()->isAjax() )
        {
            $callFunc = (string) $_POST['ajaxFunc'];

            $result = call_user_func(array($this, $callFunc), $_POST);
        }
        else
        {
            throw new Redirect404Exception();
            exit;
        }

        echo json_encode($result);
    }

    /**
     * Set vwls clip approval status (approved | blocked)
     *
     * @param array $params
     * @return array
     */
    public function ajaxSetApprovalStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('vwls');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

        $setStatus = $this->clipService->updateClipStatus($clipId, $status);

        if ( $setStatus )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('vwls', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('vwls', 'status_not_changed'));
        }

        return $return;
    }

    /**
     * Deletes vwls clip
     *
     * @param array $params
     * @return array
     */
    public function ajaxDeleteClip( $params )
    {
        $clipId = $params['clipId'];

        $ownerId = $this->clipService->findClipOwner($clipId);
        $isOwner = OW::getUser()->isAuthorized('vwls', 'add', $ownerId);
        $isModerator = OW::getUser()->isAuthorized('vwls');

        if ( !$isOwner && !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

        $delResult = $this->clipService->deleteClip($clipId);

        if ( $delResult )
        {
            $return = array(
                'result' => true,
                'msg' => OW::getLanguage()->text('vwls', 'clip_deleted'),
                'url' => OW_Router::getInstance()->urlForRoute('vwls_vwview_list_ls')
            );
        }
        else
        {
            $return = array(
                'result' => false,
                'error' => OW::getLanguage()->text('vwls', 'clip_not_deleted')
            );
        }

        return $return;
    }

    /**
     * Set 'is featured' status to vwls clip 
     *
     * @param array $params
     * @return array
     */
    public function ajaxSetFeaturedStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('vwls');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

//        $setResult = $this->clipService->updateClipFeaturedStatus($clipId, $status);

        if ( $setResult )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('vwls', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('vwls', 'status_not_changed'));
        }

        return $return;
    }
}

/**
 * Vwls edit form class
 */
class vwlsEditForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct( $clipId )
    {
        parent::__construct('vwlsEditForm');

        $language = OW::getLanguage();

        // clip id field
        $clipIdField = new HiddenField('id');
        $clipIdField->setRequired(true);
        $this->addElement($clipIdField);

        // select box for broadcasting
        $arr1 = array(
          '1' => 'yes',
          '0' => 'no'
        );
        $arr0 = array(
          '0' => 'no',
          '1' => 'yes'
        );

        // select box for permission
        $permArr0 = array(
          '1' => 'moderators',
          '3' => 'none',
          '2' => 'owner',
          '0' => 'all'
        );
        $permArr1 = array(
          '0' => 'all',
          '3' => 'none',
          '2' => 'owner',
          '1' => 'moderators'
        );
        $permArr2 = array(
          '3' => 'none',
          '2' => 'owner',
          '1' => 'moderators',
          '0' => 'all'
        );

        // room_name Field
        $generated =  base_convert((time()-1224000000).rand(0,10),10,36);
        $room_nameField = new TextField('room_name');
        $sValidator = new StringValidator(1, 22);
        $room_nameField->addValidator($sValidator);
        $room_nameField->setRequired(true);
        $room_nameField->setValue($generated);

        $this->addElement($room_nameField->setLabel($language->text('vwls', 'room_name')));

        // Description Field
        $descriptionField = new Textarea('description');
        $this->addElement($descriptionField->setLabel($language->text('vwls', 'description')));

        // Room limit Field
        $room_limitField = new TextField('room_limit');
        $room_limitField->setRequired(false);
        $room_limitField->setValue(0);
        $this->addElement($room_limitField->setLabel($language->text('vwls', 'room_limit')));

        // Show Camera Settings Field
        $show_camera_settingsField = new Selectbox('show_camera_settings');
        $show_camera_settingsField->addOptions($arr1);
        $show_camera_settingsField->setRequired();
        $show_camera_settingsField->setHasInvitation(false);
        $this->addElement($show_camera_settingsField->setLabel($language->text('vwls', 'show_camera_settings')));

        // Advanced Camera Settings Field
        $advanced_camera_settingsField = new Selectbox('advanced_camera_settings');
        $advanced_camera_settingsField->addOptions($arr1);
        $advanced_camera_settingsField->setRequired();
        $advanced_camera_settingsField->setHasInvitation(false);
        $this->addElement($advanced_camera_settingsField->setLabel($language->text('vwls', 'advanced_camera_settings')));

        // Configure Source Field
        $configure_sourceField = new Selectbox('configure_source');
        $configure_sourceField->addOptions($arr1);
        $configure_sourceField->setRequired();
        $configure_sourceField->setHasInvitation(false);
        $this->addElement($configure_sourceField->setLabel($language->text('vwls', 'configure_source')));

        // user_list Field
        $user_listField = new Textarea('user_list');
        $this->addElement($user_listField->setLabel($language->text('vwls', 'user_list')));

        // moderator_list Field
        $moderator_listField = new Textarea('moderator_list');
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById(OW::getUser()->getId());
        $username = $user->getUsername();
        $moderator_listField->setValue($username);
        $this->addElement($moderator_listField->setLabel($language->text('vwls', 'moderator_list')));

        // administrator Field
/**        $administratorField = new Selectbox('administrator');
        $administratorField->addOptions($permArr0);
        $administratorField->setRequired();
        $administratorField->setHasInvitation(false);
        $this->addElement($administratorField->setLabel($language->text('vwls', 'administrator')));
*/
        // clean_up Field
        $clean_upField = new TextField('clean_up');
        $clean_upField->setValue(0);
        $this->addElement($clean_upField->setLabel($language->text('vwls', 'clean_up')));


        // Broadcasting
        // welcome Field
        $welcomeField = new Textarea('welcome');
        $welcomeField->setValue($language->text('vwls', 'welcome_default'));
        $this->addElement($welcomeField->setLabel($language->text('vwls', 'welcome')));

        // Only video Field
        $only_videoField = new Selectbox('only_video');
        $only_videoField->addOptions($arr0);
        $only_videoField->setRequired();
        $only_videoField->setHasInvitation(false);
        $this->addElement($only_videoField->setLabel($language->text('vwls', 'only_video')));

        // No Video Field
        $no_videoField = new Selectbox('no_video');
        $no_videoField->addOptions($arr0);
        $no_videoField->setRequired();
        $no_videoField->setHasInvitation(false);
        $this->addElement($no_videoField->setLabel($language->text('vwls', 'no_video')));

        // No Embeds Field
        $no_embedsField = new Selectbox('no_embeds');
        $no_embedsField->addOptions($arr0);
        $no_embedsField->setRequired();
        $no_embedsField->setHasInvitation(false);
        $this->addElement($no_embedsField->setLabel($language->text('vwls', 'no_embeds')));

        // Show Timer Field
        $show_timerField = new Selectbox('show_timer');
        $show_timerField->addOptions($arr1);
        $show_timerField->setRequired();
        $show_timerField->setHasInvitation(false);
        $this->addElement($show_timerField->setLabel($language->text('vwls', 'show_timer')));

        // writeText Field
        $write_textField = new Selectbox('write_text');
        $write_textField->addOptions($arr1);
        $write_textField->setRequired();
        $write_textField->setHasInvitation(false);
        $this->addElement($write_textField->setLabel($language->text('vwls', 'write_text')));

        // resolution Field
        $resolutionArr = array(
          '320x240' => '320x240',
          '160x120' => '160x120',
          '176x144' => '176x144',
          '352x288' => '352x288',
          '640x480' => '640x480'
        );
        $resolutionField = new Selectbox('resolution');
        $resolutionField->addOptions($resolutionArr);
        $resolutionField->setRequired();
        $resolutionField->setHasInvitation(false);
        $this->addElement($resolutionField->setLabel($language->text('vwls', 'resolution')));

        // camera_fps Field
        $camera_fpsArr = array(
          '10' => '10',
          '12' => '12',
          '20' => '20',
          '25' => '25',
          '30' => '30'
        );
        $camera_fpsField = new Selectbox('camera_fps');
        $camera_fpsField->addOptions($camera_fpsArr);
        $camera_fpsField->setRequired();
        $camera_fpsField->setHasInvitation(false);
        $this->addElement($camera_fpsField->setLabel($language->text('vwls', 'camera_fps')));

        // Microphone Rate Field
        $microphone_rateArr = array(
          '11' => '11',
          '22' => '22',
          '44' => '44',
          '48' => '48'
        );
        $microphone_rateField = new Selectbox('microphone_rate');
        $microphone_rateField->addOptions($microphone_rateArr);
        $microphone_rateField->setRequired();
        $microphone_rateField->setHasInvitation(false);
        $this->addElement($microphone_rateField->setLabel($language->text('vwls', 'microphone_rate')));

        // soundQuality Field
        $soundQualityField = new TextField('soundQuality');
        $soundQualityField->setRequired(true);
        $this->addElement($soundQualityField->setLabel($language->text('vwls', 'soundQuality')));

        // Bandwidth Field
        $bandwidthField = new TextField('bandwidth');
        $bandwidthField->setRequired(true);
        $bandwidthField->setValue(40960);
        $this->addElement($bandwidthField->setLabel($language->text('vwls', 'bandwidth')));

        // FloodProtection Field
        $flood_protectionField = new TextField('flood_protection');
        $flood_protectionField->setValue(3);
        $this->addElement($flood_protectionField->setLabel($language->text('vwls', 'flood_protection')));

        // verbose_level Field
        $verbose_levelArr = array(
          '2' => 'warning/recoverable failure',
          '0' => 'nothing',
          '1' => 'failure',
          '3' => 'success',
          '4' => 'action'
        );
        $verbose_levelField = new Selectbox('verbose_level');
        $verbose_levelField->addOptions($verbose_levelArr);
        $verbose_levelField->setRequired();
        $verbose_levelField->setHasInvitation(false);
        $this->addElement($verbose_levelField->setLabel($language->text('vwls', 'verbose_level')));

        // Label Color Field
        $label_colorField = new TextField('label_color');
        $label_colorField->setValue('FFFFFF');
        $this->addElement($label_colorField->setLabel($language->text('vwls', 'label_color')));

        // privateTextchat Field
        $private_textchatField = new Selectbox('private_textchat');
        $private_textchatField->addOptions($arr1);
        $private_textchatField->setRequired();
        $private_textchatField->setHasInvitation(false);
        $this->addElement($private_textchatField->setLabel($language->text('vwls', 'private_textchat')));

        // Layout Code Field
        $layout_codeField = new Textarea('layout_code');
        $this->addElement($layout_codeField->setLabel($language->text('vwls', 'layout_code')));

        // Fill window Field
        $fill_windowField = new Selectbox('fill_window');
        $fill_windowField->addOptions($arr1);
        $fill_windowField->setRequired();
        $fill_windowField->setHasInvitation(false);
        $this->addElement($fill_windowField->setLabel($language->text('vwls', 'fill_window')));


        // Video / Watch
        // welcome Field
        $welcome2Field = new Textarea('welcome2');
        $welcome2Field->setValue($language->text('vwls', 'welcome_default2'));
        $this->addElement($welcome2Field->setLabel($language->text('vwls', 'welcome2')));

        // Offline message Field
        $offline_messageField = new Textarea('offline_message');
        $this->addElement($offline_messageField->setLabel($language->text('vwls', 'offline_message')));

        // FloodProtection2 Field
        $flood_protection2Field = new TextField('flood_protection2');
        $flood_protection2Field->setValue(3);
        $this->addElement($flood_protection2Field->setLabel($language->text('vwls', 'flood_protection2')));

        // Filter regex Field
        $filter_regexField = new TextField('filter_regex');
        $filter_regexField->setValue('(?i)(fuck|cunt)(?-i)');
        $this->addElement($filter_regexField->setLabel($language->text('vwls', 'filter_regex')));

        // Filter replace Field
        $filter_replaceField = new TextField('filter_replace');
        $filter_replaceField->setValue('**');
        $this->addElement($filter_replaceField->setLabel($language->text('vwls', 'filter_replace')));

        // Layout Code2 Field
        $layout_code2Field = new Textarea('layout_code2');
        $this->addElement($layout_code2Field->setLabel($language->text('vwls', 'layout_code2')));

        // Fill window2 Field
        $fill_window2Field = new Selectbox('fill_window2');
        $fill_window2Field->addOptions($permArr0);
        $fill_window2Field->setRequired();
        $fill_window2Field->setHasInvitation(false);
        $this->addElement($fill_window2Field->setLabel($language->text('vwls', 'fill_window2')));

        // writeText2 Field
        $write_text2Field = new Selectbox('write_text2');
        $write_text2Field->addOptions($permArr1);
        $write_text2Field->setRequired();
        $write_text2Field->setHasInvitation(false);
        $this->addElement($write_text2Field->setLabel($language->text('vwls', 'write_text2')));

        // Enable Video Field
        $enable_videoField = new Selectbox('enable_video');
        $enable_videoField->addOptions($permArr1);
        $enable_videoField->setRequired();
        $enable_videoField->setHasInvitation(false);
        $this->addElement($enable_videoField->setLabel($language->text('vwls', 'enable_video')));

        // Enable chat Field
        $enable_chatField = new Selectbox('enable_chat');
        $enable_chatField->addOptions($permArr1);
        $enable_chatField->setRequired();
        $enable_chatField->setHasInvitation(false);
        $this->addElement($enable_chatField->setLabel($language->text('vwls', 'enable_chat')));

        // Enable users Field
        $enable_usersField = new Selectbox('enable_users');
        $enable_usersField->addOptions($permArr1);
        $enable_usersField->setRequired();
        $enable_usersField->setHasInvitation(false);
        $this->addElement($enable_usersField->setLabel($language->text('vwls', 'enable_users')));
        
        $entityTags = BOL_TagService::getInstance()->findEntityTags($clipId, 'vwls');

        if ( $entityTags )
        {
            $tags = array();
            foreach ( $entityTags as $entityTag )
            {
                $tags[] = $entityTag->label;
            }

            $tagsField = new TagsField('tags', $tags);
        }
        else
        {
            $tagsField = new TagsField('tags');
        }

        $this->addElement($tagsField->setLabel($language->text('vwls', 'tags')));

        $submit = new Submit('edit');
        $submit->setValue($language->text('vwls', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates vwls clip
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $clipService = VWLS_BOL_ClipService::getInstance();
        $language = OW::getLanguage();

        if ( $values['id'] )
        {
            $clip = $clipService->findClipById($values['id']);

            if ( $clip )
            {
         $clip->title = htmlspecialchars($values['room_name']);
        $clip->roomLimit = $values['room_limit'];
        $clip->user_list = $values['user_list'];
        $clip->moderator_list = $values['moderator_list'];

        $clip->welcome = htmlspecialchars($values['welcome']);
        $cam = $values['resolution'];
        $camArr = explode("x", $cam); 
        $clip->camWidth = $camArr[0];
        $clip->camHeight = $camArr[1];
        $clip->camFPS = $values['camera_fps'];
        $clip->micRate = $values['microphone_rate'];
        $clip->soundQuality = $values['soundQuality'];
        $clip->camBandwidth = $values['bandwidth'];
        $clip->floodProtection = $values['flood_protection'];
        $clip->labelColor = $values['label_color'];
        $clip->layoutCode = $values['layout_code'];

        $clip->welcome2 = htmlspecialchars($values['welcome2']);
        $clip->offlineMessage = htmlspecialchars($values['offline_message']);
        $clip->floodProtection2 = $values['flood_protection2'];
        $clip->layoutCode2 = htmlspecialchars($values['layout_code2']);
        $clip->filterRegex = $values['filter_regex'];
        $clip->filterReplace = $values['filter_replace'];


        $permission = $values['show_camera_settings']."|";
        $permission .= $values['advanced_camera_settings']."|";
        $permission .= $values['configure_source']."|";
        $permission .= $values['only_video']."|";
        $permission .= $values['no_video']."|";
        $permission .= $values['no_embeds']."|";
        $permission .= $values['show_timer']."|";
        $permission .= $values['write_text']."|";
        $permission .= $values['private_textchat']."|";
        $permission .= $values['fill_window']."|";
        $permission .= $values['write_text2']."|";
        $permission .= $values['enable_video']."|";
        $permission .= $values['enable_chat']."|";
        $permission .= $values['enable_users']."|";
        $permission .= $values['fill_window2']."|";
        $permission .= $values['verbose_level']."|";
        $clip->permission = $permission;
        $clip->online = "no";
        $clip->onlineCount = 0;
        $clip->onlineUser = "0";
        $clip->onlineUsers = "0";

        $description = UTIL_HtmlTag::stripJs($values['description']);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $clip->description = $description;
        $clip->modifDatetime = time();

                if ( $clipService->updateClip($clip) )
                {
                    BOL_TagService::getInstance()->updateEntityTags(
                        $clip->id,
                        'vwls',
                        TagsField::getTags($values['tags'])
                    );

                    return array('result' => true, 'id' => $clip->id);
                }
            }
        }
        else
        {
            return array('result' => false, 'id' => $clip->id);
        }

        return false;
    }
}

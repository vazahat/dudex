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
 * Vwvc action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow_plugins.vwvc.controllers
 * @since 1.0
 */
class VWVC_CTRL_Vwvc extends OW_ActionController
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
     * @var VWVC_BOL_ClipService
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

        $this->plugin = OW::getPluginManager()->getPlugin('vwvc');
        $this->pluginJsUrl = $this->plugin->getStaticJsUrl();
        $this->ajaxResponder = OW::getRouter()->urlFor('VWVC_CTRL_Vwvc', 'ajaxResponder');

        $this->clipService = VWVC_BOL_ClipService::getInstance();

        $this->menu = $this->getMenu();

        if ( !OW::getRequest()->isAjax() )
        {
            OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'vwvc', 'vwvc');
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

/**        if ( !VWVC_BOL_ClipService::getInstance()->findClipsCount('featured') )
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
            $item->setLabel($language->text('vwvc', 'menu_' . $type));
            $item->setUrl(OW::getRouter()->urlForRoute('vwview_list', array('listType' => $type)));
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
     * Vwvc view action
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
//        $is_featured = VWVC_BOL_ClipFeaturedService::getInstance()->isFeatured($clip->id);
//        $this->assign('featured', $is_featured);

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwvc');
        $this->assign('moderatorMode', $modPermissions);

        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        if ( !$ownerMode && !OW::getUser()->isAuthorized('vwvc', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

//        $this->assign('auth_msg', null);
        
        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'vwvc_view_vwvc', 'ownerId' => $contentOwner, 'viewerId' => $userId);
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $cmtParams = new BASE_CommentsParams('vwvc', 'vwvc_comments');
        $cmtParams->setEntityId($id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);

        $vwvcCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $vwvcCmts);

        $vwvcRates = new BASE_CMP_Rate('vwvc', 'vwvc_rates', $id, $contentOwner);
        $this->addComponent('rate', $vwvcRates);

        $vwvcTags = new BASE_CMP_EntityTagCloud('vwvc');
        $vwvcTags->setEntityId($id);
        $vwvcTags->setRouteName('vwview_tagged_list');
        $this->addComponent('tags', $vwvcTags);

        $username = BOL_UserService::getInstance()->getUserName($clip->userId);
        $this->assign('username', $username);

        $displayName = BOL_UserService::getInstance()->getDisplayName($clip->userId);
        $this->assign('displayName', $displayName);

        OW::getDocument()->addScript($this->pluginJsUrl . 'vwvc.js');

        $objParams = array(
            'ajaxResponder' => $this->ajaxResponder,
            'clipId' => $id,
            'txtDelConfirm' => OW::getLanguage()->text('vwvc', 'confirm_delete'),
            'txtMarkFeatured' => OW::getLanguage()->text('vwvc', 'mark_featured'),
            'txtRemoveFromFeatured' => OW::getLanguage()->text('vwvc', 'remove_from_featured'),
            'txtApprove' => OW::getLanguage()->text('base', 'approve'),
            'txtDisapprove' => OW::getLanguage()->text('base', 'disapprove')
        );

        $script =
            "$(document).ready(function(){
                var clip = new vwvcClip( " . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        OW::getDocument()->setHeading($clip->title);
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvc');

        $toolbar = array();

        array_push($toolbar, array(
            'href' => 'javascript://',
            'id' => 'btn-vwvc-flag',
            'label' => $language->text('base', 'flag')
        ));

        if ( $ownerMode || $modPermissions )
        {
            array_push($toolbar, array(
                'href' => OW::getRouter()->urlForRoute('vwedit_clip', array('id' => $clip->id)),
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
                    'label' => $language->text('vwvc', 'remove_from_featured')
                ));
            }
            else
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'mark_featured',
                    'label' => $language->text('vwvc', 'mark_featured')
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
                ->jQueryEvent('#btn-vwvc-flag', 'click', 'document.flag(e.data.entity, e.data.id, e.data.title, e.data.href, "vwvc+flags");', array('e'),
                    array('entity' => 'vwvc_clip', 'id' => $clip->id, 'title' => $clip->title, 'href' => OW::getRouter()->urlForRoute('vwview_clip', array('id' => $clip->id))
                ));

        OW::getDocument()->addOnloadScript($js, 1001);
        
        OW::getDocument()->setTitle($language->text('vwvc', 'meta_title_vwvc_view', array('title' => $clip->title)));
        $tagsArr = BOL_TagService::getInstance()->findEntityTags($clip->id, 'vwvc');
    
        foreach ( $tagsArr as $t )
        {
            $labels[] = $t->label;
        }
        $tagStr = $tagsArr ? implode(', ', $labels) : '';
        OW::getDocument()->setDescription($language->text('vwvc', 'meta_description_vwvc_view', array('title' => $clip->title, 'tags' => $tagStr)));
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
        $modPermissions = OW::getUser()->isAuthorized('vwvc');
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
        
        $videoEditForm = new vwvcEditForm($clip->id);
        $this->addForm($videoEditForm);

        $videoEditForm->getElement('id')->setValue($clip->id);
        $videoEditForm->getElement('room_name')->setValue($clip->title);
        $videoEditForm->getElement('description')->setValue($clip->description);
        $videoEditForm->getElement('welcome')->setValue($clip->welcome);
        $resolution = $clip->camWidth."x".$clip->camHeight;
        $videoEditForm->getElement('resolution')->setValue($resolution);
        $videoEditForm->getElement('camera_fps')->setValue($clip->camFPS);
        $videoEditForm->getElement('microphone_rate')->setValue($clip->micRate);
        $videoEditForm->getElement('soundQuality')->setValue($clip->soundQuality);
        $videoEditForm->getElement('bandwidth')->setValue($clip->camBandwidth);
        $videoEditForm->getElement('background_url')->setValue($clip->background_url);
        $videoEditForm->getElement('layout_code')->setValue($clip->layoutCode);
        $permissions = $clip->permission;
        $permission = explode("|", $permissions);
        $videoEditForm->getElement('fill_window')->setValue($permission [0]);
        $videoEditForm->getElement('show_camera_settings')->setValue($permission [1]);
        $videoEditForm->getElement('advanced_camera_settings')->setValue($permission [2]);
        $videoEditForm->getElement('configure_source')->setValue($permission [3]);
        $videoEditForm->getElement('disable_video')->setValue($permission [4]);
        $videoEditForm->getElement('disable_sound')->setValue($permission [5]);
        $videoEditForm->getElement('panel_rooms')->setValue($permission [6]);
        $videoEditForm->getElement('panel_users')->setValue($permission [7]);
        $videoEditForm->getElement('panel_files')->setValue($permission [8]);
        $videoEditForm->getElement('file_upload')->setValue($permission [9]);
        $videoEditForm->getElement('file_delete')->setValue($permission [10]);
        $videoEditForm->getElement('tutorial')->setValue($permission [11]);
        $videoEditForm->getElement('auto_view_cameras')->setValue($permission [12]);
        $videoEditForm->getElement('show_timer')->setValue($permission [13]);
        $videoEditForm->getElement('write_text')->setValue($permission [14]);
        $videoEditForm->getElement('regular_watch')->setValue($permission [15]);
        $videoEditForm->getElement('new_watch')->setValue($permission [16]);
        $videoEditForm->getElement('private_textchat')->setValue($permission [17]);
        $videoEditForm->getElement('administrator')->setValue($permission [18]);
        $videoEditForm->getElement('verbose_level')->setValue($permission [19]);
        $videoEditForm->getElement('flood_protection')->setValue($clip->floodProtection);
        $videoEditForm->getElement('filter_regex')->setValue($clip->filterRegex);
        $videoEditForm->getElement('filter_replace')->setValue($clip->filterReplace);
        $videoEditForm->getElement('user_list')->setValue($clip->user_list);
        $videoEditForm->getElement('moderator_list')->setValue($clip->moderator_list);

        if ( OW::getRequest()->isPost() && $videoEditForm->isValid($_POST) )
        {
            $res = $videoEditForm->process();
            OW::getFeedback()->info($language->text('vwvc', 'clip_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('vwview_clip', array('id' => $res['id'])));
        }
        
        OW::getDocument()->setHeading($language->text('vwvc', 'tb_vwedit_clip'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvc');
        OW::getDocument()->setTitle($language->text('vwvc', 'tb_vwedit_clip'));
    }

    /**
     * Vwvc list view action
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
        $modPermissions = OW::getUser()->isAuthorized('vwvc');

        if ( !OW::getUser()->isAuthorized('vwvc', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $this->addComponent('vwvcMenu', $this->menu);

        $el = $this->menu->getElement($listType);
        if ( $el )
        {
            $el->setActive(true);
        }

        $this->assign('listType', $listType);

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwvc', 'page_title_browse_vwvc'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvc');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwvc', 'meta_title_vwvc_'.$listType));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwvc', 'meta_description_vwvc_'.$listType));

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWVC_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwvc', 'click', 'document.location.href = addNewUrl');

        OW::getDocument()->addOnloadScript($js);
    }

    /**
     * User vwvc list view action
     *
     * @param array $params
     */
    public function viewUserVwvcList( array $params )
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
        $modPermissions = OW::getUser()->isAuthorized('vwvc');

        if ( !OW::getUser()->isAuthorized('vwvc', 'view') && !$modPermissions && !$ownerMode )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
        
        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'vwvc_view_vwvc', 'ownerId' => $user->id, 'viewerId' => OW::getUser()->getId());
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $this->assign('userId', $user->id);

        $clipCount = VWVC_BOL_ClipService::getInstance()->findUserClipsCount($user->id);
        $this->assign('total', $clipCount);

        $displayName = BOL_UserService::getInstance()->getDisplayName($user->id);
        $this->assign('userName', $displayName);

        $heading = OW::getLanguage()->text('vwvc', 'page_title_vwvc_by', array('user' => $displayName));

        OW::getDocument()->setHeading($heading);
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvc');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwvc', 'meta_title_user_vwvc', array('displayName' => $displayName)));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwvc', 'meta_description_user_vwvc', array('displayName' => $displayName)));
    }


    /**
     * Onilne vwvc list view action
     *
     * @param array $params
     */
    public function viewOnlineList( array $params = null )
    {
        $listType = 'online';

        $validLists = array('online', 'latest', 'toprated', 'tagged');
        
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwvc');

        if ( !OW::getUser()->isAuthorized('vwvc', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $this->addComponent('vwvcMenu', $this->menu);

        $el = $this->menu->getElement($listType);
        if ( $el )
        {
            $el->setActive(true);
        }

        $this->assign('listType', $listType);

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwvc', 'page_title_browse_vwvc'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvc');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwvc', 'meta_title_vwvc_'.$listType));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwvc', 'meta_description_vwvc_'.$listType));

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWVC_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwvc', 'click', 'document.location.href = addNewUrl');

        OW::getDocument()->addOnloadScript($js);
    }



    /**
     * Tagged vwvc list view action
     *
     * @param array $params
     */
    public function viewTaggedList( array $params = null )
    {
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwvc');

        if ( !OW::getUser()->isAuthorized('vwvc', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
        
        $tag = !empty($params['tag']) ? trim(htmlspecialchars(urldecode($params['tag']))) : '';

        $this->addComponent('vwvcMenu', $this->menu);

        $this->menu->getElement('tagged')->setActive(true);

        $this->setTemplate(OW::getPluginManager()->getPlugin('vwvc')->getCtrlViewDir() . 'vwvc_view_list-tagged.html');

        $listUrl = OW::getRouter()->urlForRoute('vwview_taggedlist_st');

        OW::getDocument()->addScript($this->pluginJsUrl . 'vwvc_tag_search.js');

        $objParams = array(
            'listUrl' => $listUrl
        );

        $script =
            "$(document).ready(function(){
                var vwvcSearch = new vwvcTagSearch(" . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        if ( strlen($tag) )
        {
            $this->assign('tag', $tag);
            
            OW::getDocument()->setTitle(OW::getLanguage()->text('vwvc', 'meta_title_vwvc_tagged_as', array('tag' => $tag)));
            OW::getDocument()->setDescription(OW::getLanguage()->text('vwvc', 'meta_description_vwvc_tagged_as', array('tag' => $tag)));
        }
        else
        {
            $tags = new BASE_CMP_EntityTagCloud('vwvc');
            $tags->setRouteName('vwview_tagged_list');
            $this->addComponent('tags', $tags);
            
            OW::getDocument()->setTitle(OW::getLanguage()->text('vwvc', 'meta_title_vwvc_tagged'));
            $tagsArr = BOL_TagService::getInstance()->findMostPopularTags('vwvc', 20);
    
            foreach ( $tagsArr as $t )
            {
                $labels[] = $t['label'];
            }
            $tagStr = $tagsArr ? implode(', ', $labels) : '';
            OW::getDocument()->setDescription(OW::getLanguage()->text('vwvc', 'meta_description_vwvc_tagged', array('topTags' => $tagStr)));
        }

        $this->assign('listType', 'tagged');

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwvc', 'page_title_browse_vwvc'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvc');

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWVC_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwvc', 'click', 'document.location.href = addNewUrl');

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
     * Set vwvc clip approval status (approved | blocked)
     *
     * @param array $params
     * @return array
     */
    public function ajaxSetApprovalStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('vwvc');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

        $setStatus = $this->clipService->updateClipStatus($clipId, $status);

        if ( $setStatus )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('vwvc', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('vwvc', 'status_not_changed'));
        }

        return $return;
    }

    /**
     * Deletes vwvc clip
     *
     * @param array $params
     * @return array
     */
    public function ajaxDeleteClip( $params )
    {
        $clipId = $params['clipId'];

        $ownerId = $this->clipService->findClipOwner($clipId);
        $isOwner = OW::getUser()->isAuthorized('vwvc', 'add', $ownerId);
        $isModerator = OW::getUser()->isAuthorized('vwvc');

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
                'msg' => OW::getLanguage()->text('vwvc', 'clip_deleted'),
                'url' => OW_Router::getInstance()->urlForRoute('vwvc_vwview_list')
            );
        }
        else
        {
            $return = array(
                'result' => false,
                'error' => OW::getLanguage()->text('vwvc', 'clip_not_deleted')
            );
        }

        return $return;
    }

    /**
     * Set 'is featured' status to vwvc clip 
     *
     * @param array $params
     * @return array
     */
    public function ajaxSetFeaturedStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('vwvc');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

//        $setResult = $this->clipService->updateClipFeaturedStatus($clipId, $status);

        if ( $setResult )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('vwvc', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('vwvc', 'status_not_changed'));
        }

        return $return;
    }
}

/**
 * Vwvc edit form class
 */
class vwvcEditForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct( $clipId )
    {
        parent::__construct('vwvcEditForm');

        $language = OW::getLanguage();

        // clip id field
        $clipIdField = new HiddenField('id');
        $clipIdField->setRequired(true);
        $this->addElement($clipIdField);

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

        // room_name Field
        $room_nameField = new TextField('room_name');
        $sValidator = new StringValidator(1, 22);
        $room_nameField->addValidator($sValidator);
        $room_nameField->setRequired(true);
        $this->addElement($room_nameField->setLabel($language->text('vwvc', 'room_name')));

        // Description Field
        $descriptionField = new Textarea('description');
        $this->addElement($descriptionField->setLabel($language->text('vwvc', 'description')));

        // welcome Field
        $welcomeField = new Textarea('welcome');
        $welcomeField->setValue($language->text('vwvc', 'welcome_default'));
        $this->addElement($welcomeField->setLabel($language->text('vwvc', 'welcome')));

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
        $this->addElement($resolutionField->setLabel($language->text('vwvc', 'resolution')));

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
        $this->addElement($camera_fpsField->setLabel($language->text('vwvc', 'camera_fps')));

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
        $this->addElement($microphone_rateField->setLabel($language->text('vwvc', 'microphone_rate')));

        // soundQuality Field
        $soundQualityField = new TextField('soundQuality');
        $soundQualityField->setRequired(true);
        $this->addElement($soundQualityField->setLabel($language->text('vwvc', 'soundQuality')));

        // Bandwidth Field
        $bandwidthField = new TextField('bandwidth');
        $bandwidthField->setRequired(true);
        $bandwidthField->setValue(40960);
        $this->addElement($bandwidthField->setLabel($language->text('vwvc', 'bandwidth')));

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
        $this->addElement($verbose_levelField->setLabel($language->text('vwvc', 'verbose_level')));

        // Background url Field
        $background_urlField = new TextField('background_url');
        $this->addElement($background_urlField->setLabel($language->text('vwvc', 'background_url')));

        // Layout Code Field
        $layout_codeField = new Textarea('layout_code');
        $this->addElement($layout_codeField->setLabel($language->text('vwvc', 'layout_code')));

        // Fill window Field
        $fill_windowField = new Selectbox('fill_window');
        $fill_windowField->addOptions($permArr0);
        $fill_windowField->setRequired();
        $fill_windowField->setHasInvitation(false);
        $this->addElement($fill_windowField->setLabel($language->text('vwvc', 'fill_window')));

        // FloodProtection Field
        $flood_protectionField = new TextField('flood_protection');
        $flood_protectionField->setValue(3);
        $this->addElement($flood_protectionField->setLabel($language->text('vwvc', 'flood_protection')));

        // Filter regex Field
        $filter_regexField = new TextField('filter_regex');
        $filter_regexField->setValue('(?i)(fuck|cunt)(?-i)');
        $this->addElement($filter_regexField->setLabel($language->text('vwvc', 'filter_regex')));

        // Filter replace Field
        $filter_replaceField = new TextField('filter_replace');
        $filter_replaceField->setValue('**');
        $this->addElement($filter_replaceField->setLabel($language->text('vwvc', 'filter_replace')));

        // Show Camera Settings Field
        $show_camera_settingsField = new Selectbox('show_camera_settings');
        $show_camera_settingsField->addOptions($permArr1);
        $show_camera_settingsField->setRequired();
        $show_camera_settingsField->setHasInvitation(false);
        $this->addElement($show_camera_settingsField->setLabel($language->text('vwvc', 'show_camera_settings')));

        // Advanced Camera Settings Field
        $advanced_camera_settingsField = new Selectbox('advanced_camera_settings');
        $advanced_camera_settingsField->addOptions($permArr1);
        $advanced_camera_settingsField->setRequired();
        $advanced_camera_settingsField->setHasInvitation(false);
        $this->addElement($advanced_camera_settingsField->setLabel($language->text('vwvc', 'advanced_camera_settings')));

        // Configure Source Field
        $configure_sourceField = new Selectbox('configure_source');
        $configure_sourceField->addOptions($permArr1);
        $configure_sourceField->setRequired();
        $configure_sourceField->setHasInvitation(false);
        $this->addElement($configure_sourceField->setLabel($language->text('vwvc', 'configure_source')));

        // Disable Video Field
        $disable_videoField = new Selectbox('disable_video');
        $disable_videoField->addOptions($permArr1);
        $disable_videoField->setRequired();
        $disable_videoField->setHasInvitation(false);
        $this->addElement($disable_videoField->setLabel($language->text('vwvc', 'disable_video')));

        // disable_sound Field
        $disable_soundField = new Selectbox('disable_sound');
        $disable_soundField->addOptions($permArr1);
        $disable_soundField->setRequired();
        $disable_soundField->setHasInvitation(false);
        $this->addElement($disable_soundField->setLabel($language->text('vwvc', 'disable_sound')));

        // panel Files Field
        $panel_filesField = new Selectbox('panel_files');
        $panel_filesField->addOptions($permArr1);
        $panel_filesField->setRequired();
        $panel_filesField->setHasInvitation(false);
        $this->addElement($panel_filesField->setLabel($language->text('vwvc', 'panel_files')));

        // panel rooms Field
        $panel_roomsField = new Selectbox('panel_rooms');
        $panel_roomsField->addOptions($permArr1);
        $panel_roomsField->setRequired();
        $panel_roomsField->setHasInvitation(false);
        $this->addElement($panel_roomsField->setLabel($language->text('vwvc', 'panel_rooms')));

        // panel users Field
        $panel_usersField = new Selectbox('panel_users');
        $panel_usersField->addOptions($permArr1);
        $panel_usersField->setRequired();
        $panel_usersField->setHasInvitation(false);
        $this->addElement($panel_usersField->setLabel($language->text('vwvc', 'panel_users')));

        // File Upload Field
        $file_uploadField = new Selectbox('file_upload');
        $file_uploadField->addOptions($permArr1);
        $file_uploadField->setRequired();
        $file_uploadField->setHasInvitation(false);
        $this->addElement($file_uploadField->setLabel($language->text('vwvc', 'file_upload')));

        // file_delete Field
        $file_deleteField = new Selectbox('file_delete');
        $file_deleteField->addOptions($permArr0);
        $file_deleteField->setRequired();
        $file_deleteField->setHasInvitation(false);
        $this->addElement($file_deleteField->setLabel($language->text('vwvc', 'file_delete')));

        // Tutorial Field
        $tutorialField = new Selectbox('tutorial');
        $tutorialField->addOptions($permArr1);
        $tutorialField->setRequired();
        $tutorialField->setHasInvitation(false);
        $this->addElement($tutorialField->setLabel($language->text('vwvc', 'tutorial')));

        // Auto View Cameras Field
        $auto_view_camerasField = new Selectbox('auto_view_cameras');
        $auto_view_camerasField->addOptions($permArr1);
        $auto_view_camerasField->setRequired();
        $auto_view_camerasField->setHasInvitation(false);
        $this->addElement($auto_view_camerasField->setLabel($language->text('vwvc', 'auto_view_cameras')));

        // Show Timer Field
        $show_timerField = new Selectbox('show_timer');
        $show_timerField->addOptions($permArr1);
        $show_timerField->setRequired();
        $show_timerField->setHasInvitation(false);
        $this->addElement($show_timerField->setLabel($language->text('vwvc', 'show_timer')));

        // writeText Field
        $write_textField = new Selectbox('write_text');
        $write_textField->addOptions($permArr1);
        $write_textField->setRequired();
        $write_textField->setHasInvitation(false);
        $this->addElement($write_textField->setLabel($language->text('vwvc', 'write_text')));

        // regularWatch Field
        $regular_watchField = new Selectbox('regular_watch');
        $regular_watchField->addOptions($permArr1);
        $regular_watchField->setRequired();
        $regular_watchField->setHasInvitation(false);
        $this->addElement($regular_watchField->setLabel($language->text('vwvc', 'regular_watch')));

        // newWatch Field
        $new_watchField = new Selectbox('new_watch');
        $new_watchField->addOptions($permArr1);
        $new_watchField->setRequired();
        $new_watchField->setHasInvitation(false);
        $this->addElement($new_watchField->setLabel($language->text('vwvc', 'new_watch')));

        // privateTextchat Field
        $private_textchatField = new Selectbox('private_textchat');
        $private_textchatField->addOptions($permArr1);
        $private_textchatField->setRequired();
        $private_textchatField->setHasInvitation(false);
        $this->addElement($private_textchatField->setLabel($language->text('vwvc', 'private_textchat')));

        // user_list Field
        $user_listField = new Textarea('user_list');
        $this->addElement($user_listField->setLabel($language->text('vwvc', 'user_list')));

        // moderator_list Field
        $moderator_listField = new Textarea('moderator_list');
        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById(OW::getUser()->getId());
        $username = $user->getUsername();
        $moderator_listField->setValue($username);
        $this->addElement($moderator_listField->setLabel($language->text('vwvc', 'moderator_list')));

        // administrator Field
        $administratorField = new Selectbox('administrator');
        $administratorField->addOptions($permArr0);
        $administratorField->setRequired();
        $administratorField->setHasInvitation(false);
        $this->addElement($administratorField->setLabel($language->text('vwvc', 'administrator')));

        // clean_up Field
        $clean_upField = new TextField('clean_up');
        $clean_upField->setValue(0);
        $this->addElement($clean_upField->setLabel($language->text('vwvc', 'clean_up')));
        
        $entityTags = BOL_TagService::getInstance()->findEntityTags($clipId, 'vwvc');

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

        $this->addElement($tagsField->setLabel($language->text('vwvc', 'tags')));

        $submit = new Submit('edit');
        $submit->setValue($language->text('vwvc', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates vwvc clip
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $clipService = VWVC_BOL_ClipService::getInstance();
        $language = OW::getLanguage();

        if ( $values['id'] )
        {
            $clip = $clipService->findClipById($values['id']);

            if ( $clip )
            {
        $clip->title = htmlspecialchars($values['room_name']);
        $clip->description = htmlspecialchars($values['description']);
        $clip->welcome = htmlspecialchars($values['welcome']);
        $cam = $values['resolution'];
        $camArr = explode("x", $cam); 
        $clip->camWidth = $camArr[0];
        $clip->camHeight = $camArr[1];
        $clip->camFPS = $values['camera_fps'];
        $clip->micRate = $values['microphone_rate'];
        $clip->soundQuality = $values['soundQuality'];
        $clip->camBandwidth = $values['bandwidth'];
        $clip->background_url = $values['background_url'];
        $clip->layoutCode = htmlspecialchars($values['layout_code']);
        $permission = $values['fill_window']."|";
        $permission .= $values['show_camera_settings']."|";
        $permission .= $values['advanced_camera_settings']."|";
        $permission .= $values['configure_source']."|";
        $permission .= $values['disable_video']."|";
        $permission .= $values['disable_sound']."|";
        $permission .= $values['panel_rooms']."|";
        $permission .= $values['panel_users']."|";
        $permission .= $values['panel_files']."|";
        $permission .= $values['file_upload']."|";
        $permission .= $values['file_delete']."|";
        $permission .= $values['tutorial']."|";
        $permission .= $values['auto_view_cameras']."|";
        $permission .= $values['show_timer']."|";
        $permission .= $values['write_text']."|";
        $permission .= $values['regular_watch']."|";
        $permission .= $values['new_watch']."|";
        $permission .= $values['private_textchat']."|";
        $permission .= $values['administrator']."|";
        $permission .= $values['verbose_level']."|";
        $clip->permission = $permission;
        $clip->floodProtection = $values['flood_protection'];
        $clip->filterRegex = $values['filter_regex'];
        $clip->filterReplace = $values['filter_replace'];
        $clip->user_list = $values['user_list'];
        $clip->moderator_list = $values['moderator_list'];
        $clip->modifDatetime = time();

        $description = UTIL_HtmlTag::stripJs($values['description']);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $clip->description = $description;

                if ( $clipService->updateClip($clip) )
                {
                    BOL_TagService::getInstance()->updateEntityTags(
                        $clip->id,
                        'vwvc',
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

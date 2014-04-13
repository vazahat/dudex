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
 * Vwvr action controller
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow_plugins.vwvr.controllers
 * @since 1.0
 */
class VWVR_CTRL_Vwvr extends OW_ActionController
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
     * @var VWVR_BOL_ClipService
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

        $this->plugin = OW::getPluginManager()->getPlugin('vwvr');
        $this->pluginJsUrl = $this->plugin->getStaticJsUrl();
        $this->ajaxResponder = OW::getRouter()->urlFor('VWVR_CTRL_Vwvr', 'ajaxResponder');

        $this->clipService = VWVR_BOL_ClipService::getInstance();

        $this->menu = $this->getMenu();

        if ( !OW::getRequest()->isAjax() )
        {
            OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'vwvr', 'vwvr');
        }
    }

    /**
     * Returns menu component
     *
     * @return BASE_CMP_ContentMenu
     */
    private function getMenu()
    {
        $validLists = array('latest', 'toprated', 'tagged');
        $classes = array('ow_ic_clock', 'ow_ic_star', 'ow_ic_tag');

/**        if ( !VWVR_BOL_ClipService::getInstance()->findClipsCount('featured') )
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
            $item->setLabel($language->text('vwvr', 'menu_' . $type));
            $item->setUrl(OW::getRouter()->urlForRoute('vwview_list_vr', array('listType' => $type)));
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
     * Vwvr record action
     *
     */
    public function record ()
    {

        $language = OW_Language::getInstance();


    }

    /**
     * Vwvr view action
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
//        $is_featured = VWVR_BOL_ClipFeaturedService::getInstance()->isFeatured($clip->id);
//        $this->assign('featured', $is_featured);

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwvr');
        $this->assign('moderatorMode', $modPermissions);

        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        if ( !$ownerMode && !OW::getUser()->isAuthorized('vwvr', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

//        $this->assign('auth_msg', null);
        
        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'vwvr_view_vwvr', 'ownerId' => $contentOwner, 'viewerId' => $userId);
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $cmtParams = new BASE_CommentsParams('vwvr', 'vwvr_comments');
        $cmtParams->setEntityId($id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);

        $vwvrCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $vwvrCmts);

        $vwvrRates = new BASE_CMP_Rate('vwvr', 'vwvr_rates', $id, $contentOwner);
        $this->addComponent('rate', $vwvrRates);

        $vwvrTags = new BASE_CMP_EntityTagCloud('vwvr');
        $vwvrTags->setEntityId($id);
        $vwvrTags->setRouteName('vwview_tagged_list_vr');
        $this->addComponent('tags', $vwvrTags);

        $username = BOL_UserService::getInstance()->getUserName($clip->userId);
        $this->assign('username', $username);

        $displayName = BOL_UserService::getInstance()->getDisplayName($clip->userId);
        $this->assign('displayName', $displayName);

        OW::getDocument()->addScript($this->pluginJsUrl . 'vwvr.js');

        $objParams = array(
            'ajaxResponder' => $this->ajaxResponder,
            'clipId' => $id,
            'txtDelConfirm' => OW::getLanguage()->text('vwvr', 'confirm_delete'),
            'txtMarkFeatured' => OW::getLanguage()->text('vwvr', 'mark_featured'),
            'txtRemoveFromFeatured' => OW::getLanguage()->text('vwvr', 'remove_from_featured'),
            'txtApprove' => OW::getLanguage()->text('base', 'approve'),
            'txtDisapprove' => OW::getLanguage()->text('base', 'disapprove')
        );

        $script =
            "$(document).ready(function(){
                var clip = new vwvrClip( " . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        OW::getDocument()->setHeading($clip->room_name);
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvr');

        $toolbar = array();

        array_push($toolbar, array(
            'href' => 'javascript://',
            'id' => 'btn-vwvr-flag',
            'label' => $language->text('base', 'flag')
        ));

        if ( $ownerMode || $modPermissions )
        {
            array_push($toolbar, array(
                'href' => OW::getRouter()->urlForRoute('vwedit_clip_vr', array('id' => $clip->id)),
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
                    'label' => $language->text('vwvr', 'remove_from_featured')
                ));
            }
            else
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'mark_featured',
                    'label' => $language->text('vwvr', 'mark_featured')
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
                ->jQueryEvent('#btn-vwvr-flag', 'click', 'document.flag(e.data.entity, e.data.id, e.data.title, e.data.href, "vwvr+flags");', array('e'),
                    array('entity' => 'vwvr_clip', 'id' => $clip->id, 'title' => $clip->title, 'href' => OW::getRouter()->urlForRoute('vwview_clip_vr', array('id' => $clip->id))
                ));

        OW::getDocument()->addOnloadScript($js, 1001);
        
        OW::getDocument()->setTitle($language->text('vwvr', 'meta_title_vwvr_view', array('title' => $clip->room_name)));
        $tagsArr = BOL_TagService::getInstance()->findEntityTags($clip->id, 'vwvr');
    
        foreach ( $tagsArr as $t )
        {
            $labels[] = $t->label;
        }
        $tagStr = $tagsArr ? implode(', ', $labels) : '';
        OW::getDocument()->setDescription($language->text('vwvr', 'meta_description_vwvr_view', array('title' => $clip->room_name, 'tags' => $tagStr)));
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
        $modPermissions = OW::getUser()->isAuthorized('vwvr');
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
        
        $videoEditForm = new vwvrEditForm($clip->id);
        $this->addForm($videoEditForm);

        $videoEditForm->getElement('id')->setValue($clip->id);
        $videoEditForm->getElement('room_name')->setValue($clip->room_name);
        $videoEditForm->getElement('description')->setValue($clip->description);

        if ( OW::getRequest()->isPost() && $videoEditForm->isValid($_POST) )
        {
            $res = $videoEditForm->process();
            OW::getFeedback()->info($language->text('vwvr', 'clip_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('vwview_clip_vr', array('id' => $res['id'])));
        }
        
        OW::getDocument()->setHeading($language->text('vwvr', 'tb_vwedit_clip_vr'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvr');
        OW::getDocument()->setTitle($language->text('vwvr', 'tb_vwedit_clip_vr'));
    }

    /**
     * Vwvr list view action
     *
     * @param array $params
     */
    public function viewList( array $params )
    {
        $listType = isset($params['listType']) ? trim($params['listType']) : 'latest';

        $validLists = array('latest', 'toprated', 'tagged');

        if ( !in_array($listType, $validLists) )
        {
            $this->redirect(OW::getRouter()->urlForRoute('view_photo_list', array('listType' => 'latest')));
        }
        
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwvr');

        if ( !OW::getUser()->isAuthorized('vwvr', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $this->addComponent('vwvrMenu', $this->menu);

        $el = $this->menu->getElement($listType);
        if ( $el )
        {
            $el->setActive(true);
        }

        $this->assign('listType', $listType);

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwvr', 'page_title_browse_vwvr'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvr');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwvr', 'meta_title_vwvr_'.$listType));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwvr', 'meta_description_vwvr_'.$listType));

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWVR_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwvr', 'click', 'document.location.href = addNewUrl');

        OW::getDocument()->addOnloadScript($js);
    }

    /**
     * User vwvr list view action
     *
     * @param array $params
     */
    public function viewUserVwvrList( array $params )
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
        $modPermissions = OW::getUser()->isAuthorized('vwvr');

        if ( !OW::getUser()->isAuthorized('vwvr', 'view') && !$modPermissions && !$ownerMode )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
        
        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'vwvr_view_vwvr', 'ownerId' => $user->id, 'viewerId' => OW::getUser()->getId());
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $this->assign('userId', $user->id);

        $clipCount = VWVR_BOL_ClipService::getInstance()->findUserClipsCount($user->id);
        $this->assign('total', $clipCount);

        $displayName = BOL_UserService::getInstance()->getDisplayName($user->id);
        $this->assign('userName', $displayName);

        $heading = OW::getLanguage()->text('vwvr', 'page_title_vwvr_by', array('user' => $displayName));

        OW::getDocument()->setHeading($heading);
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvr');
        OW::getDocument()->setTitle(OW::getLanguage()->text('vwvr', 'meta_title_user_vwvr', array('displayName' => $displayName)));
        OW::getDocument()->setDescription(OW::getLanguage()->text('vwvr', 'meta_description_user_vwvr', array('displayName' => $displayName)));
    }


    /**
     * Tagged vwvr list view action
     *
     * @param array $params
     */
    public function viewTaggedList( array $params = null )
    {
        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('vwvr');

        if ( !OW::getUser()->isAuthorized('vwvr', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }
        
        $tag = !empty($params['tag']) ? trim(htmlspecialchars(urldecode($params['tag']))) : '';

        $this->addComponent('vwvrMenu', $this->menu);

        $this->menu->getElement('tagged')->setActive(true);

        $this->setTemplate(OW::getPluginManager()->getPlugin('vwvr')->getCtrlViewDir() . 'vwvr_view_list-tagged.html');

        $listUrl = OW::getRouter()->urlForRoute('vwview_taggedlist_st_vr');

        OW::getDocument()->addScript($this->pluginJsUrl . 'vwvr_tag_search.js');

        $objParams = array(
            'listUrl' => $listUrl
        );

        $script =
            "$(document).ready(function(){
                var vwvrSearch = new vwvrTagSearch(" . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        if ( strlen($tag) )
        {
            $this->assign('tag', $tag);
            
            OW::getDocument()->setTitle(OW::getLanguage()->text('vwvr', 'meta_title_vwvr_tagged_as', array('tag' => $tag)));
            OW::getDocument()->setDescription(OW::getLanguage()->text('vwvr', 'meta_description_vwvr_tagged_as', array('tag' => $tag)));
        }
        else
        {
            $tags = new BASE_CMP_EntityTagCloud('vwvr');
            $tags->setRouteName('vwview_tagged_list_vr');
            $this->addComponent('tags', $tags);
            
            OW::getDocument()->setTitle(OW::getLanguage()->text('vwvr', 'meta_title_vwvr_tagged'));
            $tagsArr = BOL_TagService::getInstance()->findMostPopularTags('vwvr', 20);
    
            foreach ( $tagsArr as $t )
            {
                $labels[] = $t['label'];
            }
            $tagStr = $tagsArr ? implode(', ', $labels) : '';
            OW::getDocument()->setDescription(OW::getLanguage()->text('vwvr', 'meta_description_vwvr_tagged', array('topTags' => $tagStr)));
        }

        $this->assign('listType', 'tagged');

        OW::getDocument()->setHeading(OW::getLanguage()->text('vwvr', 'page_title_browse_vwvr'));
        OW::getDocument()->setHeadingIconClass('ow_ic_vwvr');

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('VWVR_CTRL_Add', 'index'))
                ->jQueryEvent('#btn-add-new-vwvr', 'click', 'document.location.href = addNewUrl');

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
     * Set vwvr clip approval status (approved | blocked)
     *
     * @param array $params
     * @return array
     */
    public function ajaxSetApprovalStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('vwvr');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

        $setStatus = $this->clipService->updateClipStatus($clipId, $status);

        if ( $setStatus )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('vwvr', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('vwvr', 'status_not_changed'));
        }

        return $return;
    }

    /**
     * Deletes vwvr clip
     *
     * @param array $params
     * @return array
     */
    public function ajaxDeleteClip( $params )
    {
        $clipId = $params['clipId'];

        $ownerId = $this->clipService->findClipOwner($clipId);
        $isOwner = OW::getUser()->isAuthorized('vwvr', 'add', $ownerId);
        $isModerator = OW::getUser()->isAuthorized('vwvr');

        if ( !$isOwner && !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

        // find room_name
        $clipRec = $this->clipService->findClipById($clipId);
        $clipName = $clipRec->title . $clipRec->recordingId ;
        
        if ($clipName) {
            // delete recorded file from recordPath
            $config = OW::getConfig();
            $recordPath = $config->getValue('vwvr', 'recordPath');
          	if (file_exists($fileRecordPath = $recordPath ."/". $clipName  . ".flv")) unlink($fileRecordPath);
          	if (file_exists($fileRecordPath = $recordPath ."/". $clipName  . ".mp4")) unlink($fileRecordPath);
          	
          	// delete recorded file from streams and recordings folder
            $dirname = 'streams';
            if (file_exists('../../'.$dirname)) {
              $dir = '../../'.$dirname;
            } elseif (file_exists('../../../'.$dirname)) {
              $dir = '../../../'.$dirname;
            } elseif (file_exists('../../../../'.$dirname)) {
              $dir = '../../../../'.$dirname;
            } elseif (file_exists('../../../../../'.$dirname)) {
              $dir = '../../../../../'.$dirname;
            } elseif (file_exists('../../../../../../'.$dirname)) {
              $dir = '../../../../../../'.$dirname;
            }
            // $streamsPath = realpath($dir);
            $streamsPath = realpath("../".$dir);
            
            // delete file if exists
          	if (file_exists($fileStreamsPath = $streamsPath ."/streams/". $clipName  . ".flv")) unlink($fileStreamsPath);
          	if (file_exists($fileStreamsPath = $streamsPath ."/streams/". $clipName  . ".key")) unlink($fileStreamsPath);
          	if (file_exists($fileStreamsPath = $streamsPath ."/streams/". $clipName  . ".meta")) unlink($fileStreamsPath);
          	
          	// delete file from recordings directory
          	if (file_exists($fileRecordings = OW_DIR_ROOT."ow_plugins/vwvideorecorder/vr/recordings/" . $clipName  . ".vwr")) unlink($fileRecordings);
        }

        $delResult = $this->clipService->deleteClip($clipId);

        if ( $delResult )
        {
            $return = array(
                'result' => true,
                'msg' => OW::getLanguage()->text('vwvr', 'clip_deleted'),
                'url' => OW_Router::getInstance()->urlForRoute('vwvr_vwview_list_vr')
            );
        }
        else
        {
            $return = array(
                'result' => false,
                'error' => OW::getLanguage()->text('vwvr', 'clip_not_deleted')
            );
        }

        return $return;
    }

    /**
     * Set 'is featured' status to vwvr clip 
     *
     * @param array $params
     * @return array
     */
    public function ajaxSetFeaturedStatus( $params )
    {
        $clipId = $params['clipId'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('vwvr');

        if ( !$isModerator )
        {
            throw new Redirect404Exception();
            return;
        }

//        $setResult = $this->clipService->updateClipFeaturedStatus($clipId, $status);

        if ( $setResult )
        {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('vwvr', 'status_changed'));
        }
        else
        {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('vwvr', 'status_not_changed'));
        }

        return $return;
    }
}

/**
 * Vwvr edit form class
 */
class vwvrEditForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct( $clipId )
    {
        parent::__construct('vwvrEditForm');

        $language = OW::getLanguage();

        // clip id field
        $clipIdField = new HiddenField('id');
        $clipIdField->setRequired(true);
        $this->addElement($clipIdField);

        // room_name Field
        $generated =  base_convert((time()-1224000000).rand(0,10),10,36);
        $room_nameField = new TextField('room_name');
        $sValidator = new StringValidator(1, 22);
        $room_nameField->addValidator($sValidator);
        $room_nameField->setRequired(true);
        $room_nameField->setValue($generated);

        $this->addElement($room_nameField->setLabel($language->text('vwvr', 'room_name')));

        // Description Field
        $descriptionField = new Textarea('description');
        $this->addElement($descriptionField->setLabel($language->text('vwvr', 'description')));
        
        $entityTags = BOL_TagService::getInstance()->findEntityTags($clipId, 'vwvr');

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

        $this->addElement($tagsField->setLabel($language->text('vwvr', 'tags')));

        $submit = new Submit('edit');
        $submit->setValue($language->text('vwvr', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates vwvr clip
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $clipService = VWVR_BOL_ClipService::getInstance();
        $language = OW::getLanguage();

        if ( $values['id'] )
        {
            $clip = $clipService->findClipById($values['id']);

            if ( $clip )
            {
        $clip->room_name = htmlspecialchars($values['room_name']);
        $description = UTIL_HtmlTag::stripJs($values['description']);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $clip->description = $description;

                if ( $clipService->updateClip($clip) )
                {
                    BOL_TagService::getInstance()->updateEntityTags(
                        $clip->id,
                        'vwvr',
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

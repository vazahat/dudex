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
class IVIDEO_MCTRL_Action extends OW_MobileActionController {

    private $ajaxResponder;

    public function __construct() {
        parent::__construct();

        $this->ajaxResponder = OW::getRouter()->urlFor('IVIDEO_CTRL_Action', 'ajaxResponder');

        $language = OW::getLanguage();
        $menu = new BASE_MCMP_ContentMenu();

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('latest');
        $menuItem->setLabel($language->text('ivideo', 'view_latest_videos'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'latest')));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(1);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('featured');
        $menuItem->setLabel($language->text('ivideo', 'view_featured_videos'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'featured')));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(2);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('popular');
        $menuItem->setLabel($language->text('ivideo', 'view_toprated_videos'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'toprated')));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(3);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('top-rated');
        $menuItem->setLabel($language->text('ivideo', 'view_category_videos'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_list_category'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(4);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('genres');
        $menuItem->setLabel($language->text('ivideo', 'user_tag_list'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('ivideo_tag_list'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(5);
        $menu->addElement($menuItem);

        $this->addComponent('menu', $menu);
        $this->menu = $menu;

        $this->assign('videosPerRow', OW::getConfig()->getValue('ivideo', 'videosPerRow'));
        $this->assign('addItemAuthorized', OW::getUser()->isAuthenticated() && OW::getUser()->isAuthorized('ivideo', 'add'));
    }

    public function listCategoryVideos($params) {
        $modPermissions = OW::getUser()->isAuthorized('ivideo');

        if (!OW::getUser()->isAuthorized('ivideo', 'view') && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $category = !empty($params['category']) ? trim(htmlspecialchars(urldecode($params['category']))) : '';
        $this->assign('listType', 'category');
        $this->assign('category', $category);

        $this->setPageTitle(OW::getLanguage()->text('ivideo', 'meta_title_video_category', array('category' => $category)));
        $this->setPageHeading(OW::getLanguage()->text('ivideo', 'meta_description_video_category', array('category' => $category)));
    }

    public function listCategory() {
        $modPermissions = OW::getUser()->isAuthorized('ivideo');

        if (!OW::getUser()->isAuthorized('ivideo', 'view') && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $this->setPageTitle(OW::getLanguage()->text('ivideo', 'meta_title_video_categories'));
        $this->setPageHeading(OW::getLanguage()->text('ivideo', 'meta_description_video_categories'));

        $categories = IVIDEO_BOL_VideoCategoryService::getInstance()->getAllVideoCategories(1, 200);
        $details = array();

        foreach ($categories as $category) {
            $id = $category['categoryId'];
            $details[$id]['id'] = $id;
            $details[$id]['name'] = $category['name'];
            $details[$id]['description'] = $category['description'];
            $details[$id]['count'] = $category['count'];
            $catValue = trim(htmlspecialchars($category['name']));

            $details[$id]['url'] = OW::getRouter()->urlForRoute('ivideo_category_items', array('category' => $catValue));
        }
        $this->assign('details', $details);
    }

    public function viewUserVideoList(array $params) {
        if (!isset($params['user']) || !strlen($userName = trim($params['user']))) {
            throw new Redirect404Exception();
            return;
        }

        $user = BOL_UserService::getInstance()->findByUsername($userName);
        if (!$user) {
            throw new Redirect404Exception();
            return;
        }

        $ownerMode = $user->id == OW::getUser()->getId();

        $modPermissions = OW::getUser()->isAuthorized('ivideo');

        if (!OW::getUser()->isAuthorized('ivideo', 'view') && !$modPermissions && !$ownerMode) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        if (!$ownerMode && !$modPermissions) {
            $privacyParams = array('action' => 'ivideo_view_video', 'ownerId' => $user->id, 'viewerId' => OW::getUser()->getId());
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $this->assign('userId', $user->id);

        $clipCount = IVIDEO_BOL_Service::getInstance()->findUserVideosCount($user->id);
        $this->assign('total', $clipCount);

        $displayName = BOL_UserService::getInstance()->getDisplayName($user->id);
        $this->assign('userName', $displayName);

        $heading = OW::getLanguage()->text('ivideo', 'page_title_video_by', array('user' => $displayName));

        OW::getDocument()->setHeading($heading);
        OW::getDocument()->setHeadingIconClass('ow_ic_video');
        OW::getDocument()->setTitle(OW::getLanguage()->text('ivideo', 'meta_title_user_video', array('displayName' => $displayName)));
        OW::getDocument()->setDescription(OW::getLanguage()->text('ivideo', 'meta_description_user_video', array('displayName' => $displayName)));
    }

    public function taglist(array $params = null) {
        $modPermissions = OW::getUser()->isAuthorized('ivideo');

        if (!OW::getUser()->isAuthorized('ivideo', 'view') && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $tag = !empty($params['tag']) ? trim(htmlspecialchars(urldecode($params['tag']))) : '';

        $this->setTemplate(OW::getPluginManager()->getPlugin('ivideo')->getCtrlViewDir() . 'action_taglist.html');

        $listUrl = OW::getRouter()->urlForRoute('ivideo_tag_list');

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('ivideo')->getStaticJsUrl() . 'ivideo_tag_search.js');

        $objParams = array(
            'listUrl' => $listUrl
        );

        $script = "$(document).ready(function(){
                var videoSearch = new ivideoTagSearch(" . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        if (strlen($tag)) {
            $this->assign('tag', $tag);

            OW::getDocument()->setTitle(OW::getLanguage()->text('ivideo', 'meta_title_video_tagged_as', array('tag' => $tag)));
            OW::getDocument()->setDescription(OW::getLanguage()->text('ivideo', 'meta_description_video_tagged_as', array('tag' => $tag)));
        } else {
            $tags = new BASE_CMP_EntityTagCloud('ivideo-video');
            $tags->setRouteName('ivideo_view_tagged_list');
            $this->addComponent('tags', $tags);

            OW::getDocument()->setTitle(OW::getLanguage()->text('ivideo', 'meta_title_video_tagged'));
            $tagsArr = BOL_TagService::getInstance()->findMostPopularTags('ivideo-video', 20);

            foreach ($tagsArr as $t) {
                $labels[] = $t['label'];
            }
            $tagStr = $tagsArr ? implode(', ', $labels) : '';
            OW::getDocument()->setDescription(OW::getLanguage()->text('ivideo', 'meta_description_video_tagged', array('topTags' => $tagStr)));
        }

        $this->assign('listType', 'tagged');

        $this->setPageHeading(OW::getLanguage()->text('ivideo', 'page_title_browse_video'));
        $this->setPageTitle(OW::getLanguage()->text('ivideo', 'page_description_browse_video'));
        OW::getDocument()->setHeadingIconClass('ow_ic_video');

        $js = UTIL_JsGenerator::newInstance()
                ->newVariable('addNewUrl', OW::getRouter()->urlFor('IVIDEO_CTRL_Action', 'upload'))
                ->jQueryEvent('#btn-add-new-video', 'click', 'document.location.href = addNewUrl');

        OW::getDocument()->addOnloadScript($js);
    }

    public function editvideo($params) {
        if (!isset($params['id']) || !($id = (int) $params['id'])) {
            throw new Redirect404Exception();
            return;
        }
        $videoService = IVIDEO_BOL_Service::getInstance();
        $video = $videoService->findVideoById($id);

        if (!$video) {
            throw new Redirect404Exception();
        }

        $language = OW_Language::getInstance();

        $modPermissions = OW::getUser()->isAuthorized('ivideo');
        $this->assign('moderatorMode', $modPermissions);

        $contentOwner = (int) $videoService->findVideoOwner($id);
        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        if (!$ownerMode && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $videoEditForm = new videoEditForm($video->id);
        $this->addForm($videoEditForm);

        $videoEditForm->getElement('id')->setValue($video->id);
        $videoEditForm->getElement('name')->setValue($video->name);
        $videoEditForm->getElement('description')->setValue($video->description);
        $videoEditForm->getElement('category')->setValue(0);

        if (OW::getRequest()->isPost() && $videoEditForm->isValid($_POST)) {
            $res = $videoEditForm->process();
            OW::getFeedback()->info($language->text('ivideo', 'video_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('ivideo_view_video', array('id' => $res['id'])));
        }

        OW::getDocument()->setHeading($language->text('ivideo', 'tb_edit_video'));
        OW::getDocument()->setHeadingIconClass('ow_ic_video');
        OW::getDocument()->setTitle($language->text('ivideo', 'tb_edit_video'));
    }

    public function viewList(array $params) {

        if (!OW::getUser()->isAuthorized('ivideo', 'view')) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $eventParams = array('pluginKey' => 'ivideo', 'action' => 'view');
        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);

        if ($credits === false) {
            $this->assign('authMsg', OW::getEventManager()->call('usercredits.error_message', $eventParams));
            return;
        }

        $listType = isset($params['type']) ? trim($params['type']) : 'latest';
        $this->assign('listType', $listType);

        $this->assign('addItemAuthorized', OW::getUser()->isAuthenticated() && OW::getUser()->isAuthorized('ivideo', 'add'));

        OW::getDocument()->setHeadingIconClass('ow_ic_video');
        $this->setPageTitle(OW::getLanguage()->text('ivideo', 'meta_title_video_' . $listType));
        $this->setPageHeading(OW::getLanguage()->text('ivideo', 'meta_description_video_' . $listType));
    }

    public function viewvideo($params) {
        if (!isset($params['id']) || !($id = (int) $params['id'])) {
            throw new Redirect404Exception();
            return;
        }

        $id = empty($params['id']) ? 0 : (int) $params['id'];

        $modPermissions = OW::getUser()->isAuthorized('ivideo');
        $contentOwner = (int) IVIDEO_BOL_Service::getInstance()->findVideoOwner($id);

        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;

        if (!$ownerMode && !OW::getUser()->isAuthorized('ivideo', 'view') && !$modPermissions) {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $item = IVIDEO_BOL_VideoDao::getInstance()->findById((int) $id);

        if (!$ownerMode && !$modPermissions) {
            $privacyParams = array('action' => 'ivideo_view_video', 'ownerId' => $contentOwner, 'viewerId' => $userId);
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $eventParams = array('pluginKey' => 'ivideo', 'action' => 'view_video');
        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);

        if ($credits === false) {
            $this->assign('authMsg', OW::getEventManager()->call('usercredits.error_message', $eventParams));
            return;
        } else
            $this->assign('authMsg', null);

        $language = OW::getLanguage();

        if (is_null($item)) {
            OW::getFeedback()->error($language->text('ivideo', 'view_invalid_video_error'));
            $this->redirect(OW::getRouter()->urlForRoute('ivideo_view_list', array('type' => 'latest')));
        }

        $this->setPageTitle($item->name);
        $this->setPageHeading($item->name);

        $this->assign('isAdmin', OW::getUser()->isAdmin());

        $this->assign('item', $item);

        $allow_comments = true;

        if ($item->owner != OW::getUser()->getId() && !OW::getUser()->isAuthorized('ivideo')) {
            $eventParams = array(
                'action' => 'add_comment',
                'ownerId' => $item->id,
                'viewerId' => OW::getUser()->getId()
            );

            try {
                OW::getEventManager()->getInstance()->call('privacy_check_permission', $eventParams);
            } catch (RedirectException $ex) {
                $allow_comments = false;
            }
        }

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('ivideo')->getStaticJsUrl() . 'ivideo.js');

        $objParams = array(
            'ajaxResponder' => $this->ajaxResponder,
            'id' => $item->id,
            'txtDelConfirm' => OW::getLanguage()->text('ivideo', 'confirm_delete'),
            'txtMarkFeatured' => OW::getLanguage()->text('ivideo', 'mark_featured'),
            'txtRemoveFromFeatured' => OW::getLanguage()->text('ivideo', 'remove_from_featured'),
            'txtApprove' => OW::getLanguage()->text('base', 'approve'),
            'txtDisapprove' => OW::getLanguage()->text('base', 'disapprove')
        );

        $script = "$(document).ready(function(){
                var clip = new ivideoClip( " . json_encode($objParams) . ");
            }); ";

        OW::getDocument()->addOnloadScript($script);

        $cmpParams = new BASE_CommentsParams('ivideo', 'ivideo-comments');
        $cmpParams->setEntityId($item->id)
                ->setOwnerId($item->owner)
                ->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST)
                ->setAddComment($allow_comments);

        $this->addComponent('comments', new BASE_MCMP_Comments($cmpParams));

        $postTagsArray = BOL_TagService::getInstance()->findEntityTags($item->getId(), 'ivideo-video');
        $postTags = "";

        foreach ($postTagsArray as $tag) {
            $postTags .= $tag->label . ", ";
        }
        $postTags = substr($postTags, 0, -2);

        $tagCloud = new BASE_CMP_EntityTagCloud('ivideo-video');

        $tagCloud->setEntityId($item->id);
        $tagCloud->setRouteName('ivideo_view_tagged_list');
        $this->addComponent('tagCloud', $tagCloud);

        $username = BOL_UserService::getInstance()->getUserName($item->owner);
        $this->assign('username', $username);

        $displayName = BOL_UserService::getInstance()->getDisplayName($item->owner);
        $this->assign('displayName', $displayName);

        $ownerMode = $item->owner == OW::getUser()->getId();

        $modPermissions = OW::getUser()->isAuthorized('ivideo');

        $is_featured = IVIDEO_BOL_VideoFeaturedService::getInstance()->isFeatured($item->id);
        $this->assign('featured', $is_featured);

        $categoryList = IVIDEO_BOL_VideoCategoryService::getInstance()->getVideoCategories($item->id);
        $this->assign('categoryList', $categoryList);

        $toolbar = array();

        if (OW::getUser()->isAuthenticated()) {
            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'btn-ivideo-flag',
                'label' => $language->text('base', 'flag')
            ));
        }

        if ($ownerMode || $modPermissions) {
//            array_push($toolbar, array(
//                'href' => OW::getRouter()->urlForRoute('ivideo_edit_video', array('id' => $item->getId())),
//                'label' => $language->text('base', 'edit')
//            ));

            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'clip-delete',
                'label' => $language->text('base', 'delete')
            ));
        }

        if ($modPermissions) {
            if ($is_featured) {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'remove_from_featured',
                    'label' => $language->text('ivideo', 'remove_from_featured')
                ));
            } else {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'mark_featured',
                    'label' => $language->text('ivideo', 'mark_featured')
                ));
            }

            if ($item->status == 'approved') {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-set-approval-staus',
                    'rel' => 'disapprove',
                    'label' => $language->text('base', 'disapprove')
                ));
            } else {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-set-approval-staus',
                    'rel' => 'approve',
                    'label' => $language->text('base', 'approve')
                ));
            }
        }

        $this->assign('toolbar', $toolbar);

        $js = UTIL_JsGenerator::newInstance()
                ->jQueryEvent('#btn-ivideo-flag', 'click', 'OW.flagContent(e.data.entity, e.data.id, e.data.title, e.data.href, "ivideo+flags");', array('e'), array('entity' => 'ivideo_video', 'id' => $item->getId(), 'title' => $item->name, 'href' => OW::getRouter()->urlForRoute('ivideo_view_video', array('id' => $item->getId()))
        ));

        OW::getDocument()->addOnloadScript($js, 1001);

        $this->assign('getUserFilesUrl', OW::getPluginManager()->getPlugin('ivideo')->getUserFilesUrl());
        $this->assign('videoWidth', OW::getConfig()->getValue('ivideo', 'videoWidth'));
        $this->assign('videoHeight', OW::getConfig()->getValue('ivideo', 'videoHeight'));

        $videoType = UTIL_File::getExtension($item->filename);
        $this->assign('videoType', $videoType);

        $jsURL = OW::getPluginManager()->getPlugin('ivideo')->getStaticJsUrl();
        $this->assign('jsURL', $jsURL);

        if ($videoType == 'mp4' || $videoType == 'flv') {
            OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('ivideo')->getStaticCssUrl() . 'video-js.css');
            OW::getDocument()->addCustomHeadInfo('<script src="' . $jsURL . 'video.js" type="text/javascript"></script>');
            OW::getDocument()->addCustomHeadInfo('<script type="text/javascript"> _V_.options.flash.swf ="' . $jsURL . 'video-js.swf"</script>');
            OW::getDocument()->addCustomHeadInfo('<script type="text/javascript"> _V_.options.techOrder = ["flash", "html5"]</script>');
        } else {
            OW::getDocument()->addScript($jsURL . 'jquery.media.js');
            OW::getDocument()->addScript($jsURL . 'jquery.metadata.js');            
        }
    }

    public function ajaxResponder() {
        if (isset($_POST['ajaxFunc']) && OW::getRequest()->isAjax()) {
            $callFunc = (string) $_POST['ajaxFunc'];

            $result = call_user_func(array($this, $callFunc), $_POST);
        } else {
            throw new Redirect404Exception();
            exit;
        }

        exit(json_encode($result));
    }

    public function ajaxSetApprovalStatus($params) {
        $videoId = $params['id'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('ivideo');

        if (!$isModerator) {
            throw new Redirect404Exception();
            return;
        }

        $setStatus = IVIDEO_BOL_Service::getInstance()->updateVideoStatus($videoId, $status);

        if ($setStatus) {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('ivideo', 'status_changed'));
        } else {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('ivideo', 'status_not_changed'));
        }

        return $return;
    }

    public function ajaxDeleteClip($params) {
        $videoId = $params['id'];

        $ownerId = IVIDEO_BOL_Service::getInstance()->findOwner($videoId);
        $isOwner = OW::getUser()->isAuthorized('ivideo', 'add', $ownerId);
        $isModerator = OW::getUser()->isAuthorized('ivideo');

        if (!$isOwner && !$isModerator) {
            throw new Redirect404Exception();
            return;
        }

        $delResult = IVIDEO_BOL_Service::getInstance()->deleteVideo($videoId);

        if ($delResult) {
            $return = array(
                'result' => true,
                'msg' => OW::getLanguage()->text('ivideo', 'video_deleted'),
                'url' => OW_Router::getInstance()->urlForRoute('ivideo_view_list_main')
            );
        } else {
            $return = array(
                'result' => false,
                'error' => OW::getLanguage()->text('ivideo', 'video_not_deleted')
            );
        }

        return $return;
    }

    public function ajaxSetFeaturedStatus($params) {
        $videoId = $params['id'];
        $status = $params['status'];

        $isModerator = OW::getUser()->isAuthorized('ivideo');

        if (!$isModerator) {
            throw new Redirect404Exception();
            return;
        }

        $setResult = IVIDEO_BOL_Service::getInstance()->updateVideoFeaturedStatus($videoId, $status);

        if ($setResult) {
            $return = array('result' => true, 'msg' => OW::getLanguage()->text('ivideo', 'status_changed'));
        } else {
            $return = array('result' => false, 'error' => OW::getLanguage()->text('ivideo', 'status_not_changed'));
        }

        return $return;
    }

}

class importFile extends FileField {

    public function getValue() {
        return isset($_FILES[$this->getName()]) ? $_FILES[$this->getName()] : null;
    }

}

class importFileValidator extends OW_Validator {

    public function __construct() {
        $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'admin_invalid_file'));
    }

    public function isValid($value) {
        if (empty($value) || !is_array($value)) {
            return false;
        }

        if (!isset($value['name'])) {
            return false;
        }

        if (isset($value['error']) && $value['error'] > 0) {
            switch ($value['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'filesize_error'));
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'part_upld_error'));
                    break;

                case UPLOAD_ERR_NO_FILE:
                    $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'file_unpresent_error'));
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'temp_dir_error'));
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'temp_write_error'));
                    break;

                case UPLOAD_ERR_EXTENSION:
                    $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'invalid_extn_error'));
                    break;
            }

            return false;
        }

        $allowedExts = explode(",", OW::getConfig()->getValue('ivideo', 'allowedExtensions'));
        $allowedType = array("video/x-ms-wmv", "video/avi", "video/swf", "video/mpeg", "video/mpg", "video/mp4", "application/octet-stream");

        if (!in_array(UTIL_File::getExtension($value['name']), $allowedExts)) {
            $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'admin_not_allowed_file_type'));
            return false;
        }

        if (!in_array($value['type'], $allowedType)) {
            $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'admin_not_allowed_file_type'));
            return false;
        }

        if (!isset($value['type']) || $value['size'] == 0) {
            $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'admin_invalid_file'));
            return false;
        }

        $allowedSize = (int) OW::getConfig()->getValue('ivideo', 'allowedFileSize');

        if ($value['size'] > $allowedSize * 1024 * 1024) {
            $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'admin_file_size_greater'));
            return false;
        }

        return true;
    }

}

class videoEditForm extends Form {

    public function __construct($videoId) {
        parent::__construct('videoEditForm');

        $language = OW::getLanguage();

        $videoIdField = new HiddenField('id');
        $videoIdField->setRequired(true);
        $this->addElement($videoIdField);

        $titleField = new TextField('name');
        $titleField->addValidator(new StringValidator(1, 128));
        $titleField->setRequired(true);
        $this->addElement($titleField->setLabel($language->text('ivideo', 'upload_video_name')));

        $descField = new WysiwygTextarea('description');
        $this->addElement($descField->setLabel($language->text('ivideo', 'upload_video_desc')));

        $element = new Selectbox('category');
        $element->setRequired(true);
        $element->setLabel($language->text('ivideo', 'admin_video_category'));

        foreach (IVIDEO_BOL_CategoryDao::getInstance()->findAll() as $category)
            $element->addOption($category->id, $category->name);

        $this->addElement($element);

        $entityTags = BOL_TagService::getInstance()->findEntityTags($videoId, 'ivideo-video');

        if ($entityTags) {
            $tags = array();
            foreach ($entityTags as $entityTag) {
                $tags[] = $entityTag->label;
            }

            $tagsField = new TagsField('tags', $tags);
        } else {
            $tagsField = new TagsField('tags');
        }

        $this->addElement($tagsField->setLabel($language->text('ivideo', 'tags_field_label')));

        $submit = new Submit('edit');
        $submit->setValue($language->text('ivideo', 'button_edit_label'));
        $this->addElement($submit);
    }

    public function process() {
        $values = $this->getValues();
        $videoService = IVIDEO_BOL_Service::getInstance();
        $language = OW::getLanguage();

        if ($values['id']) {
            $video = $videoService->findVideoById($values['id']);

            if ($video) {
                $video->name = htmlspecialchars($values['name']);
                $description = UTIL_HtmlTag::stripJs($values['description']);
                $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
                $video->description = $description;

                if ($videoService->updateVideo($video)) {
                    BOL_TagService::getInstance()->updateEntityTags(
                            $video->id, 'ivideo-video', TagsField::getTags($values['tags'])
                    );

                    return array('result' => true, 'id' => $video->id);
                }
            }
        } else {
            return array('result' => false, 'id' => $video->id);
        }

        return false;
    }

}

class uploadValidator extends StringValidator {

    public function __construct() {
        $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'admin_video_not_uploaded_error'));
    }

    public function checkValue($value) {
        $fileName = OW::getSession()->get('ivideo.filename');
        if (is_null($fileName)) {
            $this->setErrorMessage(OW::getLanguage()->text('ivideo', 'admin_video_not_uploaded_error'));
            return false;
        }

        return true;
    }

}

<?php

class GVIDEOVIEWER_CMP_VideoComment extends OW_Component
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
     * @var VIDEO_BOL_ClipService
     */
    private $clipService;
	
    public function __construct( array $params )
    {
        parent::__construct();
		
        $id = $params['videoId'];
		
		$this->clipService = VIDEO_BOL_ClipService::getInstance();
		
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
		
		$is_featured = VIDEO_BOL_ClipFeaturedService::getInstance()->isFeatured($clip->id);
        $this->assign('featured', $is_featured);

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('video');
        $this->assign('moderatorMode', $modPermissions);

        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        if ( !$ownerMode && !OW::getUser()->isAuthorized('video', 'view') && !$modPermissions )
        {
            $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getCtrlViewDir() . 'authorization_failed.html');
            return;
        }

        $this->assign('auth_msg', null);

        // permissions check
        if ( !$ownerMode && !$modPermissions )
        {
            $privacyParams = array('action' => 'video_view_video', 'ownerId' => $contentOwner, 'viewerId' => $userId);
            $event = new OW_Event('privacy_check_permission', $privacyParams);
            OW::getEventManager()->trigger($event);
        }

        $cmtParams = new BASE_CommentsParams('video', 'video_comments');
        $cmtParams->setEntityId($id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);

        $videoCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $videoCmts);

        $videoRates = new BASE_CMP_Rate('video', 'video_rates', $id, $contentOwner);
        $this->addComponent('rate', $videoRates);

        $videoTags = new BASE_CMP_EntityTagCloud('video');
        $videoTags->setEntityId($id);
        $videoTags->setRouteName('view_tagged_list');
        $this->addComponent('tags', $videoTags);
		
		$this->assign('canEdit', false);
		$this->assign('canReport', false);
		$this->assign('canMakeFeature', false);		
		
		OW::getLanguage()->addKeyForJs('video', 'tb_edit_clip');	
		OW::getLanguage()->addKeyForJs('video', 'confirm_delete');
		OW::getLanguage()->addKeyForJs('video', 'mark_featured');
		OW::getLanguage()->addKeyForJs('video', 'remove_from_featured');
		OW::getLanguage()->addKeyForJs('base', 'approve');
		OW::getLanguage()->addKeyForJs('base', 'disapprove');
		
        $toolbar = array();

        $toolbarEvent = new BASE_CLASS_EventCollector('video.collect_video_toolbar_items', array(
            'clipId' => $clip->id,
            'clipDto' => $clip
        ));
		
        OW::getEventManager()->trigger($toolbarEvent);

        foreach ( $toolbarEvent->getData() as $toolbarItem )
        {
            array_push($toolbar, $toolbarItem);
        }

        if ( OW::getUser()->isAuthenticated() && !$ownerMode )
        {
            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'btn-video-flag',
                'label' => $language->text('base', 'flag')
            ));
			$this->assign('canReport', true);
        }

        if ( $ownerMode || $modPermissions )
        {
            array_push($toolbar, array(
                'href' => OW::getRouter()->urlForRoute('edit_clip', array('id' => $clip->id)),
                'label' => $language->text('base', 'edit')
            ));

            array_push($toolbar, array(
                'href' => 'javascript://',
                'id' => 'clip-delete',
                'label' => $language->text('base', 'delete')
            ));
			$this->assign('canEdit', true);
        }

        if ( $modPermissions )
        {
            if ( $is_featured )
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'remove_from_featured',
                    'label' => $language->text('video', 'remove_from_featured')
                ));
				$this->assign('isFeature', true);
            }
            else
            {
                array_push($toolbar, array(
                    'href' => 'javascript://',
                    'id' => 'clip-mark-featured',
                    'rel' => 'mark_featured',
                    'label' => $language->text('video', 'mark_featured')
                ));
				$this->assign('isFeature', false);
            }
			$this->assign('canMakeFeature', true);
            /*
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
            */
        }
		
        $this->assign('toolbar', $toolbar);
		/*
        $js = UTIL_JsGenerator::newInstance()
                ->jQueryEvent('#btn-video-flag', 'click', 'OW.flagContent(e.data.entity, e.data.id, e.data.title, e.data.href, "video+flags");', array('e'),
                    array('entity' => 'video_clip', 'id' => $clip->id, 'title' => $clip->title, 'href' => OW::getRouter()->urlForRoute('view_clip', array('id' => $clip->id))
                ));

        OW::getDocument()->addOnloadScript($js, 1001);
		*/
		//avatar
		$avatar = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($contentOwner), true, true, true, false);
        $this->assign('avatar', $avatar[$contentOwner]);

























		/*
        $config = OW::getConfig();
        $lang = OW::getLanguage();

        $this->videoService = VIDEO_BOL_VideoService::getInstance();
        $this->videoAlbumService = VIDEO_BOL_VideoAlbumService::getInstance();

        $video = $this->videoService->findVideoById($videoId);
        $album = $this->videoAlbumService->findAlbumById($video->albumId);
        $this->assign('album', $album);
		$this->assign('video', $video);

        // is owner
        $contentOwner = $this->videoService->findVideoOwner($video->id);
        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('video');
        $this->assign('moderatorMode', $modPermissions);

        $canView = true;
        if ( !$ownerMode && !$modPermissions && !OW::getUser()->isAuthorized('video', 'view') )
        {
            $canView = false;
        }

        $this->assign('canView', $canView);
		$this->assign('canDownload', $config->getValue('gvideoviewer', 'can_users_to_download_videos'));
		
        $cmtParams = new BASE_CommentsParams('video', 'video_comments');
        $cmtParams->setEntityId($video->id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);

        $videoCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $videoCmts);

        $videoRates = new BASE_CMP_Rate('video', 'video_rates', $video->id, $contentOwner);
        $this->addComponent('rate', $videoRates);

        $videoTags = new BASE_CMP_EntityTagCloud('video');
        $videoTags->setEntityId($video->id);
        $videoTags->setRouteName('view_tagged_video_list');
        $this->addComponent('tags', $videoTags);

        $description = $video->description;
        $video->description = UTIL_HtmlTag::autoLink($video->description);

        $this->assign('video', $video);
        $this->assign('url', $this->videoService->getVideoUrl($video->id));
        $this->assign('ownerName', BOL_UserService::getInstance()->getUserName($album->userId));

        $is_featured = VIDEO_BOL_VideoFeaturedService::getInstance()->isFeatured($video->id);

        if ( (int) $config->getValue('video', 'store_fullsize') && $video->hasFullsize )
        {
            $this->assign('fullsizeUrl', $this->videoService->getVideoFullsizeUrl($video->id));
        }
        else
        {
            $this->assign('fullsizeUrl', null);
        }

        $action = new BASE_ContextAction();
        $action->setKey('video-moderate');

        $context = new BASE_CMP_ContextAction();
        $context->addAction($action);

        $contextEvent = new BASE_CLASS_EventCollector('video.collect_video_context_actions', array(
            'videoId' => $videoId,
            'videoDto' => $video
        ));

        OW::getEventManager()->trigger($contextEvent);
		$this->assign('canEdit', false);
		$this->assign('canReport', false);
		$this->assign('canMakeFeature', false);
        foreach ( $contextEvent->getData() as $contextAction )
        {
			
            $action = new BASE_ContextAction();
            $action->setKey(empty($contextAction['key']) ? uniqid() : $contextAction['key']);
            $action->setParentKey('video-moderate');
            $action->setLabel($contextAction['label']);

            if ( !empty($contextAction['id']) )
            {
                $action->setId($contextAction['id']);
            }

            if ( !empty($contextAction['order']) )
            {
                $action->setOrder($contextAction['order']);
            }

            if ( !empty($contextAction['class']) )
            {
                $action->setClass($contextAction['class']);
            }

            if ( !empty($contextAction['url']) )
            {
                $action->setUrl($contextAction['url']);
            }

            $attributes = empty($contextAction['attributes']) ? array() : $contextAction['attributes'];
            foreach ( $attributes as $key => $value )
            {
                $action->addAttribute($key, $value);
            }

            $context->addAction($action);
        }

        if ( $userId && !$ownerMode )
        {
            $action = new BASE_ContextAction();
            $action->setKey('flag');
            $action->setParentKey('video-moderate');
            $action->setLabel($lang->text('base', 'flag'));
            $action->setId('btn-video-flag');
            $action->addAttribute('rel', $videoId);
            $action->addAttribute('url', OW::getRouter()->urlForRoute('view_video', array('id' => $video->id)));

            $context->addAction($action);
			$this->assign('canReport', true);
        }

        if ( $ownerMode || $modPermissions )
        {
            $action = new BASE_ContextAction();
            $action->setKey('edit');
            $action->setParentKey('video-moderate');
            $action->setLabel($lang->text('base', 'edit'));
            $action->setId('btn-video-edit');
            $action->addAttribute('rel', $videoId);

            $context->addAction($action);

            $action = new BASE_ContextAction();
            $action->setKey('delete');
            $action->setParentKey('video-moderate');
            $action->setLabel($lang->text('base', 'delete'));
            $action->setId('video-delete');
            $action->addAttribute('rel', $videoId);

            $context->addAction($action);
			
			$this->assign('canEdit', true);
        }

        if ( $modPermissions )
        {
            if ( $is_featured )
            {
                $action = new BASE_ContextAction();
                $action->setKey('unmark-featured');
                $action->setParentKey('video-moderate');
                $action->setLabel($lang->text('video', 'remove_from_featured'));
                $action->setId('video-mark-featured');
                $action->addAttribute('rel', 'remove_from_featured');
                $action->addAttribute('video-id', $videoId);

                $context->addAction($action);
				$this->assign('isFeature', true);
            }
            else
            {
                $action = new BASE_ContextAction();
                $action->setKey('mark-featured');
                $action->setParentKey('video-moderate');
                $action->setLabel($lang->text('video', 'mark_featured'));
                $action->setId('video-mark-featured');
                $action->addAttribute('rel', 'mark_featured');
                $action->addAttribute('video-id', $videoId);

                $context->addAction($action);
				$this->assign('isFeature', false);
            }
			$this->assign('canMakeFeature', true);
        }
		
        $this->addComponent('contextAction', $context);
        $avatar = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($contentOwner), true, true, true, false);
        $this->assign('avatar', $avatar[$contentOwner]);
		*/
    }
}
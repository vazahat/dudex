<?php

class GPHOTOVIEWER_CMP_PhotoComment extends OW_Component
{
    /**
     * @var PHOTO_BOL_PhotoService
     */
    private $photoService;
    /**
     * @var PHOTO_BOL_PhotoAlbumService
     */
    private $photoAlbumService;

    /**
     * Class constructor
     *
     * @param string $listType
     * @param int $count
     * @param string $tag
     */
    public function __construct( array $params )
    {
        parent::__construct();
		
        $photoId = $params['photoId'];

        $config = OW::getConfig();
        $lang = OW::getLanguage();

        $this->photoService = PHOTO_BOL_PhotoService::getInstance();
        $this->photoAlbumService = PHOTO_BOL_PhotoAlbumService::getInstance();

        $photo = $this->photoService->findPhotoById($photoId);
        $album = $this->photoAlbumService->findAlbumById($photo->albumId);
        $this->assign('album', $album);
		$this->assign('photo', $photo);

        // is owner
        $contentOwner = $this->photoService->findPhotoOwner($photo->id);
        $userId = OW::getUser()->getId();
        $ownerMode = $contentOwner == $userId;
        $this->assign('ownerMode', $ownerMode);

        // is moderator
        $modPermissions = OW::getUser()->isAuthorized('photo');
        $this->assign('moderatorMode', $modPermissions);

        $canView = true;
        if ( !$ownerMode && !$modPermissions && !OW::getUser()->isAuthorized('photo', 'view') )
        {
            $canView = false;
        }

        $this->assign('canView', $canView);

        $cmtParams = new BASE_CommentsParams('photo', 'photo_comments');
        $cmtParams->setEntityId($photo->id);
        $cmtParams->setOwnerId($contentOwner);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_BOTTOM_FORM_WITH_FULL_LIST);

        $photoCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $photoCmts);

        $photoRates = new BASE_CMP_Rate('photo', 'photo_rates', $photo->id, $contentOwner);
        $this->addComponent('rate', $photoRates);

        $photoTags = new BASE_CMP_EntityTagCloud('photo');
        $photoTags->setEntityId($photo->id);
        $photoTags->setRouteName('view_tagged_photo_list');
        $this->addComponent('tags', $photoTags);

        $description = $photo->description;
        $photo->description = UTIL_HtmlTag::autoLink($photo->description);

        $this->assign('photo', $photo);
        $this->assign('url', $this->photoService->getPhotoUrl($photo->id));
        $this->assign('ownerName', BOL_UserService::getInstance()->getUserName($album->userId));

        $is_featured = PHOTO_BOL_PhotoFeaturedService::getInstance()->isFeatured($photo->id);

        if ( (int) $config->getValue('photo', 'store_fullsize') && $photo->hasFullsize )
        {
        	if( (int) BOL_PluginService::getInstance()->findPluginByKey('photo')->build > 6272){ 
            	$this->assign('fullsizeUrl', $this->photoService->getPhotoFullsizeUrl($photo->id, $photo->hash));
        	}else{
        		$this->assign('fullsizeUrl', $this->photoService->getPhotoFullsizeUrl($photo->id));
        	}	
        }
        else
        {
            $this->assign('fullsizeUrl', null);
        }

        $action = new BASE_ContextAction();
        $action->setKey('photo-moderate');

        $context = new GPHOTOVIEWER_CMP_FbAction();
        $context->addAction($action);

        $contextEvent = new BASE_CLASS_EventCollector('photo.collect_photo_context_actions', array(
            'photoId' => $photoId,
            'photoDto' => $photo
        ));

        OW::getEventManager()->trigger($contextEvent);
		$this->assign('canEdit', false);
		$this->assign('canReport', false);
		$this->assign('canMakeFeature', false);
        foreach ( $contextEvent->getData() as $contextAction )
        {
			
            $action = new BASE_ContextAction();
            $action->setKey(empty($contextAction['key']) ? uniqid() : $contextAction['key']);
            $action->setParentKey('photo-moderate');
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
            $action->setParentKey('photo-moderate');
            $action->setLabel( '<i class="icon-flag"></i>' . $lang->text('base', 'flag'));
            $action->setId('btn-photo-flag-' . $photo->id);
            $action->addAttribute('rel', $photoId);
            $action->addAttribute('url', OW::getRouter()->urlForRoute('view_photo', array('id' => $photo->id)));

            $context->addAction($action);
			$this->assign('canReport', true);
        }

        if ( $ownerMode || $modPermissions )
        { 
			/*
            $action = new BASE_ContextAction();
            $action->setKey('edit');
            $action->setParentKey('photo-moderate');
            $action->setLabel($lang->text('base', 'edit'));
            $action->setId('btn-photo-edit');
            $action->addAttribute('rel', $photoId);

            $context->addAction($action);

            $action = new BASE_ContextAction();
            $action->setKey('delete');
            $action->setParentKey('photo-moderate');
            $action->setLabel($lang->text('base', 'delete'));
            $action->setId('photo-delete');
            $action->addAttribute('rel', $photoId);

            $context->addAction($action);
			*/
			$this->assign('canEdit', true);
        }

        if ( $modPermissions )
        {
            if ( $is_featured )
            {
                $action = new BASE_ContextAction();
                $action->setKey('unmark-featured');
                $action->setParentKey('photo-moderate');
                $action->setLabel('<i class="icon-picture"></i>' . $lang->text('photo', 'remove_from_featured'));
                $action->setId('photo-mark-featured-' . $photo->id);
                $action->addAttribute('rel', 'remove_from_featured');
                $action->addAttribute('photo-id', $photoId);

                $context->addAction($action);
				$this->assign('isFeature', true);
            }
            else
            {
                $action = new BASE_ContextAction();
                $action->setKey('mark-featured');
                $action->setParentKey('photo-moderate');
                $action->setLabel('<i class="icon-picture"></i>' . $lang->text('photo', 'mark_featured'));
                $action->setId('photo-mark-featured-' . $photo->id);
                $action->addAttribute('rel', 'mark_featured');
                $action->addAttribute('photo-id', $photoId);

                $context->addAction($action);
				$this->assign('isFeature', false);
            }
			$this->assign('canMakeFeature', true);
        }
		
		$this->assign('canDownload', $canDownload = $config->getValue('gphotoviewer', 'can_users_to_download_photos'));
		if($canDownload){
			$action = new BASE_ContextAction();
            $action->setKey('download');
            $action->setParentKey('photo-moderate');
            $action->setLabel('<i class="icon-download"></i>' . $lang->text('gphotoviewer', 'download_this_photo'));
            $action->setId('btn-photo-download');
            $action->setUrl(OW::getRouter()->urlForRoute('gphotoviewer.photo_download', array('id' => $photo->id)));

            $context->addAction($action);
		}
		
        $this->addComponent('fbAction', $context);
        $avatar = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($contentOwner), true, true, true, false);
        $this->assign('avatar', $avatar[$contentOwner]);
    }
}
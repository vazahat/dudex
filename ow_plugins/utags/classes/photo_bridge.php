<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.classes
 */
class UTAGS_CLASS_PhotoBridge
{

    const FEED_ENTITY_TYPE = "photo_comments";
    
    /**
     * Class instance
     *
     * @var UTAGS_CLASS_PhotoBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return UTAGS_CLASS_PhotoBridge
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var OW_Plugin
     */
    private $plugin;
    
    private $defaultPhotoAlbumName = 'Photo with me';

    private $disabledEvents = array();
    
    private function __construct()
    {
        $this->plugin = OW::getPluginManager()->getPlugin('utags');
    }
    
    public function isActive()
    {
        return OW::getPluginManager()->isPluginActive("photo");
    }
    
    private function triggerEvent( OW_Event $event )
    {
        if ( in_array($event->getName(), $this->disabledEvents) )
        {
            return $event;
        }

        return OW::getEventManager()->trigger($event);
    }

    private function callEvent( $eventName, $params )
    {
        if ( in_array($eventName, $this->disabledEvents) )
        {
            return null;
        }

        return OW::getEventManager()->call($eventName, $params);
    }
    
    private function getAlbumName()
    {
        $albumName = OW::getLanguage()->text($this->plugin->getKey(), 'default_photo_album_name');

        return empty($albumName) ? $this->defaultPhotoAlbumName : $albumName;
    }

    private function getAlbum( $userId )
    {
        $albumService = PHOTO_BOL_PhotoAlbumService::getInstance();

        $albumName = $this->getAlbumName();
        $album = $albumService->findAlbumByName($albumName, $userId);

        if ( empty($album) )
        {
            $album = new PHOTO_BOL_PhotoAlbum();
            $album->name = $albumName;
            $album->userId = $userId;
            $album->createDatetime = time();

            $albumService->addAlbum($album);
        }

        return $album;
    }
    
    private function getTagRealCoords( $tagData, $imageWidth, $imageHeight, $requiredWidth, $requiredHeight )
    {
        if ( !OW::getConfig()->getValue("utags", "crop_photo") )
        {
            $resize = array(
                "width" => $requiredWidth,
                "height" => $requiredHeight,
                "crop" => true
            );
            
            return array(
                "resize" => $resize
            );
        }
        
        $areaWidth = $tagData["area"]["width"];
        $areaHeight = $tagData["area"]["height"];
        
        $scaleHeight = $imageHeight / $areaHeight;
        $scaleWidth = $imageWidth / $areaWidth;
        
        $top = $tagData["top"] * $scaleHeight;
        $left = $tagData["left"] * $scaleWidth;
        $width = $tagData["width"] * $scaleWidth;
        $height = $tagData["height"] * $scaleHeight;
        
        $resize = null;
        
        if ( $height > $requiredHeight || $width > $requiredWidth )
        {
            if ( $height > $width )
            {
                $tmp = $height - $width;
                $width = $height;
                $left = $left - $tmp / 2;
            }
            else
            {
                $tmp = $width - $height;
                $height = $width;
                $top = $top - $tmp / 2;
            }
            
            $resize = array(
                "width" => $requiredWidth,
                "height" => $requiredHeight,
                "crop" => true
            );
        }
        else
        {
            if ( $width < $requiredWidth )
            {
                $tmp = $requiredWidth - $width;
                $width = $requiredWidth;

                $left = $left - $tmp / 2;
            }

            if ( $height < $requiredHeight )
            {
                $tmp = $requiredHeight - $height;
                $height = $requiredHeight;

                $top = $top - $tmp / 2;
            }
        }
        
        
        $crop = array(
            "width" => $width > $imageWidth ? $imageWidth : $width,
            "height" => $height > $imageHeight ? $imageHeight : $height,
            "top" => $top <= 0 ? 0 : $top,
            "left" => $left <= 0 ? 0 : $left
        );
        
        return array(
            "crop" => $crop,
            "resize" => $resize
        );
    }


    /**
     *
     * @return PHOTO_BOL_Photo
     */
    private function upload( $userId, $path, $description, $tagData )
    {
        $pluginfilesDir = $this->plugin->getPluginFilesDir();
        $source = $pluginfilesDir . uniqid('tmp_') . '.jpg';

        if ( !@OW::getStorage()->copyFileToLocalFS($path, $source) )
        {
            return null;
        }

        $photoService = PHOTO_BOL_PhotoService::getInstance();

        $album = $this->getAlbum($userId);

        $privacy = OW::getEventManager()->call(
            'plugin.privacy.get_privacy',
            array('ownerId' => $userId, 'action' => 'photo_view_album')
        );

        $photo = new PHOTO_BOL_Photo();
        $photo->description = htmlspecialchars($description);
        $photo->albumId = $album->id;
        $photo->addDatetime = time();
        $photo->status = 'approved';
        $photo->hasFullsize = true;
        $photo->privacy = mb_strlen($privacy) ? $privacy : 'everybody';

        $photoService->addPhoto($photo);

        $config = OW::getConfig();
        $width = $config->getValue('photo', 'main_image_width');
        $height = $config->getValue('photo', 'main_image_height');
        $previewWidth = $config->getValue('photo', 'preview_image_width');
        $previewHeight = $config->getValue('photo', 'preview_image_height');

        $tmpMainPath = $pluginfilesDir . 'main_' . $photo->id . '.jpg';
        $tmpPreviewPath = $pluginfilesDir . 'preview_' . $photo->id . '.jpg';
        $tmpOriginalPath = $pluginfilesDir . 'original_' . $photo->id . '.jpg';

        try
        {
            $image = new UTIL_Image($source);

            $tagCoords = $this->getTagRealCoords($tagData, $image->getWidth(), $image->getHeight(), $previewWidth, $previewHeight);
            
            $mainPhoto = $image
                ->resizeImage($width, $height)
                ->saveImage($tmpMainPath);
            
            
            //Cropping tag
            if ( !empty($tagCoords["crop"]) )
            {
                $mainPhoto->cropImage($tagCoords["crop"]["left"], $tagCoords["crop"]["top"], $tagCoords["crop"]["width"], $tagCoords["crop"]["height"]);
            }
            
            if ( !empty($tagCoords["resize"]) )
            {
                $mainPhoto->resizeImage($tagCoords["resize"]["width"], $tagCoords["resize"]["height"], $tagCoords["resize"]["crop"]);
            }
            
            $mainPhoto->saveImage($tmpPreviewPath);

            if ( $config->getValue('photo', 'store_fullsize') && $mainPhoto->imageResized() )
            {
                $originalImage = new UTIL_Image($source);
                $res = (int) $config->getValue('photo', 'fullsize_resolution');
                $res = $res ? $res : 1024;
                $originalImage
                    ->resizeImage($res, $res)
                    ->saveImage($tmpOriginalPath);

                $photo->hasFullsize = true;
            }
            else
            {
                $photo->hasFullsize = false;
                $photoService->updatePhoto($photo);
            }
        }
        catch ( WideImage_Exception $e )
        {
            @unlink($source);

            return null;
        }

        @unlink($source);

        $storage = OW::getStorage();

        $mainPath = $photoService->getPhotoPath($photo->id, $photo->hash);
        $previewPath = $photoService->getPhotoPath($photo->id, $photo->hash, 'preview');
        $originalPath = $photoService->getPhotoPath($photo->id, $photo->hash, 'original');

        $storage->copyFile($tmpMainPath, $mainPath);
        @unlink($tmpMainPath);
        $storage->copyFile($tmpPreviewPath, $previewPath);
        @unlink($tmpPreviewPath);

        if ( $photo->hasFullsize )
        {
            $storage->copyFile($tmpOriginalPath, $originalPath);
            @unlink($tmpOriginalPath);
        }

        return $photo;
    }

    public function isPhotoExists( $photoId )
    {
        return PHOTO_BOL_PhotoService::getInstance()->findPhotoById($photoId) !== null;
    }
    
    /**
     * 
     * @param int $userId
     * @param int $photoId
     * @return PHOTO_BOL_Photo
     */
    public function copyPhoto( $userId, $photoId, $tagData )
    {
        $photo = PHOTO_BOL_PhotoService::getInstance()->findPhotoById($photoId);

        if ( empty($photo) )
        {
            return null;
        }

        $source = PHOTO_BOL_PhotoService::getInstance()->getPhotoPath($photo->id, $photo->hash);

        return $this->upload($userId, $source, $photo->description, $tagData);
    }
    
    
    public function getPhotoOwnerId( $photoId )
    {
        return PHOTO_BOL_PhotoService::getInstance()->findPhotoOwner($photoId);
    }
    
    public function getPhotoUrl( $photoId )
    {
        return OW::getRouter()->urlForRoute('view_photo', array('id' => $photoId));
    }
    
    public function getPreviewSrc( $photoId )
    {
        $photo = PHOTO_BOL_PhotoService::getInstance()->findPhotoById($photoId);
        
        if ( $photo == null )
        {
            return null;
        }
        
        return PHOTO_BOL_PhotoService::getInstance()->getPhotoPreviewUrl($photo->id, $photo->hash);
    }
    
    public function getTagCloudHtml( $photoId )
    {
        $photoTags = new BASE_CMP_EntityTagCloud(UTAGS_CLASS_TagsBridge::ENTITY_PHOTO);
        $photoTags->setEntityId($photoId);
        $photoTags->setRouteName('view_tagged_photo_list');

        return trim($photoTags->render());
    }
    
    public function collectActions(BASE_CLASS_EventCollector $event ) 
    {
        $params = $event->getParams();
        $photoId = $params["photoId"];
        $photoOwnerId = $this->getPhotoOwnerId($photoId);
        
        $isModerator = OW::getUser()->isAuthorized("utags");
        
        if ( !$isModerator )
        {
            if ( !OW::getUser()->isAuthorized("utags", "add_tags") )
            {
                return;
            }

            $permited = UTAGS_CLASS_PrivacyBridge::getInstance()->checkPrivacy(UTAGS_CLASS_PrivacyBridge::ACTION_TAG_MY_PHOTO, $photoOwnerId);

            if ( !$permited )
            {
                return;
            }
        }
        
        $action = array(
            "label" => OW::getLanguage()->text("utags", "start_tagging_btn"),
            "class" => "ut-start-tagging",
            "order" => -1,
            "attributes" => array(
                "data-pid" => $photoId,
            )
        );
        
        $event->add($action);
        
        OW::getLanguage()->addKeyForJs('utags', 'start_tagging_btn');
        OW::getLanguage()->addKeyForJs('utags', 'stop_tagging_btn');
    }
    
    public function addContent( BASE_CLASS_EventCollector $event ) 
    {
        $photo = OW_ViewRenderer::getInstance()->getAssignedVar("photo");
        
        $tags = array();
        $tagsCmp = null;
        
        if ( !empty($photo) )
        {
            $tags = UTAGS_BOL_Service::getInstance()->getTagsByPhotoId($photo->id, UTAGS_BOL_Service::ENTITY_TYPE_USER);
        }
        
        if ( !empty($tags) )
        {
            $tagsCmp = new UTAGS_CMP_TagList($tags);
        }
        
        $tagList = new UTAGS_CMP_TagListContainer($tagsCmp);
        $event->add($tagList->render());

        // Trick: dom elements reposition
        OW::getDocument()->addOnloadScript(
            '$(".ow_photo_info .ow_right div:first", "#ow-photo-view").after($(".ow_photo_info .ow_right .ow_box_empty:eq(2)", "#ow-photo-view").addClass("ut-custom-tags-wrap"));
            $(".ow_photo_info .ow_right div:first", "#ow-photo-view").after($(".ut-tag-list-wrap", "#ow-photo-view"));'
        );
        
        $closeUrl = OW::getThemeManager()->getCurrentTheme()->getStaticImagesUrl() . "chat_btn_close_search.png";
        OW::getDocument()->addStyleDeclaration(".ut-ic-close { background-image: url($closeUrl) };");
    }

    
    private $fbInited = false;
    public function initPhotoFloatBox()
    {
        if ( $this->fbInited ) return;
        
        $baseStaticUrl = OW::getPluginManager()->getPlugin('base')->getStaticJsUrl();
        $photoStaticUrl = OW::getPluginManager()->getPlugin('photo')->getStaticJsUrl();
        
        OW::getLanguage()->addKeyForJs('photo', 'tb_edit_photo');
        OW::getLanguage()->addKeyForJs('photo', 'confirm_delete');
        OW::getLanguage()->addKeyForJs('photo', 'mark_featured');
        OW::getLanguage()->addKeyForJs('photo', 'remove_from_featured');
        
        $objParams = array(
            'ajaxResponder' => OW::getRouter()->urlFor('PHOTO_CTRL_Photo', 'ajaxResponder'),
            'fbResponder' => OW::getRouter()->urlForRoute('photo.floatbox')
        );
        
        $onloadScript = UTIL_JsGenerator::composeJsString('UTAGS_Require(function( app ) { app.PhotoLauncher.setup({$settings}); });', array(
            'settings' => $objParams
        ));
        
        if ( !OW::getRequest()->isAjax() )
        {
            OW::getDocument()->addScript($baseStaticUrl . 'jquery.bbq.min.js');
            OW::getDocument()->addScript($photoStaticUrl . 'photo.js');
            OW::getDocument()->addOnloadScript($onloadScript);
        }
        else
        {
            OW::getDocument()->addOnloadScript(UTIL_JsGenerator::composeJsString('if ( window.photoView ) { ' . $onloadScript . ' } else {
                OW.addScriptFiles({$files}, function() { ' . $onloadScript . ' });
            }', array(
                "files" => array(
                    $baseStaticUrl . 'jquery.bbq.min.js',
                    $photoStaticUrl . 'photo.js'
                )
            )));
        }
        
        $this->fbInited = true;
    }
    
    public function onFinalize()
    {
        // JS events binding
        OW::getDocument()->addScriptDeclaration('OW.bind("photo.photo_show", function(params) { UTAGS_Require(function(t) { t.setPhoto(params.photoId); }); });');
        OW::getDocument()->addScriptDeclaration('OW.bind("photo.photo_show_complete", function(params) { UTAGS_Require(function(t) { t.activatePhoto(params.photoId, window.photoViewObj); }); });');
        OW::getDocument()->addScriptDeclaration('$(document.body).on("click", ".ut-start-tagging", function() { var self = $(this); UTAGS_Require(function(t) { t.startTagging(self.data("pid"), self); }); });');
    } 
    
    public function onTagRemove( OW_Event $event )
    {
        if ( !OW::getConfig()->getValue("utags", "copy_photo") )
        {
            return;
        }
        
        $params = $event->getParams();
        $tagId = $params['tagId'];
        
        $tag = UTAGS_BOL_Service::getInstance()->findTagById($tagId);
        
        if ( $tag === null || empty($tag->copyPhotoId) )
        {
            return;
        }
        
        PHOTO_BOL_PhotoService::getInstance()->deletePhoto($tag->copyPhotoId);
    }
    
    public function onTagAdd( OW_Event $event )
    {
        if ( !OW::getConfig()->getValue("utags", "copy_photo") )
        {
            return;
        }
        
        $params = $event->getParams();
        /*@var $tag UTAGS_BOL_Tag */
        $tag = $params['tag'];
        $tagData = $tag->getData();
        
        if ( $tag->entityType != UTAGS_BOL_Service::ENTITY_TYPE_USER )
        {
            return;
        }
        
        $newPhoto = $this->copyPhoto($tag->entityId, $tag->photoId, $tagData);

        if ( $newPhoto === null )
        {
            return;
        }
        
        $tag->copyPhotoId = $newPhoto->id;
    }
    
    public function afterPhotoDelete( OW_Event $event )
    {
        $params = $event->getParams();
        $photoId = $params["photoId"];
        
        $service = UTAGS_BOL_Service::getInstance();
        $copyTags = $service->findTagsByCopyPhotoId($photoId);
        foreach ( $copyTags as $tag )
        {
            $tag->copyPhotoId = null;
            $service->saveTag($tag);
        }
        
        
        $service->deleteByPhotoId($photoId);       
    }
    
    public function genericInit()
    {
        OW::getEventManager()->bind("photo.after_delete", array($this, "afterPhotoDelete"));
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_BEFORE_DELETE, array($this, 'onTagRemove'));
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_BEFORE_ADD, array($this, 'onTagAdd'));
    }
    
    public function init()
    {
        if ( !$this->isActive() ) return;
        
        $this->genericInit();
        
        OW::getEventManager()->bind("photo.collect_photo_context_actions", array($this, "collectActions"));
        OW::getEventManager()->bind(OW_EventManager::ON_FINALIZE, array($this, "onFinalize"));
        OW::getEventManager()->bind("photo.photo_floatbox.content.between_description_and_wall", array($this, "addContent"));
    }
}
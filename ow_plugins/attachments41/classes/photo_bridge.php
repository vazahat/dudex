<?php

class ATTACHMENTS_CLASS_PhotoBridge
{
    /**
     * Class instance
     *
     * @var ATTACHMENTS_CLASS_PhotoBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return ATTACHMENTS_CLASS_PhotoBridge
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private $isPluginActive = false;

    public function __construct()
    {
        $this->isPluginActive = OW::getPluginManager()->isPluginActive('photo');
    }

    public function isActive()
    {
        return $this->isPluginActive && OW::getConfig()->getValue('attachments', 'photo_share');
    }

    private function getAlbum( $userId, $entityType = "user", $entityId = null )
    {
        if ( !$this->isActive() ) return null;
        
        if ( empty($entityId) )
        {
            $entityId = $userId;
        }

        $albumName = OW::getLanguage()->text('attachments', 'default_photo_album_name');
        if ( empty($albumName) )
        {
            $albumName = 'Attached Photos';
        }

        $album = OW::getEventManager()->call("photo.album_find", array(
            "userId" => $userId,
            "albumTitle" => $albumName
        ));
        
        if ( empty($album) )
        {
            $data = OW::getEventManager()->call("photo.album_add", array(
                "userId" => $userId,
                "name" => $albumName,
                "entityType" => $entityType,
                "entityId" => $entityId
            ));
            
            $albumId = $data["albumId"];
        }
        else
        {
            $albumId = $album["id"];
        }

        return $albumId;
    }

    public function addPhoto( $userId, $path, $title, $text = null, $addToFeed = true )
    {
        if ( !$this->isActive() ) return null;
        
        $pluginfilesDir = OW::getPluginManager()->getPlugin('attachments')->getPluginFilesDir();
        $filePath = $pluginfilesDir . uniqid('tmp_') . '.jpg';
        
        if ( !@copy($path, $filePath) )
        {
            return null;
        }
        
        $description = empty($title) ? $text : $title;
        $description = empty($description) ? null : $description;
        
        $data = OW::getEventManager()->call("photo.add", array(
            "albumId" => $this->getAlbum($userId),
            "path" => $filePath,
            "description" => $description,
            "addToFeed" => false
        ));
        
        @unlink($filePath);
        
        if ( empty($data["photoId"]) )
        {
            return null;
        }
        
        $photoId = $data["photoId"];
        
        if ( $addToFeed )
        {
            //Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'photo',
                'entityType' => 'photo_comments',
                'entityId' => $photoId,
                'userId' => $userId
            ), array(
                "content" => array(
                    "vars" => array(
                        "status" => $text
                    )
                )
            ));
            OW::getEventManager()->trigger($event);
        }
        
        return $photoId;
    }

    public function beforeContentAdd( OW_Event $event )
    {
        $params = $event->getParams();
        
        if ( $params["type"] != "photo" )
        {
            return;
        }
        
        if ( empty($params["data"]) )
        {
            $event->setData(false);
            
            return;
        }
        
        $creditsParams = array('pluginKey' => 'photo', 'action' => 'add_photo');

        $credits = OW::getEventManager()->call('usercredits.check_balance', $creditsParams);
        if ( $credits === false )
        {
            $event->setData(array(
                "error" => OW::getEventManager()->call('usercredits.error_message', $creditsParams)
            ));
            
            return;
        }
        
        $filePath = null;
        
        if ( !empty($params["data"]["url"]) )
        {
            $filePath = $params["data"]["url"];
        }
        
        if ( !empty($params["data"]["filePath"]) )
        {
            $filePath = $params["data"]["filePath"];
        }
        
        $photoId = $this->addPhoto($params["userId"], $filePath, $params["status"], $params["status"]);
        
        if ( empty($photoId) )
        {
            $event->setData(false);
            
            return;
        }
        
        OW::getEventManager()->call('usercredits.track_action', $creditsParams);
        
        $event->setData(array(
            'entityType' => 'photo_comments',
            'entityId' => $photoId
        ));
    }

    public function renderFormat( OW_Event $event )
    {
        $params = $event->getParams();
        
        if ( $params["format"] != "image" )
        {
            return;
        }
        
        $format = new ATTACHMENTS_CLASS_ImageFormat($params["vars"], $params["format"]);
        $event->setData($format->render());
    }
    
    public function init()
    {
        OW::getEventManager()->bind("feed.render_format", array($this, "renderFormat"));
        
        if ( !$this->isActive() ) return;
        OW::getEventManager()->bind(ATTACHMENTS_CLASS_EventHandler::EVENT_BEFORE_CONTENT_ADD, array($this, "beforeContentAdd"));
    }
}
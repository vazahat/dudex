<?php

class ATTACHMENTS_CLASS_VideoBridge
{
    /**
     * Class instance
     *
     * @var ATTACHMENTS_CLASS_VideoBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return ATTACHMENTS_CLASS_VideoBridge
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function __construct()
    {
        $this->isPluginActive = OW::getPluginManager()->isPluginActive('video');
    }

    public function isActive()
    {
        return $this->isPluginActive && OW::getConfig()->getValue('attachments', 'video_share');
    }

    public function addVideo( $userId, $embed, $title, $description, $thumbnailUrl, $text, $addToFeed = true )
    {
        if ( !$this->isActive() ) return null;

        $title = empty($title) ? $text : $title;
        $title = empty($title) ? '' : $title;
        $description = empty($description) ? '' : $description;

        $clipService = VIDEO_BOL_ClipService::getInstance();

        $clip = new VIDEO_BOL_Clip();
        $clip->title = $title;
        $description = UTIL_HtmlTag::stripJs($description);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $clip->description = $description;
        $clip->userId = $userId;

        $clip->code = UTIL_HtmlTag::stripJs($embed);

        $prov = new VideoProviders($clip->code);

        $privacy = OW::getEventManager()->call(
            'plugin.privacy.get_privacy',
            array('ownerId' => $clip->userId, 'action' => 'video_view_video')
        );

        $clip->provider = $prov->detectProvider();
        $clip->addDatetime = time();
        $clip->status = 'approved';
        $clip->privacy = mb_strlen($privacy) ? $privacy : 'everybody';
        
        $thumbUrl = empty($thumbnailUrl) ? $prov->getProviderThumbUrl($clip->provider) : $thumbnailUrl;
        if ( $thumbUrl != VideoProviders::PROVIDER_UNDEFINED )
        {
            $clip->thumbUrl = $thumbUrl;
        }
        $clip->thumbCheckStamp = time();
        $clipId = $clipService->addClip($clip);
        
        if ( $addToFeed )
        {
            // Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'video',
                'entityType' => 'video_comments',
                'entityId' => $clipId,
                'userId' => $clip->userId
            ), array(
                "content" => array(
                    "vars" => array(
                        "status" => $text
                    )
                )
            ));

            OW::getEventManager()->trigger($event);
        }
        
        return $clipId;
    }
    
    public function beforeContentAdd( OW_Event $event )
    {
        $params = $event->getParams();
        
        if ( $params["type"] != "video" )
        {
            return;
        }
        
        if ( empty($params["data"]) )
        {
            $event->setData(false);
            
            return;
        }
        
        $attachment = $params["data"];
        
        $thumbnailUrl = empty($attachment['thumbnail_url']) ? null : $attachment['thumbnail_url'];
        $title = empty($attachment['title']) ? null : $attachment['title'];
        $description = empty($attachment['description']) ? null : $attachment['description'];
        $embed = $attachment['html'];
        
        $eventParams = array('pluginKey' => 'video', 'action' => 'add_video');
        
        if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === false )
        {
            $event->setData(array(
                "error" => OW::getEventManager()->call('usercredits.error_message', $eventParams)
            ));
            
            return;
        }
        
        $clipId = $this->addVideo($params["userId"], $embed, $title, $description, $thumbnailUrl, $params["status"]);
        
        OW::getEventManager()->call('usercredits.track_action', $eventParams);
        
        $event->setData(array(
            "entityType" => "video_comments",
            "entityId" => $clipId
        ));
    }
    
    public function renderFormat( OW_Event $event )
    {
        $params = $event->getParams();
        
        if ( $params["format"] != "video" )
        {
            return;
        }
        
        $format = new ATTACHMENTS_CLASS_VideoFormat($params["vars"], $params["format"]);
        $event->setData($format->render());
    }

    public function init()
    {
        OW::getEventManager()->bind("feed.render_format", array($this, "renderFormat"));
        
        if ( !$this->isActive() ) return;
        OW::getEventManager()->bind(ATTACHMENTS_CLASS_EventHandler::EVENT_BEFORE_CONTENT_ADD, array($this, "beforeContentAdd"));
    }
}
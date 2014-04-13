<?php

class ATTACHMENTS_CLASS_LinksBridge
{
    /**
     * Class instance
     *
     * @var ATTACHMENTS_CLASS_LinksBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return ATTACHMENTS_CLASS_LinksBridge
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
        $this->isPluginActive = OW::getPluginManager()->isPluginActive('links');
    }

    public function isActive()
    {
        return $this->isPluginActive && OW::getConfig()->getValue('attachments', 'link_share');
    }

    public function addLink( $userId, $href, $title, $description, $thumbnailUrl, $text = null, $addToFeed = true )
    {
        if ( !$this->isActive() ) return null;

        OW::getCacheManager()->clean( array( LinkDao::CACHE_TAG_LINK_COUNT ));
        $service = LinkService::getInstance();

        $url = (mb_ereg_match('^http(s)?:\\/\\/', $href) ? $href : 'http://' . $href);

        $link = new Link();

        $eventParams = array(
            'action' => LinkService::PRIVACY_ACTION_VIEW_LINKS,
            'ownerId' => OW::getUser()->getId()
        );

        $privacy = OW::getEventManager()->getInstance()->call('plugin.privacy.get_privacy', $eventParams);

        if (!empty($privacy))
        {
            $link->setPrivacy($privacy);
        }

        $link->setUserId($userId);

        $link->setTimestamp(time());
        $link->setUrl($url);
        $link->setDescription(strip_tags($description));

        $title = empty($title) ? $text : $title;
        $link->setTitle(strip_tags($title));

        $service->save($link);
        
        if ( $addToFeed )
        {
            $content = array(
                "format" => null,
                "vars" => array(
                    "status" => $text
                )
            );

            if ( !empty($thumbnailUrl) )
            {
                $content["format"] = "image_content";
                $content["vars"]["image"] = $thumbnailUrl;
                $content["vars"]["thumbnail"] = $thumbnailUrl;
            }

            //Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'links',
                'entityType' => 'link',
                'entityId' => $link->getId(),
                'userId' => $link->getUserId()
            ), array(
                "content" => $content
            ));
            OW::getEventManager()->trigger($event);
        }
        
        return $link->id;
    }
    
    public function beforeContentAdd( OW_Event $event )
    {
        $params = $event->getParams();
        
        if ( $params["type"] != "link" )
        {
            return;
        }
        
        if ( empty($params["data"]) )
        {
            $event->setData(false);
            
            return;
        }
        
        $creditsParams = array('pluginKey' => 'links', 'action' => 'add_link');

        $credits = OW::getEventManager()->call('usercredits.check_balance', $creditsParams);
        if ( $credits === false )
        {
            $event->setData(array(
                "error" => OW::getEventManager()->call('usercredits.error_message', $creditsParams)
            ));
            
            return;
        }
        
        $attachment = $params["data"];
        
        $thumbnailUrl = empty($attachment['thumbnail_url']) ? null : $attachment['thumbnail_url'];
        $title = empty($attachment['title']) ? null : $attachment['title'];
        $description = empty($attachment['description']) ? null : $attachment['description'];
        $href = $attachment['href'];
        
        $linkId = $this->addLink( $params["userId"], $href, $title, $description, $thumbnailUrl, $params["status"]);
        
        if ( empty($linkId) )
        {
            $event->setData(false);
            
            return;
        }
        
        OW::getEventManager()->call('usercredits.track_action', $creditsParams);
        
        $event->setData(array(
            'entityType' => 'link',
            'entityId' => $linkId
        ));
    }

    public function init()
    {
        if ( !$this->isActive() ) return;
        
        OW::getEventManager()->bind(ATTACHMENTS_CLASS_EventHandler::EVENT_BEFORE_CONTENT_ADD, array($this, "beforeContentAdd"));
    }
}
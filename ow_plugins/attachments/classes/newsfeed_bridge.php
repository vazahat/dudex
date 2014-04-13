<?php

class ATTACHMENTS_CLASS_NewsfeedBridge
{
    /**
     * Class instance
     *
     * @var ATTACHMENTS_CLASS_NewsfeedBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return ATTACHMENTS_CLASS_NewsfeedBridge
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
        $this->isPluginActive = OW::getPluginManager()->isPluginActive('newsfeed');
    }

    public function isActive()
    {
        return $this->isPluginActive;
    }
    
    
    public function beforeContentAdd( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();
        
        if ( !empty($data) )
        {
            return;
        }
        
        if ( empty($params["status"]) && empty($params["data"]) )
        {
            $event->setData(false);
            
            return;
        }
        
        $attachId = null;
        $content = array();
        
        if ( !empty($params["data"]) )
        {
            $content = $params["data"];
            if( $content['type'] == 'photo' && !empty($content['genId']) )
            {
                $content['url'] = $content['href'] = OW::getEventManager()->call('base.attachment_save_image', array( 'genId' => $content['genId'] ));
                $attachId = $content['genId'];
            }

            if( $content['type'] == 'video' )
            {
                $content['html'] = BOL_TextFormatService::getInstance()->validateVideoCode($content['html']);
            }
        }

        $status = UTIL_HtmlTag::autoLink($params["status"]);
        $out = NEWSFEED_BOL_Service::getInstance()->addStatus(OW::getUser()->getId(), $params['feedType'], $params['feedId'], $params['visibility'], $status, array(
            "content" => $content,
            "attachmentId" => $attachId
        ));

        $event->setData($out);
    }

    public function afterInits()
    {
        OW::getEventManager()->bind(ATTACHMENTS_CLASS_EventHandler::EVENT_BEFORE_CONTENT_ADD, array($this, "beforeContentAdd"));
    }
    
    public function init()
    {
        if ( !$this->isActive() ) return;
        
        OW::getEventManager()->bind(OW_EventManager::ON_PLUGINS_INIT, array($this, "afterInits"));
    }
}
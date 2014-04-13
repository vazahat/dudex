<?php

class ATTACHMENTS_CLASS_EventHandler
{
    const EVENT_BEFORE_CONTENT_ADD = "attachments.before_content_add";
    
    /**
     * Class instance
     *
     * @var ATTACHMENTS_CLASS_EventHandler
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return ATTACHMENTS_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function processAttachment( $userId, $attachment, $text )
    {
        switch ( $attachment['type'] )
        {
            case 'photo':
                
                $filePath = empty($attachment['url']) ? null  : $attachment['url'];

                if ( empty($filePath) )
                {
                    return;
                }

                $title = empty($attachment['title']) ? null : $attachment['title'];

                ATTACHMENTS_CLASS_PhotoBridge::getInstance()->addPhoto($userId, $filePath, $title, $text, false);
                break;

            case 'video':
                $thumbnailUrl = empty($attachment['thumbnail_url']) ? null : $attachment['thumbnail_url'];
                $title = empty($attachment['title']) ? null : $attachment['title'];
                $description = empty($attachment['description']) ? null : $attachment['description'];
                $embed = $attachment['html'];
                ATTACHMENTS_CLASS_VideoBridge::getInstance()->addVideo($userId, $embed, $title, $description, $thumbnailUrl, $text, false);
                break;

            case 'link':
                $thumbnailUrl = empty($attachment['thumbnail_url']) ? null : $attachment['thumbnail_url'];
                $title = empty($attachment['title']) ? null : $attachment['title'];
                $description = empty($attachment['description']) ? null : $attachment['description'];
                $href = $attachment['href'];
                ATTACHMENTS_CLASS_LinksBridge::getInstance()->addLink($userId, $href, $title, $description, $thumbnailUrl, $text, false);
                break;
        }
    }

    public function onCommentAdd( OW_Event $event )
    {
        $params = $event->getParams();

        if ( empty($params['attachment']) )
        {
            return;
        }

        $commentId = $params['commentId'];
        $commentDto = BOL_CommentService::getInstance()->findComment($commentId);

        $this->processAttachment($params['userId'], $params['attachment'], $commentDto->message);
    }
    
    public function init()
    {
        OW::getEventManager()->bind('base_add_comment', array($this, 'onCommentAdd'));
    }
}
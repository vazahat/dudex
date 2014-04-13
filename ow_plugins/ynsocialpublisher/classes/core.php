<?php
/**
 * @author trunglt
 * @package ow_plugins.ynsocialpublisher.classes
 * @since 1.01
 */
class YNSOCIALPUBLISHER_CLASS_Core
{
    /**
     * Singleton instance.
     * @var YNSOCIALPUBLISHER_CLASS_Core
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     * @return YNSOCIALPUBLISHER_CLASS_Core
     */
    public static function getInstance()
    {
        if (self::$classInstance === null)
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public  function getSupportedPluginKeys()
    {
        return array('newsfeed', 'blogs', 'photo', 'event', 'forum', 'groups', 'video', 'links');
    }

    public function getTypesByPluginKey($pluginKey)
    {
        switch($pluginKey)
        {
            case 'newsfeed':
                return array('user-status');
            case 'photo':
                return array('photo_comments', 'multiple_photo_upload');
            case 'blogs':
                return array('blog-post');
            case 'event':
                return array('event');
            case 'forum':
                return array('forum-topic');
            case 'groups':
                return array('group');
            case 'video':
                return array('video_comments');
            case 'links':
                return array('link');
            default:
                return array();
        }
    }

    public function getService($pluginKey)
    {
        switch ($pluginKey)
        {
            case 'newsfeed':
                return NEWSFEED_BOL_Service::getInstance();
            case 'blogs':
                return PostService::getInstance();
            case 'groups':
                return GROUPS_BOL_Service::getInstance();
            case 'event':
                return EVENT_BOL_EventService::getInstance();
            case 'links':
                return LinkService::getInstance();
            case 'video':
                return VIDEO_BOL_ClipService::getInstance();
            case 'forum':
                return FORUM_BOL_ForumService::getInstance();
            case 'photo':
                return PHOTO_BOL_PhotoService::getInstance();
        }
    }

    public function getEntity($pluginKey, $entityType, $entityId)
    {
        $entityId = (int)$entityId;
        $service = $this->getService($pluginKey);
        $entity = null;
        switch ($pluginKey)
        {
            case 'newsfeed':
                $entity = $service->findStatusDtoById($entityId);
                break;
            case 'blogs':
                $entity = $service->findById($entityId);
                break;
            case 'groups':
                $entity = $service->findGroupById($entityId);
                break;
            case 'event':
                $entity = $service->findEvent($entityId);
                break;
            case 'links':
                $entity = $service->findById($entityId);
                break;
            case 'video':
                $entity = $service->findClipById($entityId);
                break;
            case 'forum':
                $entity = $service->findTopicById($entityId);
                break;
            case 'photo';
                $entity = $service->findPhotoById($entityId);
                break;
            default:
                break;

        }
        return $entity;
    }

    public function getMediaType($entityType, $entityId) {
        switch ($entityType)
        {
            case 'user-status':
                return 'a status';
            case 'photo_comments':
            case 'multiple_photo_upload':
                return 'a photo';
            case 'blog-post':
                return 'a blog';
            case 'event':
                return 'an event';
            case 'forum-topic':
                return 'a topic';
            case 'group':
                return 'a group';
            case 'video_comments':
                return 'a video';
            case 'link':
                return 'a link';
            default:
                return 'an item';
        }
    }

    public function getTitle($pluginKey, $entityType, $entityId)
    {
        $entity = $this->getEntity($pluginKey, $entityType, $entityId);
        if (!$entity)
        {
            return '';
        }
        switch($pluginKey)
        {
            case 'newsfeed':
            	return UTIL_HtmlTag::stripTags($entity->status);
                //return $entity->status;
            case 'photo':
                $title = $entity->description;
                if (empty($title))
                {
                    $photoData = $this->getPhotoFeedData($entityType, $entityId);
                    $title = $photoData['albumName'];
                }
                return $title;
            case 'blogs':
            case 'event':
            case 'forum':
            case 'groups':
            case 'video':
            case 'links':
                return $entity->title;
            default:
                return '';
        }

    }
    /*
     * @var $params = array(
     *     'pluginKey' => '',
     *     'pluginType' => '',
     *     'entityId' => ''
     * )
     */
    public function getDefaultStatus($pluginKey, $entityType, $entityId)
    {
        //list($pluginKey, $pluginType, $entityId) = $params;
        $language = OW::getLanguage();

        $userId = OW::getUser()->getId();
        $userName = BOL_UserService::getInstance()->getUserName($userId);

        $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();

        // get media type
        $mediaType = $this->getMediaType($entityType, $entityId);
        // get title
        $title = $this->getTitle($pluginKey, $entityType, $entityId);
        // site title
        $siteTitle = OW::getConfig()->getValue('base', 'site_name');

        // special case for photo plugin
        if ($pluginKey == 'photo')
        {
            $photoData = $this->getPhotoFeedData($entityType, $entityId);
            list($photoNumber, $albumName) = array($photoData['photoNumber'], $photoData['albumName']);
            if ($photoNumber <= 9)
            {
                $photoTemplate = $language->text('ynsocialpublisher', "photo_template");
            }
            else
            {
                $photoTemplate = $language->text('ynsocialpublisher', "photo_more_template");
            }
            return sprintf($photoTemplate, $userName, $photoNumber, $albumName, $siteTitle);
        }

        if (!empty($title))
        {
            $strTemplate = $language->text('ynsocialpublisher', "status_title");
            return sprintf($strTemplate, $userName, $language->text('ynsocialpublisher', $mediaType), $title, $siteTitle);

        }
        else
        {
            $strTemplate = $language->text('ynsocialpublisher', "status_title_empty");
            return sprintf($strTemplate, $userName, $language->text('ynsocialpublisher', $mediaType), $siteTitle);
        }
    }

    public function getNewsfeedLinkData($entityId, $type)
    {
    	$feedData = $this->getFeedData($entityId);
    	switch ($type)
    	{
    		case 'title':
    			return UTIL_HtmlTag::stripTags($feedData['data']['status']);
    			break;
    		case 'photo':
    			$photoUrl = '';
    			if (isset($feedData['attachment']['oembed']['thumbnail_url']))
    			{
    				$photoUrl = $feedData['attachment']['oembed']['thumbnail_url'];
    			}
    			return $photoUrl;
    			break;	
			default:
				break;    			
    	}
    }
    
    public function getPhotoFeedData($entityType, $entityId)
    {
        $newsfeedService = $this->getService('newsfeed');
        $photoFeed = $newsfeedService->findAction($entityType, $entityId);
    
        $photoFeedData = json_decode($photoFeed->data, true);
        $string = $photoFeedData['string'];
        //print_r($string);die;
        preg_match("((.+)([0-9]+)(.+)<a.*?href=[\"']([^\"']+)[\"'][^>]?>(.+)</a>)i", $string, $matches);
        // img match
        $content = $photoFeedData['content'];

	    if (!isset($matches[5]))
        {
            $photo = $this->getEntity('photo', $entityType, $entityId);
            $album = PHOTO_BOL_PhotoAlbumService::getInstance()->findAlbumById($photo->albumId);
            $albumName = $album->name;
        }
        else
        {
            $albumName = $matches[5];
        }
        return array(
                'photoNumber' => isset($matches[2])?$matches[2]:1,
                'albumUrl' => isset($matches[4])?$matches[4]:'',
                'albumName' => $albumName,
                //'photoUrls' => $photoImgSrc
                );
    }

    public function getUrl($pluginKey, $entityType, $entityId)
    {
        $pluginService = $this->getService($pluginKey);
        $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();

        switch($pluginKey)
        {
            case 'newsfeed':
                // return user profile
                return BOL_UserService::getInstance()->getUserUrl(OW::getUser()->getId());
            case 'photo':
                // return album id
                $photoData = $this->getPhotoFeedData($entityType, $entityId);
                list($photoNumber, $albumUrl) = array($photoData['photoNumber'], $photoData['albumUrl']);
                if ($photoNumber == 1)
                {
                    return OW::getRouter()->urlForRoute('view_photo', array('id' => $entityId));
                }
                else
                {
                    return $albumUrl;
                }
            case 'blogs':
                return OW::getRouter()->urlForRoute('user-post', array('id' => $entityId));
            case 'event':
                return OW::getRouter()->urlForRoute('event.view', array('eventId' => $entityId));
            case 'forum':
                return OW::getRouter()->urlForRoute('topic-default', array('topicId' => $entityId));
            case 'groups':
                $group = $core->getEntity($pluginKey, $entityType, (int)$entityId);
                return $pluginService->getGroupUrl($group);
            case 'video':
                return OW::getRouter()->urlForRoute('view_clip', array('id' => $entityId));
            case 'links':
                return OW::getRouter()->urlForRoute('link', array('id' => $entityId));
            default:
                return '';
        }
    }

    public function getFeedData($entityId)
    {
    	$entityId = (int)$entityId;
        $newsfeedService = $this->getService('newsfeed');
		
        $feed = $newsfeedService->findAction('user-status', $entityId);
		print_r($feed);die;
        $feedData = json_decode($feed->data, true);
        return $feedData;
    }

    public function getPhotoUrlForProvider($pluginKey, $entityType, $entityId)
    {
        $pluginService = $this->getService($pluginKey);
        $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();

        $photoUrl = '';
        switch($pluginKey)
        {
            case 'newsfeed':
				$entityId = (int)$entityId;
        		$newsfeedService = $this->getService('newsfeed');
        		$feed = $newsfeedService->findAction('user-status', $entityId);
        		$feedData = json_decode($feed->data, true);
                if (isset($feedData['attachment']['oembed']['thumbnail_url']))
                {
                	$photoUrl = $feedData['attachment']['oembed']['thumbnail_url'];
                }
                else if (isset($feedData['attachment']['oembed']['url']))
                {
                	$photoUrl = $feedData['attachment']['oembed']['url'];
                }
                break;
            case 'photo':
                return $pluginService->getPhotoPreviewUrl($entityId);
            case 'blogs':
            	$userId = OW::getUser()->getId();
                $photoUrl = BOL_AvatarService::getInstance()->getAvatarUrl($userId);
                if ( empty($photoUrl) )
                {
                    $photoUrl = BOL_AvatarService::getInstance()->getDefaultAvatarUrl();
                }
                break;
            case 'event':
            	$entity = $core->getEntity($pluginKey, $entityType, $entityId);
                $photoUrl = $entity->getImage() ? $pluginService->generateImageUrl($entity->getImage(), true) : $pluginService->generateDefaultImageUrl();
                break;
            case 'forum':
                break;
            case 'groups':
            	$entity = $core->getEntity($pluginKey, $entityType, $entityId);
                $photoUrl = $pluginService->getGroupImageUrl($entity);
                break;
            case 'video':
                $photoUrl = $pluginService->getClipThumbUrl($entityId);
                break;
            case 'links':
                break;
            default:
                break;
        }
        return $photoUrl;
    }

    public function getPostData($pluginKey, $entityId, $entityType, $providers, $status) {
        $postData = array();
        if (count($providers) == 0)
        {
            return $postData;
        }
        $core = YNSOCIALPUBLISHER_CLASS_Core::getInstance();
        // get media type
        $mediaType = $this->getMediaType($entityType, $entityId);
        // share link
        $shareLink = $core->getUrl($pluginKey, $entityType, $entityId);

        // title
        $title = $core->getTitle($pluginKey, $entityType, $entityId);

        // site title
        $siteTitle = OW::getConfig()->getValue('base', 'site_name');
        // user id
        $userId = OW::getUser()->getId();
        // photo url for provider
        $photoUrl = '';

        foreach ($providers as $provider)
        {
            $coreBridge = new YNSOCIALBRIDGE_CLASS_Core();
            $obj = $coreBridge -> getInstance($provider);
            $values = array(
                    'service' => $provider,
                    'userId' => $userId
            );
            //$tokenDto = $obj -> getToken($values);
            $accessToken = null;
            $secretToken = null;
            if (!empty($_SESSION['socialbridge_session'][$provider]))
            {
                $accessToken = $_SESSION['socialbridge_session'][$provider]['access_token'];
                $secretToken = isset($_SESSION['socialbridge_session'][$provider]['secret_token'])?$_SESSION['socialbridge_session'][$provider]['secret_token']:'';

            }
            /*elseif($tokenDto)
            {
                $accessToken = $tokenDto->accessToken;
                $secretToken = $tokenDto->secretToken;
            }*/
            if (!empty($accessToken))
            {
                switch ($provider) {
                    case 'twitter':
                            $postData[$provider] = array(
                                    'access_token' => $accessToken,
                                    'secret_token' => $secretToken,
                                    'message' => $status,
                                    'link' => $shareLink
                            );
                            // for type == link --- later

                        break;
                    case 'facebook':
                    case 'linkedin':
                            $photoUrl = $this->getPhotoUrlForProvider($pluginKey, $entityType, $entityId);
                            if (empty($photoUrl) || ($photoUrl == 'undefined')) {
                            	$userId = OW::getUser()->getId();
                            	$photoUrl = BOL_AvatarService::getInstance()->getAvatarUrl($userId);
                            	if ( empty($photoUrl) )
                            	{
                            		$photoUrl = BOL_AvatarService::getInstance()->getDefaultAvatarUrl(2);
                            	}
                            }                            
                            if ($provider == 'facebook') {
                                $postData[$provider] = array(
                                        'access_token' => $accessToken,
                                        'link' => $shareLink,
                                        'name' => $title,
                                        'caption'=> $status,
                                        'message' => $title,
                                        'description' => $siteTitle
                                );
                                if (empty($title))
                                {
	                                $postData[$provider] = array(
	                                        'access_token' => $accessToken,
	                                        'link' => $shareLink,
	                                        'name' => $status,
	                                        'caption'=> $title,
	                                        'message' => $title,
	                                        'description' => $siteTitle
	                                );
                                }
                                $postData[$provider]['picture'] = $photoUrl;
                            }
                            elseif ($provider == 'linkedin') {
                                $postData[$provider] = array(
                                        'access_token' => $accessToken,
                                        'secret_token' => $secretToken,
                                        'comment' => '',
                                        'title' => $status,
                                        'submitted-url' => $shareLink,
                                        'description' => $title
                                );
                                $postData[$provider]['submitted-image-url'] = $photoUrl;
                            }

                        break;
                    default:
                        break;
                }
            }
        }
        return $postData;
    }
}
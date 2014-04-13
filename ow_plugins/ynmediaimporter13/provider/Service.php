<?php

/**
 * import first class.
 *
 */
require_once YNMEDIAIMPORTER_PROVIDER_PATH . '/Abstract.php';

/**
 * this should be used as singleton class only.
 * call via static only.
 * Ynmediaimporter::getInstance();
 */
class Ynmediaimporter
{

    protected static $_log;

    /**
     * do not instance any of this class
     * ignore constructor
     */
    private function __construct()
    {

    }

    /**
     * @var Ynmediaimporter
     */
    static protected $_instance = null;

    /**
     * Zend Cache instance
     * @var Zend_Cache
     */
    static protected $_cache = null;

    /**
     * check provider.
     * list of providers.
     * @var array
     */
    static protected $_providers = array();

    /**
     * @return Ynmediaimporter
     */
    static public function getInstance()
    {
        if (null == self::$_instance)
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * get provider service.
     * name should be lowercase
     * @param string $name
     * @param bool $singleton is used to instance only one, default  = 1, strong
     * recommend to save performance.
     * @return Ynmediaimporter_Proider_Abstract
     * @throws Exception when provider not found.
     */
    static public function getProvider($name, $singleton = true)
    {
        $name = strtolower(trim($name));

        if ($singleton && isset(self::$_providers[$name]))
        {
            return self::$_providers[$name];
        }

        $file = YNMEDIAIMPORTER_PROVIDER_PATH. '/' . ucfirst($name) . '.php';
        $class = 'Ynmediaimporter_Provider_' . ucfirst($name);

        if (file_exists($file))
        {
            require_once $file;

            // check that class is exists!
            if (class_exists($class, false))
            {
                return self::$_providers[$name] = new $class;
            }
        }

        throw new Exception("service $name  has not supported.");
    }

    /**
     * @param string|array $data
     * @param string|array $message
     * @param string $filename  a part of file name under
     * YNMEDIAIMPORTER_LOG_PATH
     * @return void.
     */
    static public function log($data, $message = null)
    {
        if (false == YNMEDIAIMPORTER_DEBUG)
        {
            return;
        }

        if (null == self::$_log)
        {
            $file = YNMEDIAIMPORTER_LOG_PATH . '/importer.log';
            self::$_log = new Zend_Log(new Zend_Log_Writer_Stream($file));
        }

        if (!is_string($data))
        {
            $data = var_export($data, 1);
        }

        if (null == $message)
        {
            $message = 'info';
        }
        else
        if (!is_string($message))
        {
            $message = var_export($message, 1);
        }

        self::$_log -> log($message . PHP_EOL . $data, Zend_Log::INFO);
    }

    static public function getCache()
    {
    	if (null == self::$_cache)
        {
            self::$_cache = OW::getCacheManager();
        }
    	
        return self::$_cache;

    }
    
    static public function setupScheduler($photos = null, $albums = null, $album_id = 0, $params = array())
    {
        $user_id = OW::getUser()->getId();
        
        $aScheduler = array();
        $aScheduler['owner_id'] = $user_id;
        $aScheduler['owner_type'] = 'user';
        $aScheduler['user_id'] = $user_id;
		$aScheduler['params'] = json_encode($params);
		
        $scheduler = YNMEDIAIMPORTER_BOL_SchedulerService::getInstance()->addScheduler($aScheduler);
        $schedulerId = $scheduler -> id;
        
        $nodeId = array();
        $album_id = intval($album_id);

        if (is_array($albums) && !empty($albums))
        {

            foreach ($albums as $id)
            {
                $nodeId[] = "'" . $id . "'";
            }
        }

        if (is_array($photos) && !empty($photos))
        {
            foreach ($photos as $id)
            {
                $nodeId[] = "'" . $id . "'";
            }
        }

        $nodeId = implode(',', $nodeId);
        
        $nodeTableName = OW_DB_PREFIX . 'ynmediaimporter_nodes';
        $sql = "UPDATE `{$nodeTableName}` SET ";
        $sql .= "`scheduler_id` = {$schedulerId}, ";
        $sql .= "`user_aid` = {$album_id}, ";
        $sql .= "`status` = 1 "; // in schdule
		$sql .= "where nid IN ($nodeId) and user_id = '$user_id' and `status` < 3";
		
		OW::getDbo()->query($sql);
        return $schedulerId;
    }

    static public function getValidDir()
    {
        $dir = Ow::getPluginManager()->getPlugin('ynmediaimporter')->getUserFilesDir();

        foreach (array(date('Y'),date('m'), date('d')) as $sub)
        {
            $dir = $dir . $sub . '/';
            if (!realpath($dir))
            {
                if (!mkdir($dir, 0777))
                {
                    throw new Exception("$dir is not writeable or is not exists!");
                }
            }
        }
        return $dir;
    }

    static public function saveImageFromUrl($sLink, $filePath)
    {
    	$source = file_get_contents(trim($sLink));
    	$savefile = fopen($filePath, 'w');
    	fwrite($savefile, $source);
    	fclose($savefile);
    	return $filePath;
    }
    
    static function handleShutdown($schedulerId)
    {
    	return array(
    			'remain' => 1,
    			'scheduler_id' => $schedulerId,
    	);

    	if(function_exists('error_get_last'))
    	{
    		if (is_array($error = error_get_last()))
    		{
    			$i = ob_get_level();
    			while($i > 0)
    			{
    				ob_clean();
    				$i--;
    			}
    			var_dump($error);
    			die;
    		}
    	}
    }
    
    static public function processScheduler($scheduler, $user_aid = null, $limit = 10, $sendNotification = false, $sendActivity = true)
    {
    	//register_shutdown_function(array('Ynmediaimporter','handleShutdown'), $scheduler -> id);
    	//ini_set('max_execution_time',1);
    	
    	$movedCount = 0;
    	$movedArray = array();
    	$photos = array();
    	
        $schedulerId = $scheduler -> id;
        $userId = $scheduler -> user_id;
		$user = BOL_UserService::getInstance()->findUserById($userId);
        $album = null;
       
        $example = new OW_Example();
        $example->andFieldEqual('scheduler_id', $schedulerId);
        $example->andFieldLessThan('status', '3');
        $example->andFieldGreaterThan('status', '0');
        $example->setLimitClause(0, $limit);
       
        if ($user_aid)
        {
			$example->andFieldEqual('user_aid', intval($user_aid));
        }

        $nodeList = YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->findListByExample($example);
        $order = 0;
        
        foreach ($nodeList as $node)
        {
            if ('photo' == $node -> media && $node -> user_aid > 0)
            {
                $album = PHOTO_BOL_PhotoAlbumDao::getInstance()->findById($node -> user_aid);
                if (!is_object($album))
                    continue;

                //download file
                $dir = Ynmediaimporter::getValidDir();
                $file = $dir . $node -> getUUID();
                
                $privacy = OW::getEventManager()->call(
                		'plugin.privacy.get_privacy',
                		array('ownerId' => $album->userId, 'action' => 'photo_view_album')
                );
                
                $photo = new PHOTO_BOL_Photo();
                $photo->description = '';
                $photo->albumId = $album->id;
                $photo->addDatetime = time();
                $photo->status = 'approved';
                $photo->hasFullsize = '1';
                $photo->privacy = mb_strlen($privacy) ? $privacy : 'everybody';
                
                $source =self::saveImageFromUrl($node -> getDownloadFilename(), $file);
                $photo = self::__savePhoto($photo, $source, $userId);
                
                if ($photo)
                {
                	$photos[] = $photo;
                	$movedArray[] = array('addTimestamp' => time(), 'photoId' => $photo->id);
                	$movedCount++;
                }
                
                $node -> status = 3;
                YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->save($node);
            }
            else if ( in_array($node -> media, array('album','photoset','gallery')) && 0 == $node -> user_aid )
            {
            	
                // create new albums for this roles
                $album = self::createPhotoAlbums($scheduler, $node);
                
                // setup album and node.
                // update all sub node of current scheduler to this albums.
                $example = new OW_Example();
                $example->andFieldEqual('scheduler_id', $schedulerId);
                $example->andFieldEqual('aid', $node -> aid);
                
                $nodeTemp = YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->findObjectByExample($example);
                $nodeTemp->user_aid = $album -> id;
                $nodeTemp->status = '1';
                YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->save($nodeTemp);
                
                $node -> user_aid = $album -> id;
                $node -> status = 1;
                YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->save($node);
                self::processScheduler($scheduler, $album -> id, 10, 0, 0);
                break;
                // force process this album to escape no value style.

            }
            
        }
        
        $example = new OW_Example();
        $example->andFieldEqual('scheduler_id', $schedulerId);
        $example->andFieldEqual('media', 'photo');
        $example->andFieldLessThan('status', '3');
        $remain = intval(YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->countByExample($example));
		
        // all scheduler is completed. send notification to users
        if (is_object($album) && $remain == 0)
        {
        	// Send notification
            if ($sendNotification)
            {
            	$actor = array(
            			'username' => BOL_UserService::getInstance()->getUserName($userId) , 
            			'name' => BOL_UserService::getInstance()->getDisplayName($userId),
            			'url' => BOL_UserService::getInstance()->getUserUrl($userId)
            	);
            	$avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($userId));
            	
                $event = new OW_Event('notifications.add', array(
			        'pluginKey' => 'ynmediaimporter',
			        'entityType' => 'ynmediaimporter_album',
			        'entityId' => (int) $album->id,
			        'action' => 'ynmediaimporter_album-added',
			        'userId' => $album->userId,
			        'time' => time()
			    ), array(
			        'avatar' => $avatars[$userId],
			        'string' => array(
			            'key' => 'ynmediaimporter+added_album_notification_string',
			            'vars' => array(
			                'actor' => $actor['name'],
			                'actorUrl' => $actor['url'],
			                'title' => $album->name,
			                'url' => OW::getRouter()->urlForRoute('photo_user_album', array('user' => $actor['username'],'album' => $album->id))
			            )
			        ),
			        'content' => $album->name,
			        'url' => OW::getRouter()->urlForRoute('photo_user_album', array('user' => $actor['username'],'album' => $album->id))
			    ));
			
			    OW::getEventManager()->trigger($event);
            }

            if ($sendActivity)
            {
            	self::__sendActivity($album, $photos, $movedArray, $movedCount);
            }
        }

        $scheduler -> status = ($remain == 0) ? 3 : 1;
        $scheduler -> last_run = time();
        YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->save($scheduler);
        
        // and of process rec count all
        $tableName = OW_DB_PREFIX . 'ynmediaimporter_nodes';
        $sql = "SELECT
				  album.id,
				  (SELECT
				     COUNT( * )
				   FROM {$tableName} AS photo
				   WHERE photo.media = 'photo'
				       AND photo.aid = album.id
				       AND photo.status = 1) AS remaining
				FROM `{$tableName}` album
				WHERE album.media <> 'photo'
				    AND album.status = 1
				GROUP BY album.id
				HAVING remaining = 0";

        $completedList = OW::getDbo()->queryForColumnList($sql);
        
        if($completedList){
           $sql = "UPDATE `{$tableName}` SET `status` = '3' where `id` IN (" .implode(',',$completedList). ")";
           OW::getDbo()->query($sql);
        }
        
        return array(
            'remain' => $remain,
            'scheduler_id' => $schedulerId,
        );
    }

    static public function __sendActivity($album, $photos, $movedArray, $movedCount)
    {
    	$userId = $album->userId; 
    	$photoService = PHOTO_BOL_PhotoService::getInstance();
    	if ( !empty($movedArray) )
    	{
    		$event = new OW_Event('plugin.photos.add_photo', $movedArray);
    		OW::getEventManager()->trigger($event);
    	}
    	
    	$albumUrl = OW::getRouter()->urlForRoute('photo_user_album', array(
    			'user' => BOL_UserService::getInstance()->getUserName($userId),
    			'album' => $album->id
    	));
    	
    	if ( $movedCount )
    	{
    		if ( $movedCount == 1 )
    		{
    			//Newsfeed
    			$event = new OW_Event('feed.action', array(
    					'pluginKey' => 'photo',
    					'entityType' => 'photo_comments',
    					'entityId' => $photos[0]->id,
    					'userId' => $userId
    			));
    			OW::getEventManager()->trigger($event);
    		}
    		else
    		{
    			$content = '';
    			$counter = 0;
    			$photos = array_reverse($photos);
    	
    			foreach ( $photos as $photo )
    			{
    				if ( $counter == 5 )
    				{
    					$content .= '<span class="ow_remark" style="float: left; display: inline-block; padding-top: 65px"><a class="photo_view_more" href="'.$albumUrl.'"> '
    							. OW::getLanguage()->text('photo', 'feed_more_items', array('moreCount' => $movedCount-5)) . "</a></span>";
    					break;
    				}
    				$id = $photo->id;
    				$pageUrl = $url = OW::getRouter()->urlForRoute('view_photo', array('id' => $id));
    				$content .= '<a style="float: left; margin: 0px 4px 4px 0px;" href="' . $pageUrl . '"><img src="' . $photoService->getPhotoUrl($id, true) . '" height="80" /></a> ';
    				$counter++;
    			}
    	
    			$content = '<div class="clearfix">'.$content.'</div>';
    	
    			$description = $photos[0]->description;
    			$diff = false;
    			if ( !mb_strlen($description) )
    			{
    				$diff = true;
    			}
    			else
    			{
    				foreach ( $photos as $photo )
    				{
    					if ( $photo->description != $description )
    					{
    						$diff = true;
    						break;
    					}
    				}
    			}
    	
    			//Newsfeed
    			$albumName = UTIL_String::truncate(strip_tags($album->name), 25, '...');
    	
    			if ( $diff )
    			{
    				$title = OW::getLanguage()->text('photo', 'feed_multiple_descriptions',
    						array('number' => $movedCount, 'albumUrl' => $albumUrl, 'albumName' => $albumName)
    				);
    			}
    			else
    			{
    				$title = UTIL_String::truncate(strip_tags($description), 100, '...');
    			}
    	
    			$event = new OW_Event('feed.action', array(
    					'pluginKey' => 'photo',
    					'entityType' => 'multiple_photo_upload',
    					'entityId' => $photos[0]->id,
    					'userId' => $userId
    			), array(
    					'string' => $title,
    					'features' => array('likes'),
    					'content' => $content,
    					'view' => array('iconClass' => 'ow_ic_picture')
    			));
    			OW::getEventManager()->trigger($event);
    		}
    	}
    }
    
    static public function createPhotoAlbums($scheduler, $node)
    {
    	$album = new PHOTO_BOL_PhotoAlbum();
    	$album->name = empty($node -> title) ? 'Untitled Album' : $node -> title;
    	$album->userId = $node -> owner_id;
    	$album->createDatetime = time();
    	
    	$albumId = PHOTO_BOL_PhotoAlbumService::getInstance()->addAlbum($album);
		return $album;
    }

    /**
     * clear cache for current session
     */
    static public function clearCache()
    {
        try
        {
            $ssid = session_id();
            if ($ssid)
            {
                self::getCache() -> remove($ssid);
            }
            return 1;
        }
        catch(Exception $e)
        {
            return 0;
        }
        return 1;
    }

    static public function resetAll()
    {
        $params = array();

        if (isset($_SESSION['YNMEDIAIMPORTER']))
        {
            unset($_SESSION['YNMEDIAIMPORTER']);
        }

        if (isset($_SESSION[YNMEDIAIMPORTER_SSID]) && !empty($_SESSION[YNMEDIAIMPORTER_SSID]))
        {
            $params['ssid'] = $_SESSION[YNMEDIAIMPORTER_SSID];
            file_get_contents(YNMEDIAIMPORTER_CENTRALIZE_HOST . '/index/reset?' . http_build_query($params));
        }
        return;
    }

    static public function addTemporaryPhoto( $source, $userId, $order )
    {
    	$tmpPhoto = new PHOTO_BOL_PhotoTemporary();
    	$tmpPhoto->userId = $userId;
    	$tmpPhoto->addDatetime = time();
    	$tmpPhoto->hasFullsize = 0;
    	$tmpPhoto->order = $order;
    	$photoTemporaryDao = PHOTO_BOL_PhotoTemporaryDao::getInstance();
    	$photoTemporaryDao->save($tmpPhoto);
    
    	$preview = $photoTemporaryDao->getTemporaryPhotoPath($tmpPhoto->id, 1);
    	$main = $photoTemporaryDao->getTemporaryPhotoPath($tmpPhoto->id, 2);
    	$original = $photoTemporaryDao->getTemporaryPhotoPath($tmpPhoto->id, 3);
    
    	$config = OW::getConfig();
    	$width = $config->getValue('photo', 'main_image_width');
    	$height = $config->getValue('photo', 'main_image_height');
    	$previewWidth = $config->getValue('photo', 'preview_image_width');
    	$previewHeight = $config->getValue('photo', 'preview_image_height');
    
    	try {
    		$image = new UTIL_Image($source);
    
    		$mainPhoto = $image
    		->resizeImage($width, $height)
    		->saveImage($main);
    
    		if ( (bool) $config->getValue('photo', 'store_fullsize') && $mainPhoto->imageResized() )
    		{
    			$originalImage = new UTIL_Image($source);
    			$res = (int) $config->getValue('photo', 'fullsize_resolution');
    			$res = $res ? $res : 1024;
    			$originalImage
    			->resizeImage($res, $res)
    			->saveImage($original);
    
    			$tmpPhoto->hasFullsize = 1;
    			$photoTemporaryDao->save($tmpPhoto);
    		}
    
    		$mainPhoto
    		->resizeImage($previewWidth, $previewHeight, true)
    		->saveImage($preview);
    	}
    	catch ( WideImage_Exception $e )
    	{
    		$photoTemporaryDao->deleteById($tmpPhoto->id);
    		return false;
    	}
    
    	return true;
    }
    
    static public function __savePhoto($photo, $source, $userId)
    {   
    	$photoTmpService = PHOTO_BOL_PhotoTemporaryService::getInstance();
    	$photoTmpService->deleteUserTemporaryPhotos($userId);
    	self::addTemporaryPhoto($source, $userId, '0');
    	$tmpList = $photoTmpService->findUserTemporaryPhotos($userId, 'order');
		
    	if ( !$tmpList )
    	{
    		$resp = array('result' => false, 'msg' => OW::getLanguage()->text('photo', 'photo_upload_error'));
    		exit(json_encode($resp));
    	}
    	
    	$tmpPhoto = end($tmpList);
       		
		$tmpId = $tmpPhoto['dto']->id;
		if ( $tmpId )
		{
    		$eventParams = array('pluginKey' => 'photo', 'action' => 'add_photo');
    		$credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);
    		if ( $credits === false )
    		{
    			$resp = array('result' => false, 'msg' => OW::getEventManager()->call('usercredits.error_message', $eventParams));
    			exit(json_encode($resp));
    		}
    		$photo = $photoTmpService->moveTemporaryPhoto($tmpId, $photo->albumId, '', '');
    		if ( $credits === true )
    		{
    			OW::getEventManager()->call('usercredits.track_action', $eventParams);
    		}

		}
    	return $photo;
    }

}

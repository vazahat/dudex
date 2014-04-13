<?php 

class YNMEDIAIMPORTER_CTRL_Importer extends OW_ActionController
{
	public function index()
	{
// 		DEBUG TIMEOUT ISSUE
// 		$scheduler = YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->findById(1);
// 		$result = Ynmediaimporter::processScheduler($scheduler, 0, 10, 1, 1);
// 		exit;
		
		$this->setPageTitle(OW::getLanguage()->text("ynmediaimporter", "index_page_title")); 
        $this->setPageHeading(OW::getLanguage()->text("ynmediaimporter", "index_page_heading")); 
        $configs =  OW::getConfig()->getValues('ynmediaimporter');
        $iframeurl = (isset($_REQUEST['iframeurl'])) ? $_REQUEST['iframeurl'] : '';
        $iframeurlDecode = base64_decode($iframeurl);
        
        //$serviceNames = array('facebook','picasa','flickr','instagram','yfrog');
        $serviceNames = array('facebook','picasa','flickr','instagram');
        $services = array();
        $hasSocialBridge = OW::getPluginManager()->isPluginActive('ynsocialbridge');
        $hasFacebookApp = $this->checkFacebookApp();
        $langObj = OW::getLanguage();
        
        for ($i = 0; $i < count($serviceNames); $i++)
        {
        	$services[$i]['name'] = $serviceNames[$i];
        	$services[$i]['img_src'] = OW::getPluginManager()->getPlugin('ynmediaimporter')->getStaticUrl().'img/service/' . $serviceNames[$i] . '.jpg';
        	$settingName = sprintf('enable_%s', $serviceNames[$i]);
        	$services[$i]['enable'] = $configs[$settingName];
        	$services[$i]['lang'] = array(
        			'connect' => $langObj->text("ynmediaimporter", "connect_to_".$serviceNames[$i]),
        			'discover' => $langObj->text("ynmediaimporter", "discover_".$serviceNames[$i])
        			);
        	
        	$services[$i]['obj_service'] = Ynmediaimporter::getProvider($serviceNames[$i]);
        }
        
        $this->assign("services", $services);
        $this->assign("hasSocialBridge", $hasSocialBridge);
        $this->assign("hasFacebookApp", $hasFacebookApp);
        $this->assign("iframeurl", $iframeurl);
        $this->assign("iframeurlDecode", $iframeurlDecode);
        $this->assign("description", OW::getLanguage()->text("ynmediaimporter", "index_page_description"));
	}
	
	public function checkFacebookApp()
	{
		$name = 'facebook';
		$fbSettings = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance()->getConfig($name);
		if (!is_object($fbSettings))
		{
			return false;
		}
		$api_params = unserialize($fbSettings -> apiParams);
		$appId = $api_params['key'];
		$secret = $api_params['secret'];
		if ($appId && $secret)
			return true;
		return false;
	}
	
	public function callback($params)
	{
		if (!($_REQUEST['task']) && $params['service'] == 'facebook' ) {
			$this->redirect(OW::getRouter()->urlForRoute('ynmediaimporter.index'));
			exit;
		}
    	
    	if ( empty($_REQUEST) || $_REQUEST['error'] ){
    		$this->redirect(OW::getRouter()->urlForRoute('ynmediaimporter.index'));
    		exit;
    	}

        /**
         * @var string
         */
        $service = $params['service'];

        /**
         * get provider
         * @var Ynmediaimporter_Provider_Abstract
         */
        $provider = Ynmediaimporter::getProvider($service);
		
        /**
         * process connect
         */
        $provider -> doConnect($_POST);
        /**
         * @var string
         */
        $action = $provider -> supportedMethod('getAlbums') ? 'albums' : 'photos';

        /**
         *
         * @var string
         */
		$url = OW::getRouter()->urlForRoute("ynmediaimporter.{$service}");
        /**
         * redirect to next page.
         */
		$this -> redirect($url);
    }
	
	
	public function connect($params)
    {
    	
        $service = $params['service'];
        /**
         * get provider
         */
        $provider = Ynmediaimporter::getProvider($service);
		
        /**
         * get remote url,
         */
        $callBackUrl = OW::getRouter()->urlForRoute('ynmediaimporter.callback', array('service' => $service));
        
        // callback url.
        $url = $provider -> getAuthUrl($callBackUrl);
        $this->redirect($url);
    }
    
	public function disconnect($params)
    {
        $service = $params['service'];

        $provider = Ynmediaimporter::getProvider($service);

        $provider -> doDisconnect($_REQUEST);

        /**
         *
         * @var string
         */
        $url = OW::getRouter()->urlForRoute("ynmediaimporter.index");

        $url .= '?iframeurl=' . $provider -> getLogoutIframeUrl();

        /**
         * redirect to next page.
         */
		$this -> redirect($url);
    }
    
    public function getdata()
    {
		// ini_set('display_startup_errors', 0);
		// ini_set('display_errors', 0);
		// ini_set('error_reporting', E_ALL);
		
		$format = isset($_REQUEST['format']) ? $_REQUEST['format'] : null;
		$service = isset($_REQUEST['service']) ? $_REQUEST['service'] : 'facebook';
		$cache = isset($_REQUEST['cache']) ? $_REQUEST['cache'] : 1;
		
		if (isset($_GET['remove-cache']) && $_GET['remove-cache'])
		{
		    Ynmediaimporter::clearCache();
		    unset($_GET['remove-cache']);
		}
		$provider = Ynmediaimporter::getProvider($service);
		
		$data = array(
		    'html' => '',
		    'message' => ''
		);
		
		try
		{
		    list($items, $params, $media) = $provider -> getData($_GET, $cache);
		    $arrItems = array();

		    foreach ($items as $item) {
		    	$t = $item;
		    	if (!is_array($t['title']))
		    		$t['title'] = urlencode($t['title']);
		    	
		    	array_push($arrItems, $t);
		    }
		
		    $aParams['items'] = $arrItems;
		    $aParams['params'] = $params;
		    $aParams['item_count'] = count($items);
		    $aParams['userId'] = OW::getUser()->getId();
		    
		    $component = null;
		    
		    if ('photo' == $media)
		    {
		        //$script = 'index/__photos.tpl';
		        $component = new YNMEDIAIMPORTER_CMP_PhotoBrowse($aParams);
		    }
		    else
		    {
		        //$script = 'index/__albums.tpl';
		        $component = new YNMEDIAIMPORTER_CMP_AlbumBrowse($aParams);
		    }
		    $data['html'] = $component -> render();
		
		}
		catch(Exception $e)
		{
		    $data['message'] = $e -> getMessage();
		}
		echo json_encode($data);
		exit(0);
    }
    
    public function quote($str)
    {
    	return "'" . $str . "'";
    }
    
    public function check()
    {
    	
    }
    
    public function postimport()
    {
    	$json = $_POST['json'];
    	$items = (array)json_decode($json, 1);
    	
    	$albumName = '';
    	$photo = 0;
    	$photos = array();
    	$albums = array();
    	$photosId = array();
    	$albumsId = array();
    	$provider = 'facebook';
    	foreach ($items as $item)
    	{
    		$data = json_decode($item['data'], 1);
    		$media = $item['media'];
    		$provider = $item['provider'];
    		if ('photo' == $media)
    		{
    			$photos[] = $data;
    		}
    		else
    		{
    			$albums[] = $data;
    			$albumName = $data['title_decode'];
    			$rows = Ynmediaimporter::getProvider($provider) -> getAllPhoto(array(
    					'media' => $media,
    					'aid' => $data['aid'],
    					'media_parent'=>$data['media_parent'],
    					'photo_count' => $data['photo_count'],
    			));
    	
    			foreach ($rows as $item)
    			{
    				$photos[] = $item;
    			}
    		}
    	}
    	
    	$schedulerId = 0;
    	
     	$tableName = OW_DB_PREFIX . 'ynmediaimporter_nodes';
    	
    	$sql = "INSERT IGNORE INTO $tableName (
    	nid, user_id, owner_id,owner_type, `key`, uid, aid, media, provider,
    	photo_count,`status`,title, src_thumb, src_small,src_medium,src_big, description
    	) VALUES ";
    	
    	$pieces = array();
    	$status = 0;
    	$userId = OW::getUser()->getId();
    	$ownerId = $userId;
    	$ownerType = 'user';
    	
    	$deleteSql = "DELETE FROM $tableName WHERE `user_id` = '{$userId}' AND `scheduler_id` = '0'";
    	OW::getDbo()->query($deleteSql);
    	
    	$flag = 0;
    	
    	foreach ($photos as $item)
    	{
	    	$photosId[] = $item['nid'];
	    	
	    	$pieces[] = '(' . implode(',', array_map(array(
			    	$this,
			    	'quote'
			    	), array(
			    			$item['nid'],
			    			$userId,
			    			$ownerId,
			    			$ownerType,
			    			$item['id'],
			    			$item['uid'],
			    			$item['aid'],
			    			$item['media'],
			    			$item['provider'],
			    			$item['photo_count'],
			    			($item['status']) ? $item['status'] : 0,
			    			( is_array($item['title']) && isset($item['title']['text'])) 
			    				? (mysql_real_escape_string($item['title']['text'])) 
			    				: (mysql_real_escape_string($item['title'])),
			    			$item['src_thumb'],
			    			$item['src_small'],
			    			$item['src_medium'],
			    			$item['src_big'],
			    			$item['description']
			    		))) . ')';
		}
    	
    	foreach ($albums as $item)
    	{
    		$albumsId[] = $item['nid'];
    		$pieces[] = '(' . implode(',', array_map(array(
    			$this,
    			'quote'
		    	), array(
		    			$item['nid'],
    					$userId,
    					$ownerId,
    					$ownerType,
						$item['id'],
						$item['uid'],
						$item['aid'],
						$item['media'],
						$item['provider'],
						$item['photo_count'],
						($item['status']) ? $item['status'] : 0,
						mysql_real_escape_string(urldecode($item['title'])),
						$item['src_thumb'],
						$item['src_small'],
						$item['src_medium'],
						$item['src_big'],
						$item['description']
			))) . ')';
		}
		
		$sql .= implode(',', $pieces);

		OW::getDbo()->query($sql);
		$numphoto = intval(OW::getDbo()->queryForColumn("select count(*) from $tableName where user_id=$userId and scheduler_id=0 and media='photo'"));
		$count_photo = count($photosId);
		$count_album = count($albumsId);
		$message = '';
    	
		if ($numphoto == 0)
		{
			$message = 'It seem all selected photos is set to queue. So the current request will be canceled.';
		}
		else if ($count_album && $count_photo)
		{
			$message = "Import <strong>{$count_photo}</strong> photo(s) in <strong>{$count_album}</strong> album(s).";
		}
		else if ($count_photo)
		{
			$message = "Import {$count_photo} photo(s).";
		}
		else
		{
			$message = 'Sorry, Your request can not be executed.';
		}
    	
		$aOutput = array(
    		'scheduler' => $schedulerId,
			'photos' => $photosId,
			'numphoto'=>$numphoto,
			'albums' => $albumsId,
    	    'message' => $message,
    		'photo_count' => $count_photo,
			'provider' => $provider,
			'album_count' => $count_album
		);
		
		$addPhotoForm = new YNMEDIAIMPORTER_CMP_AddPhoto(array('json_data' => json_encode($aOutput)));
		
		$responderUrl = OW::getRouter()->urlFor('PHOTO_CTRL_Upload', 'suggestAlbum', array('userId' => $userId));
		$aOutput['form'] = $addPhotoForm->render();
		$aOutput['responder_url'] = $responderUrl;
		$aOutput['album_name'] = $albumName;
		echo json_encode($aOutput);
		exit(0);
    }
    
    public function generateImagePath( $imageId, $icon = true )
    {
    	$imagesDir = OW::getPluginManager()->getPlugin('ynmediaimporter')->getUserFilesDir();
    	return $imagesDir . ( $icon ? 'event_icon_' : 'event_image_' ) . $imageId . '.jpg';
    }
    
    function addphoto()
    {
    	//$script = "$(window).bind('beforeunload', function(){return 'Processing your request...';});";
    	//OW::getDocument()->addOnloadScript($script);
    	
    	OW::getDocument()->addStyleSheet( OW::getPluginManager()->getPlugin('ynmediaimporter')->getStaticCssUrl().'ynmediaimporter.css');
    	$this->setPageTitle(OW::getLanguage()->text("ynmediaimporter", "adding_photo"));
    	$this->setPageHeading(OW::getLanguage()->text("ynmediaimporter", "index_page_heading"));
    	
    	if ( !OW::getRequest()->isPost() ){
    		$url = OW::getRouter()->urlForRoute('ynmediaimporter.index');
           	$this -> redirect($url);
           	exit;
    	}
    	
    	$lang = OW::getLanguage();
    	
    	if ( !strlen($albumName = htmlspecialchars(trim($_POST['album']))) )
    	{
    		$resp = array('result' => false, 'msg' => $lang->text('photo', 'photo_upload_error'));
    		exit(json_encode($resp));
    	}
    	
    	$photoService = PHOTO_BOL_PhotoService::getInstance();
    	$tagService = BOL_TagService::getInstance();
    	$photoAlbumService = PHOTO_BOL_PhotoAlbumService::getInstance();
     	$photoTmpService = PHOTO_BOL_PhotoTemporaryService::getInstance();
    	
    	$userId = OW::getUser()->getId();
    	
    	// check album exists
    	if ( !($album = $photoAlbumService->findAlbumByName($albumName, $userId)) )
    	{
    		$album = new PHOTO_BOL_PhotoAlbum();
    		$album->name = $albumName;
    		$album->userId = $userId;
    		$album->createDatetime = time();
    	
    		$albumId = $photoAlbumService->addAlbum($album);
    		$newAlbum = true;
    	}
    	else{
    		$albumId = $album->id;
    	}
    	
    	$data = str_replace("'", "\"", $_POST['json_data']);
		$data = json_decode($data);

		if (!empty($data->albums)) {
			$schedulerId = Ynmediaimporter::setupScheduler($data->photos, $data->albums, $albumId, array());
		}
		else {
			$schedulerId = Ynmediaimporter::setupScheduler($data->photos, null, $albumId, array());
		}
			
    	
    	$schedulerUrl = OW::getRouter()->urlForRoute('ynmediaimporter.scheduler', array('scheduler_id' => $schedulerId));
    	$this->assign('callback_url', OW::getRouter()->urlForRoute("ynmediaimporter.index") . "/" . $data->provider);
    	$this->assign('scheduler_id', $schedulerId);
    	$this->assign('scheduler_url', $schedulerUrl);
    	
    }
    
    public function handleShutdown()
    {
    	exit;
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
    
    public function scheduler($params)
    {
    	register_shutdown_function(array($this,'handleShutdown'));
    	ini_set('max_execution_time',12);
    	
    	/**
    	 * following step to speed up & beat performance
    	 * 1. check album limit
    	 * 2. check quota limit
    	 * 3. get nodes of this schedulers
    	 * 4. get all items of current schedulers.
    	 * 5. process each node
    	 * 5.1 check required quota
    	 * 5.2 fetch data to pubic file
    	 * 5.3 store to file model
    	 * 6. check status of schedulers, if scheduler is completed == (remaining == 0)
    	 * 6.1 udpate feed and message.
    	*/
    	
    	/**
    	 * Unlimited time.
    	*/
    	//set_time_limit(0);
    	
    	$schedulerId = $params['scheduler_id'];
    	$scheduler = YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->findById($schedulerId);
    	
    	/**
    	 * check Ynmediaimporter::processScheduler for futher information.
    	 *
    	*/
    	$result = Ynmediaimporter::processScheduler($scheduler, 0, 10, 1, 1);

    	/**
    	 * get remain
    	 * @see Ynmediaimporter::processScheduler
    	*/
    	$remain = $result['remain'];
    	
    	if ($remain == 0)
    	{
    		$result['message'] = 'Your import request has been completed.';
    	}
    	else
    	{
    		$result['message'] = 'Your import request has been added to the queue.';
    	}
    	
    	echo json_encode($result);
    	
    	exit(0);
    	 
    }
}

<?php

class GPHOTOVIEWER_CTRL_Index extends OW_ActionController
{
    /**
     * @var OW_PluginManager
     */
    private $plugin;
    /**
     * @var string
     */
    private $pluginJsUrl;
    /**
     * @var string
     */
    private $ajaxResponder;
    /**
     * @var PHOTO_BOL_PhotoService 
     */
    private $photoService;
    /**
     * @var PHOTO_BOL_PhotoAlbumService 
     */
    private $photoAlbumService;
    /**
     * @var BASE_CMP_ContentMenu
     */
    private $menu;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
		$this->photoService = PHOTO_BOL_PhotoService::getInstance();
        $this->photoAlbumService = PHOTO_BOL_PhotoAlbumService::getInstance();
		$this->ajaxResponder = OW::getRouter()->urlFor('GPHOTOVIEWER_CTRL_Index', 'ajaxResponder');
    }

    /**
     * Method acts as ajax responder. Calls methods using ajax
     * 
     * @return JSON encoded string
     *
     */
    public function getPhotosContent()
    {	
		if ( empty($_GET['photo_id']) || !$_GET['photo_id'] )
        {
            throw new Redirect404Exception();
            exit;
        }
		$userId = OW::getUser()->getId();
        $photoId = (int) $_GET['photo_id'];

        $photo = $this->photoService->findPhotoById($photoId);
        
        if ( !$photo )
        {
            exit(json_encode(array('result' => 'error')));
        }
		
		// is moderator
        $moderatorMode = OW::getUser()->isAuthorized('photo');
        $userId = OW::getUser()->getId();
		$contentOwner = $this->photoService->findPhotoOwner($photoId);
		$ownerMode = $contentOwner == $userId;
		$canView = true;
		$message = '';
        if ( !$ownerMode && !OW::getUser()->isAuthorized('photo', 'view') )
        {
            $canView = false;
			$message = OW::getLanguage()->text('base', 'authorization_failed_feedback');
        }
		
		
		$album = $this->photoAlbumService->findAlbumById($photo->albumId);
		$ownerName = BOL_UserService::getInstance()->getUserName($album->userId);
		$photoCount = $this->photoAlbumService->countAlbumPhotos($photo->albumId);
		$photos = $this->photoService->getAlbumPhotos($photo->albumId, 1, $photoCount);
		foreach($photos as $item){
			$photosList[] = array(
			'photo_id' => $item['id'],
			'thumb' => $item['url'],
			'src' => $this->photoService->getPhotoUrl($item['id']),
			'active' => ($item['id'] == $_GET['photo_id']),
			'title' => '',
			'description' => ''
		  );
		}
		//var_dump($photos);die;
		exit(json_encode(array(
			'photos' => $photosList,
			'count' => $photoCount,
			'album_title' => $album->name,
			'album_href' => OW::getRouter()->urlForRoute('photo_user_album', array('user' => $ownerName, 'album' => $photo->albumId)),
			'owner_title' => BOL_UserService::getInstance()->getDisplayName($album->userId),
			'owner_href' => BOL_UserService::getInstance()->getUserUrl($album->userId),
			'authorized' => $ownerMode || $moderatorMode || OW::getUser()->isAuthorized('photo', 'view'),
			'message' => $message
		)));
		
    }
	
	    /**
     * Method acts as ajax responder. Calls methods using ajax
     * 
     * @return JSON encoded string
     *
     */
    public function getPhotosComment()
    {
		$resp = $this->prepareMarkup($_GET['photo_id']);
		exit(json_encode($resp));
    }
	
	private function prepareMarkup( $photoId )
    {
        $cmp = new GPHOTOVIEWER_CMP_PhotoComment(array('photoId' => $photoId));
    
        /* @var $document OW_AjaxDocument */
        $document = OW::getDocument();

        $markup = array();

        $markup['id'] = (int) $photoId;
        $markup['html'] = $cmp->render();

        $onloadScript = $document->getOnloadScript();
        if ( !empty($onloadScript) )
        {
            $markup['onloadScript'] = $onloadScript;
        }
        
        $scriptFiles = $document->getScripts();
        if ( !empty($scriptFiles) )
        {
            $markup['scriptFiles'] = $scriptFiles;
        }

        $css = $document->getStyleDeclarations();
        if ( !empty($css) )
        {
            $markup['css'] = $css;
        }
        
        return $markup;
    }
	
	public function download( array $params )
    {	
		$config = OW::getConfig();
        $canDownload = $config->getValue('gphotoviewer', 'can_users_to_download_photos');
		if(!$canDownload){
			throw new Redirect404Exception();
		}
        if ( !isset($params['id']) || !($photoId = (int) $params['id']) )
        {
            throw new Redirect404Exception();
        }

        $photo = $this->photoService->findPhotoById($photoId);
		
        if ( !$photo )
        {
            throw new Redirect404Exception();
        }
		$canView = true;
        if ( !$ownerMode && !$modPermissions && !OW::getUser()->isAuthorized('photo', 'view') )
        {
            $canView = false;
			throw new Redirect404Exception();
        }
        if( (int) BOL_PluginService::getInstance()->findPluginByKey('photo')->build > 6272){
			$url = $this->photoService->getPhotoPath($photo->id, $photo->hash);
        }else{
        	$url = $this->photoService->getPhotoPath($photo->id);
        }
		if (file_exists($url) && is_readable($url)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($url));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($url));
			ob_clean();
			flush();
			readfile($url);
			exit;
		} else {
		  throw new Redirect404Exception();
		}
		exit;
    }
	
	/**
     * Method acts as ajax responder. Calls methods using ajax
     *
     * @return JSON encoded string
     *
     */
    public function ajaxResponder()
    {
        $request = json_decode($_POST['request'], true);

        if ( isset($request['ajaxFunc']) && OW::getRequest()->isAjax() )
        {
            $callFunc = (string) $request['ajaxFunc'];

            $result = call_user_func(array($this, $callFunc), $request);
        }
        else
        {
            return;
        }

        exit(json_encode($result));
    }
}
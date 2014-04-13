<?php

class GVIDEOVIEWER_CTRL_Index extends OW_ActionController
{
	/**
     * @var OW_Plugin
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
     * @var VIDEO_BOL_ClipService
     */
    private $clipService;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->plugin = OW::getPluginManager()->getPlugin('video');
        $this->pluginJsUrl = $this->plugin->getStaticJsUrl();
        $this->ajaxResponder = OW::getRouter()->urlFor('VIDEO_CTRL_Video', 'ajaxResponder');

		$this->clipService = VIDEO_BOL_ClipService::getInstance();

		
    }

    /**
     * Method acts as ajax responder. Calls methods using ajax
     * 
     * @return JSON encoded string
     *
     */
    public function getVideosContent()
    {	
		if ( empty($_GET['video_id']) || !$_GET['video_id'] )
        {
            throw new Redirect404Exception();
            exit;
        }
		
		$id = $_GET['video_id'];
		
		$clip = $this->clipService->findClipById($id);

        if ( !$clip )
        {
            throw new Redirect404Exception();
        }
		
		$contentOwner = (int) $this->clipService->findClipOwner($id);
		$videoCount = $this->clipService->findUserClipsCount($contentOwner);
		$videos = $this->clipService->findUserClipsList($contentOwner, 1, $videoCount);

		foreach($videos as $item){
			$videosList[] = array(
			'video_id' => $item['id'],
			'thumb' => ($item['thumb'] != 'undefined' ? $item['thumb'] : OW_URL_HOME .'ow_static/plugins/gvideoviewer/img/video-no-video.jpg'),
			'src' => $this->getVideoCode($item['code'], $item['provider']),
			'active' => ($item['id'] == $_GET['video_id']),
			'title' => $item['title'],
			'description' => $item['description'],
			'href' => OW::getRouter()->urlForRoute('view_clip', array('id' => $item['id']))
		  );
		}
		
		exit(json_encode(array(
			'videos' => $videosList,
			'count' => $videoCount,
			'album_title' => '',
			'album_href' => '',
			'owner_title' => BOL_UserService::getInstance()->getDisplayName($contentOwner),
			'owner_href' => BOL_UserService::getInstance()->getUserUrl($contentOwner)
		)));
		
    }
	
	public function getVideoCode($clipCode, $provider){
	
		$code = $this->clipService->validateClipCode($clipCode, $provider);
        $code = $this->clipService->addCodeParam($code, 'wmode', 'transparent');

        $config = OW::getConfig();
        $playerWidth = $config->getValue('video', 'player_width');
        $playerHeight = $config->getValue('video', 'player_height');

        $code = $this->clipService->formatClipDimensions($code, $playerWidth, $playerHeight);

        if ( $provider == 'youtube' )
        {
            $code = preg_replace('/src="([^"]+)"/i', 'src="$1?wmode=transparent&origin=http://ow"', $code);
        }
        
        return $code;
		
	}
	
	/**
     * Method acts as ajax responder. Calls methods using ajax
     * 
     * @return JSON encoded string
     *
     */
    public function getVideosComment()
    {
		$resp = $this->prepareMarkup($_GET['video_id']);
		exit(json_encode($resp));
    }
	
	private function prepareMarkup( $videoId )
    {
        $cmp = new GVIDEOVIEWER_CMP_VideoComment(array('videoId' => $videoId));
    
        /* @var $document OW_AjaxDocument */
        $document = OW::getDocument();

        $markup = array();

        $markup['id'] = (int) $videoId;
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
	    
	public function ajaxUpdateVideo()
    {
		if ( OW::getRequest()->isAjax() )
        {
			$clipId = (int) $_POST['id'];
            
            $form = new GVIDEOVIEWER_CLASS_EditForm($clipId); 
			if ( $form->isValid($_POST) )
            {
				$values = $form->getValues();
				$clip = $this->clipService->findClipById($clipId);

				if ( $clip )
				{
					$clip->title = htmlspecialchars($values['title']);
					$description = UTIL_HtmlTag::stripJs($values['description']);
					$description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
					$clip->description = $description;
					$clip->code = $values['code'];

					if ( $this->clipService->updateClip($clip) )
					{
						BOL_TagService::getInstance()->updateEntityTags(
							$clip->id,
							'video',
							$values['tags']
						);
						exit(json_encode(array('result' => true, 'id' => $clip->id)));
					}
				}
			}
		}
	}

}
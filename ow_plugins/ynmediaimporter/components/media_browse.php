<?php

class YNMEDIAIMPORTER_CMP_MediaBrowse extends OW_Component
{
	public function __construct(  array $arrParams )
    {
        parent::__construct();
    	$format = (isset($arrParams['format'])) ? ($arrParams['format']) : null;
        $serviceName = (isset($arrParams['service'])) ? ($arrParams['service']) : null;
        
        if (null != $serviceName)
        {
           	$params = array();
	        $params['limit'] = intval( (isset($arrParams['limit'])) ? ($arrParams['limit']) : YNMEDIAIMPORTER_PER_PAGE );
	        $params['offset'] = intval( (isset($arrParams['offset'])) ? ($arrParams['offset']) : 0 );
	        $params['service'] = $serviceName;
	        $params['extra'] = (isset($arrParams['extra'])) ? ($arrParams['extra']) : 'my';
	        
	        //Load my photos first with Flickr provider.
	        if ($serviceName == 'flickr')
	        	$params['media'] = $media = (isset($arrParams['media'])) ? $arrParams['media'] : 'photo';
	        else 
	        	$params['media'] = $media = (isset($arrParams['media'])) ? $arrParams['media'] : 'album';
	        	
	        $params['aid'] = (isset($arrParams['aid'])) ? $arrParams['aid'] : 0;
	        $params['cache'] = (isset($arrParams['cache'])) ? $arrParams['cache'] : 1;
	        
	        $cache = (isset($arrParams['cache'])) ? $arrParams['cache'] : 1;
	        $jsonParams = json_encode($params);
	        
	        $getDataUrl = OW::getRouter()->urlForRoute('ynmediaimporter.getdata');
			$moduleUrl = OW::getRouter()->urlForRoute('ynmediaimporter.index');
			
			OW::getLanguage()->addKeyForJs('ynmediaimporter', 'message_text_loading');
			$ajaxImageUrl = OW::getThemeManager()->getThemeImagesUrl() . '/ajax_preloader_content.gif';
			$this->assign('ajaxImageUrl', $ajaxImageUrl);
			$this->assign('getDataUrl', $getDataUrl);
	        $this->assign('moduleUrl', $moduleUrl);
	        $this->assign('jsonParams', $jsonParams);
        }
        else 
        {
        	$this->assign('content', "errors");
        }
    }
 
    public static function getAccess() // If you redefine this method, you'll be able to manage the widget visibility 
    {
        return self::ACCESS_ALL;
    }
 
    public function onBeforeRender() // The standard method of the component that is called before rendering
    {
        
        
        
    }
}
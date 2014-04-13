<?php

class YNMEDIAIMPORTER_CMP_AlbumBrowse extends OW_Component
{
	public function __construct(  array $aParams )
    {
        parent::__construct();
		
        //print_r($aParams); exit;
        
        foreach ($aParams['items'] as $k => $v){
        	$aParams['items'][$k]['media_parent'] = isset($aParams['items'][$k]['media_parent']) ? $aParams['items'][$k]['media_parent'] : '';
        	$aParams['items'][$k]['title_decode'] = urldecode($aParams['items'][$k]['title']);
        	$aParams['items'][$k]['update_page_param'] = http_build_query(array(
        				'service'=>$aParams['items'][$k]['provider'],
        				'media'=>'photo',
        				'media_parent'=> $aParams['items'][$k]['media_parent'],
        				'extra'=>'aid',
        				'aid'=>$aParams['items'][$k]['aid'])) ;
        	
        	//$aParams['items'][$k]['brief_title'] = substr($aParams['items'][$k]['title_decode'], 0, 20);
        	$aParams['items'][$k]['status_text'] = OW::getLanguage()->text("ynmediaimporter", 'import_status_' . $aParams['items'][$k]['status']);  
        	$aParams['items'][$k]['json'] = json_encode($aParams['items'][$k]);
		}
       	//print_r($aParams); exit;
       	if (isset($aParams['items']))
        	$this->assign('items', $aParams['items']);
        	
        if (isset($aParams['params'])){
        	$this->assign('params', $aParams['params']);
        	$this->assign('params_json', json_encode($aParams['params']));
        }
        
        if (isset($aParams['item_count']))
        	$this->assign('item_count', $aParams['item_count']);
        	
        if (isset($aParams['userId']))
        	$this->assign('userId', $aParams['userId']);
    }
 
    public static function getAccess() // If you redefine this method, you'll be able to manage the widget visibility 
    {
        return self::ACCESS_ALL;
    }
 
    public function onBeforeRender() // The standard method of the component that is called before rendering
    {
        
    }
}
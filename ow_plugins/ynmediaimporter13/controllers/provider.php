<?php
class YNMEDIAIMPORTER_CTRL_Provider extends OW_ActionController
{
	public function init()
	{
		OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery-suggest.js');
		OW::getDocument()->addScript( OW::getPluginManager()->getPlugin('ynmediaimporter')->getStaticJsUrl().'json2.js');
		OW::getDocument()->addScript( OW::getPluginManager()->getPlugin('ynmediaimporter')->getStaticJsUrl().'ynmediaimporter.js');
		OW::getDocument()->addStyleSheet( OW::getPluginManager()->getPlugin('ynmediaimporter')->getStaticCssUrl().'ynmediaimporter.css');
		$this->initStyle();
	}
	
	
	public function initStyle()
	{
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		
		//a.ynimporter-album-thumb-stager i,div.ynimporter-album-thumb-wrapper
		// i{}
		$width = ($configs['album_thumb_width']) ?  intval($configs['album_thumb_width']) : 160;
		$height = ($configs['album_thumb_height']) ?  intval($configs['album_thumb_height']) : 116;

		
		//div.ynimporter-album-wrapper{}
		$wrapHeight = ($configs['album_wrap_height']) ?  intval($configs['album_wrap_height']) : 200;
		$wrapMargin = ($configs['album_wrap_margin']) ?  intval($configs['album_wrap_margin']) : 10;
		
		
		//a.ynimporter-album-thumb-stager i,div.ynimporter-album-thumb-wrapper
		// i{}
		$width2 = ($configs['photo_thumb_width']) ?  intval($configs['photo_thumb_width']) : 160;
		$height2 = ($configs['photo_thumb_height']) ?  intval($configs['photo_thumb_height']) : 116;
		
		//div.ynimporter-album-wrapper{}
		$wrapHeight2 = ($configs['photo_wrap_height']) ?  intval($configs['photo_wrap_height']) : 180;
		$wrapMargin2 = ($configs['photo_wrap_margin']) ?  intval($configs['photo_wrap_margin']) : 10;
		
		OW::getDocument()->addStyleDeclaration("
				.ynimporter-album-thumb-stager i, div.ynimporter-album-thumb-wrapper i{
					height: {$height}px;
					width: {$width}px;
				} 
				.ynimporter-album-wrapper{
					height:{$wrapHeight}px;
					margin-right:{$wrapMargin}px;
				}
		");
		
		OW::getDocument()->addStyleDeclaration("
				.ynimporter-photo-thumb-stager i,div.ynimporter-photo-thumb-wrapper i{
					height: {$height2}px;
					width: {$width2}px;
				} 
				.ynimporter-photo-wrapper{
					height:{$wrapHeight2}px;
					margin-right:{$wrapMargin2}px;
				}
		");
	}
	
	private function getMenu()
    {
        $menuItems = array();
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		
		if ($configs['enable_facebook']){
			$item = new BASE_MenuItem();
			$item->setLabel( OW::getLanguage()->text('ynmediaimporter', 'facebook') );
			$item->setUrl( OW::getRouter()->urlForRoute('ynmediaimporter.facebook') );
			$item->setKey( 'facebook' );
			//$item->setIconClass( 'ow_ic_gear_wheel' );
			$item->setOrder( 0 );
			array_push( $menuItems, $item );
		}
                
		if ($configs['enable_picasa']){
			$item = new BASE_MenuItem();
			$item->setLabel( OW::getLanguage()->text('ynmediaimporter', 'picasa') );
			$item->setUrl( OW::getRouter()->urlForRoute('ynmediaimporter.picasa') );
			$item->setKey( 'picasa' );
			//$item->setIconClass( 'ow_ic_gear_wheel' );
			$item->setOrder( 1 );
			array_push( $menuItems, $item );
		}
       
		if ($configs['enable_flickr']){
			$item = new BASE_MenuItem();
			$item->setLabel( OW::getLanguage()->text('ynmediaimporter', 'flickr') );
			$item->setUrl( OW::getRouter()->urlForRoute('ynmediaimporter.flickr') );
			$item->setKey( 'flickr' );
			//$item->setIconClass( 'ow_ic_gear_wheel' );
			$item->setOrder( 2 );
			array_push( $menuItems, $item );
		}
		
        if ($configs['enable_instagram']){
        	$item = new BASE_MenuItem();
        	$item->setLabel( OW::getLanguage()->text('ynmediaimporter', 'instagram') );
        	$item->setUrl( OW::getRouter()->urlForRoute('ynmediaimporter.instagram') );
        	$item->setKey( 'instagram' );
        	//$item->setIconClass( 'ow_ic_gear_wheel' );
        	$item->setOrder( 3 );
        	array_push( $menuItems, $item );
        }
        
        /*
        if ($configs['enable_yfrog']){
        	$item = new BASE_MenuItem();
        	$item->setLabel( OW::getLanguage()->text('ynmediaimporter', 'yfrog') );
        	$item->setUrl( OW::getRouter()->urlForRoute('ynmediaimporter.yfrog') );
        	$item->setKey( 'yfrog' );
        	//$item->setIconClass( 'ow_ic_gear_wheel' );
        	$item->setOrder( 4 );
        	array_push( $menuItems, $item );
        }
        */
        
        return new BASE_CMP_ContentMenu( $menuItems );
    }
	
    
	public function facebook()
	{
		$this->addComponent( 'menu', $this->getMenu() );
		if (is_object($this->getComponent( 'menu' )->getElement( 'facebook' )))
		{
			$this->getComponent( 'menu' )->getElement( 'facebook' )->setActive( true );
		}
        
		$language = OW::getLanguage();
	}
	
	public function flickr()
	{
		$this->addComponent( 'menu', $this->getMenu() );
		if (is_object($this->getComponent( 'menu' )->getElement( 'flickr' )))
		{
        	$this->getComponent( 'menu' )->getElement( 'flickr' )->setActive( true );
		}
		$language = OW::getLanguage();
	}
	
	public function picasa()
	{
		$this->addComponent( 'menu', $this->getMenu() );
		if (is_object($this->getComponent( 'menu' )->getElement( 'picasa' )))
		{
        	$this->getComponent( 'menu' )->getElement( 'picasa' )->setActive( true );
		}
		$language = OW::getLanguage();
	}
	
	public function instagram()
	{
		$this->addComponent( 'menu', $this->getMenu() );
		if (is_object($this->getComponent( 'menu' )->getElement( 'instagram' )))
		{
        	$this->getComponent( 'menu' )->getElement( 'instagram' )->setActive( true );
		}
		$language = OW::getLanguage();
	}
	
	/*
	public function yfrog()
	{
		$this->addComponent( 'menu', $this->getMenu() );
        $this->getComponent( 'menu' )->getElement( 'yfrog' )->setActive( true );
		$language = OW::getLanguage();
	}
	*/
}
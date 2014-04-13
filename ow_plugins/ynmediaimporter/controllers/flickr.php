<?php
class YNMEDIAIMPORTER_CTRL_Flickr extends YNMEDIAIMPORTER_CTRL_Provider
{
	protected $_serviceName = 'flickr';
	
	public function index()
	{
		$this->setPageTitle(OW::getLanguage()->text("ynmediaimporter", "flickr") . " - " . OW::getLanguage()->text("ynmediaimporter", "index_page_title"));
		$this->setPageHeading(OW::getLanguage()->text("ynmediaimporter", "index_page_heading"));
		
		$this->flickr();
		$flickrProfile = new YNMEDIAIMPORTER_CMP_FlickrProfile();
        $this->addComponent('flickrProfile', $flickrProfile);
        
        $mediaBrowse = new YNMEDIAIMPORTER_CMP_MediaBrowse(array('service' => 'flickr'));
        $this->addComponent('mediaBrowse', $mediaBrowse);
        
		$provider = Ynmediaimporter::getProvider($this -> _serviceName);
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		
		if ($configs['enable_flickr'] == 0) {
			$url = OW::getRouter()->urlForRoute('ynmediaimporter.index');
            $this -> redirect($url);
            exit ;
		}
		
        if (!$provider->isAlive())
        {
        	$this -> redirect($provider -> getConnectUrl());
            exit ;
        }
        
		if(!OW::getPluginManager()->isPluginActive('ynsocialbridge'))
		{
			return;
		}
	}
}
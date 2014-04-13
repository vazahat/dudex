<?php
class YNMEDIAIMPORTER_CTRL_Instagram extends YNMEDIAIMPORTER_CTRL_Provider
{
	protected $_serviceName = 'instagram';
	
	public function index()
	{
		$this->setPageTitle(OW::getLanguage()->text("ynmediaimporter", "instagram") . " - " . OW::getLanguage()->text("ynmediaimporter", "index_page_title"));
		$this->setPageHeading(OW::getLanguage()->text("ynmediaimporter", "index_page_heading"));
		
		$this->instagram();
		$instagramProfile = new YNMEDIAIMPORTER_CMP_InstagramProfile();
        $this->addComponent('instagramProfile', $instagramProfile);
        
        $mediaBrowse = new YNMEDIAIMPORTER_CMP_MediaBrowse(array('service' => 'instagram'));
        $this->addComponent('mediaBrowse', $mediaBrowse);
        
		$provider = Ynmediaimporter::getProvider($this -> _serviceName);
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		
		if ($configs['enable_instagram'] == 0) {
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
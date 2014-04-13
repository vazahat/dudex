<?php
class YNMEDIAIMPORTER_CTRL_Facebook extends YNMEDIAIMPORTER_CTRL_Provider
{
	protected $_serviceName = 'facebook';
	
	public function index()
	{
		$this->setPageTitle(OW::getLanguage()->text("ynmediaimporter", "facebook") . " - " . OW::getLanguage()->text("ynmediaimporter", "index_page_title"));
		$this->setPageHeading(OW::getLanguage()->text("ynmediaimporter", "index_page_heading"));
		
		$this->facebook();//gender facebook page template
		$facebookProfile = new YNMEDIAIMPORTER_CMP_FacebookProfile();
        $this->addComponent('facebookProfile', $facebookProfile);
        
        $mediaBrowse = new YNMEDIAIMPORTER_CMP_MediaBrowse(array('service' => 'facebook'));
        $this->addComponent('mediaBrowse', $mediaBrowse);
        
		$provider = Ynmediaimporter::getProvider($this -> _serviceName);
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		
		if ($configs['enable_facebook'] == 0) {
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
<?php
class YNMEDIAIMPORTER_CTRL_Picasa extends YNMEDIAIMPORTER_CTRL_Provider
{
	protected $_serviceName = 'picasa';
	
	public function index()
	{
		$this->setPageTitle(OW::getLanguage()->text("ynmediaimporter", "picasa") . " - " . OW::getLanguage()->text("ynmediaimporter", "index_page_title"));
		$this->setPageHeading(OW::getLanguage()->text("ynmediaimporter", "index_page_heading"));
		
		$this->picasa();
		$picasaProfile = new YNMEDIAIMPORTER_CMP_PicasaProfile();
        $this->addComponent('picasaProfile', $picasaProfile);
        
        $mediaBrowse = new YNMEDIAIMPORTER_CMP_MediaBrowse(array('service' => 'picasa'));
        $this->addComponent('mediaBrowse', $mediaBrowse);
        
		$provider = Ynmediaimporter::getProvider($this -> _serviceName);
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		
		if ($configs['enable_picasa'] == 0) {
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
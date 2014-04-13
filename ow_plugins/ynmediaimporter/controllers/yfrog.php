<?php
class YNMEDIAIMPORTER_CTRL_Yfrog extends YNMEDIAIMPORTER_CTRL_Provider
{
	protected $_serviceName = 'yfrog';
	
	public function index()
	{
		$this->setPageTitle(OW::getLanguage()->text("ynmediaimporter", "yfrog") . " - " . OW::getLanguage()->text("ynmediaimporter", "index_page_title"));
		$this->setPageHeading(OW::getLanguage()->text("ynmediaimporter", "index_page_heading"));
		
		$this->yfrog();
		$yfrogProfile = new YNMEDIAIMPORTER_CMP_YfrogProfile();
        $this->addComponent('yfrogProfile', $yfrogProfile);
        
        $mediaBrowse = new YNMEDIAIMPORTER_CMP_MediaBrowse(array('service' => 'yfrog'));
        $this->addComponent('mediaBrowse', $mediaBrowse);
        
		$provider = Ynmediaimporter::getProvider($this -> _serviceName);
		$configs =  OW::getConfig()->getValues('ynmediaimporter');
		
		if ($configs['enable_yfrog'] == 0) {
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
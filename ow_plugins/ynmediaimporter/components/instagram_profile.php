<?php

class YNMEDIAIMPORTER_CMP_InstagramProfile extends OW_Component
{
	public function __construct( )
    {
        parent::__construct();
        $provider = Ynmediaimporter::getProvider('instagram');
        $userThumb = $provider -> getUserSquareAvatarUrl();
        $userProfileUrl = $provider -> getUserProfileUrl();
        $userDisplayName = $provider -> getUserDisplayname();
        $disconnectUrl = $provider->getDisconnectUrl();
        
        $this->assign('userThumb', $userThumb);
        $this->assign('userProfileUrl', $userProfileUrl);
        $this->assign('userDisplayName', $userDisplayName);
        $this->assign('disconnectUrl', $disconnectUrl);
	}
 
    public static function getAccess() // If you redefine this method, you'll be able to manage the widget visibility 
    {
        return self::ACCESS_ALL;
    }
}
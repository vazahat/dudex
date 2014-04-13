<?php

class YNSOCIALCONNECT_CMP_ViewInJoinPage extends OW_Component
{
	const SESSION_USE_PROFILE_PHOTO = 'ynsc_use_profile_photo';

	public function render()
	{
		//	init
		$iLimit = OW::getConfig() -> getValue('ynsocialconnect', 'limit_providers_view_on_login_header');
		$iLimitSelected = OW::getConfig() -> getValue('ynsocialconnect', 'limit_providers_view_on_login_header');

		//	process
		//$aOpenProviders = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getEnabledProviders($iLimit, (int)$iLimitSelected);
		$aOpenProviders = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvidersByStatus($bDisplay = true);
		$listProvider = array();
		$step = 0;
		foreach($aOpenProviders as $item){
			if(in_array($item->name, array('facebook', 'twitter','linkedin')))
			{
				if(!YNSOCIALCONNECT_CLASS_SocialConnect::getInstance()->checkSocialBridgePlugin($item->name))
				{
					continue;
				}
			}
			$listProvider[] = $item;
			$step ++;
			if($step >= $iLimit){
				break;
			}
		}
		
		$iIconSize = (intval(OW::getConfig() -> getValue('ynsocialconnect', 'size_of_provider_icon_px')) >= 0) ? intval(OW::getConfig() -> getValue('ynsocialconnect', 'size_of_provider_icon_px')) : 24;
		$iWidth = (count($listProvider) + 1) * ($iIconSize + 6);

		$this -> assign('iLimitView', $iLimit);
		$this -> assign('iLimitSelected', $iLimitSelected);
		$this -> assign('aOpenProviders', $listProvider);
		$this -> assign('iIconSize', $iIconSize);
		$this -> assign('iWidth', $iWidth);
		$this -> assign('sCoreUrl', OW_DIR_ROOT);
		$this -> assign('sImgSrc', OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl() . 'img/');

		//	set url redirect to session
		$uri = OW::getRequest() -> getRequestUri();
		// @formatter:off
		if(isset($uri) 
			&& strpos($uri, 'socialbridge') === false 
			&& strpos($uri, 'ynsocialbridge') === false
			&& strpos($uri, 'ynsocialconnect') === false
			&& strpos($uri, 'socialconnect') === false
			){
			$uri = OW_URL_HOME . $uri;
			$_SESSION['ynsc_session']['urlRedirect'] = $uri;
		}
		// @formatter:on

		if (OW::getSession() -> isKeySet(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA))
		{
			$signupData = OW::getSession() -> get(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA);
			$profilePicture = YNSOCIALCONNECT_CLASS_SocialConnect::getInstance() -> getPhotoUrlFromTokenData($signupData['user'], $signupData['service']);
			if ($profilePicture == null)
			{
				//OW::getSession() -> set(self::SESSION_USE_PROFILE_PHOTO, 'not_use');
				$profilePicture = "";
			} else
			{
				//OW::getSession() -> set(self::SESSION_USE_PROFILE_PHOTO, 'use');
				$url = OW::getRouter() -> urlFor('YNSOCIALCONNECT_CTRL_Sync', 'removeAvatar');
				$removeEl = "<div class=\"ow_avatar_change\"><a href=\"javascript:void(0);\" onclick=\"YNSocialConnect.removeAvatar(\'$url\');\" class=\"ow_lbutton\">X</a></div>";
				//$removeEl = "<div class=\"ow_avatar_change\"><a href=\"javascript:void(0);\" onclick=\"YNSocialConnect.removeAvatar();\" class=\"ow_lbutton\">X</a></div>";
				$profilePicture = "<div id=\"ynsc_profile_picture\" class=\"ynsc_profile_photo_center\"><img class=\"ynsc_profile_phpto\" src=\"" . $profilePicture . "\">" . $removeEl . "</div><br />";
			}
			$this -> assign('profilePicture', $profilePicture);
		}

		//	end
		return parent::render();
	}


}

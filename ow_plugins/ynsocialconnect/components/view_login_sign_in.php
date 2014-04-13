<?php

/**
 * YNSOCIALCONNECT View Login Sign In
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.components
 * @since 1.0
 */

class YNSOCIALCONNECT_CMP_ViewLoginSignIn extends OW_Component
{
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

		if (YNSOCIALCONNECT_CLASS_Helper::getInstance() -> getIsViewMore() == '0')
		{
			$staticUrl = OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl();
			$document = OW::getDocument();
			$document -> addScript($staticUrl . 'js/ynsocialconnect.js');

			$this -> assign('isShowViewMore', '1');
			$this -> addComponent('eicmp', new YNSOCIALCONNECT_CMP_ViewMore());
		}

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
		// @formatter:ofn
		
		//	end
		return parent::render();
	}

}

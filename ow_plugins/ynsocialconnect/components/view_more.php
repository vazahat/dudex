<?php

/**
 * YNSOCIALCONNECT View More
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.components
 * @since 1.0
 */


class YNSOCIALCONNECT_CMP_ViewMore extends OW_Component
{
	public function onBeforeRender()
	{
		parent::onBeforeRender();
		$language = OW::getLanguage();

		// 	init

		//	process
		$aOpenProviders = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvidersByStatus($bDisplay = true);
		$listProvider = array();
		foreach($aOpenProviders as $item){
			if(in_array($item->name, array('facebook', 'twitter','linkedin')))
			{
				if(!YNSOCIALCONNECT_CLASS_SocialConnect::getInstance()->checkSocialBridgePlugin($item->name))
				{
					continue;
				}
			}
			$listProvider[] = $item;
		}
		
		$this -> assign('aOpenProviders', $listProvider);
		$this -> assign('sImgSrc', OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl() . 'img/');

		//	end
	}

}

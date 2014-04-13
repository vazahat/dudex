<?php
/**
 * YNSOCIALCONNECT View Login Header
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.components
 * @since 1.0
 */

class YNSOCIALCONNECT_CMP_ViewLoginHeader extends OW_Component
{
	/**
	 *
	 * @var BASE_CMP_ConsoleItem
	 */
	protected $consoleItem;
	/**
	 * Constructor.
	 *
	 */
	public function __construct()
	{
		//	init
		parent::__construct();

		$this -> consoleItem = new BASE_CMP_ConsoleItem();

		$this -> addClass('ow_console_button');

		$staticUrl = OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl();
		$document = OW::getDocument();
		$document -> addScript($staticUrl . 'js/ynsocialconnect.js');

		//	process
		$iLimit = OW::getConfig() -> getValue('ynsocialconnect', 'limit_providers_view_on_login_header');
		$iLimitSelected = OW::getConfig() -> getValue('ynsocialconnect', 'limit_providers_view_on_login_header');

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
		
		$minusSign = '';
		if(22 < $iIconSize){
			$minusSign = '-';
			$marginTop = ceil(($iIconSize - 22)/2);
			$marginTop = $minusSign . $marginTop;
		}else{
			$marginTop = ceil((22 - $iIconSize)/2);	
		}
		
		$this -> assign('marginTop', $marginTop);
		$this -> assign('iLimitView', $iLimit);
		$this -> assign('iLimitSelected', $iLimitSelected);
		$this -> assign('aOpenProviders', $listProvider);
		$this -> assign('iIconSize', $iIconSize);
		$this -> assign('iWidth', $iWidth);
		$this -> assign('sCoreUrl', OW_DIR_ROOT);
		$this -> assign('sImgSrc', OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl() . 'img/');

		$this -> addComponent('eicmp', new YNSOCIALCONNECT_CMP_ViewMore());
		YNSOCIALCONNECT_CLASS_Helper::getInstance() -> setIsViewMore('1');

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
	}

	public function addClass($class)
	{
		$this -> consoleItem -> addClass($class);
	}

	public function render()
	{
		$this -> consoleItem -> setControl(parent::render());

		return $this -> consoleItem -> render();
	}

}

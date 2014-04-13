<?php
class YNSOCIALCONNECT_CTRL_Userlinking  extends OW_ActionController
{
	public function __construct()
	{
		parent::__construct();

		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}

		$this -> setPageHeading(OW::getLanguage() -> text('ynsocialconnect', 'h_account_linking'));
		$this -> setPageHeadingIconClass('ow_ic_gear_wheel');

		//	Preference menu
		$contentMenu = new BASE_CMP_PreferenceContentMenu();
		$contentMenu -> getElement('ynsc_user_linking') -> setActive(true);
		$this -> addComponent('contentMenu', $contentMenu);
	}

	public function index()
	{
		//	init

		//	process
		////	get all account linking
		$aUserLinking = YNSOCIALCONNECT_BOL_UserlinkingService::getInstance() -> findByUserId(OW::getUser() -> getId());
		$listUserLinking = array();
		$serviceUserLinking = array();
		$service = '';
		foreach ($aUserLinking as $link)
		{
			if ($service != $link['name'])
			{
				$service = $link['name'];
				$serviceUserLinking[] = $link['name'];

				$obj = array();
				$obj['userLinkingId'] = $link['id'];
				$obj['userId'] = $link['userId'];
				$obj['identity'] = $link['identity'];
				$obj['serviceId'] = $link['serviceId'];
				$obj['serviceName'] = $link['name'];
				$obj['serviceTitle'] = $link['title'];

				$tokenData = unserialize(base64_decode($link['tokenData']));
				$infor = YNSOCIALCONNECT_CLASS_SocialConnect::getInstance() -> getAccountLinkingInfor($tokenData, $link['name']);
				$obj['profile_image'] = $infor['profile_image'];
				$obj['profile_full_name'] = $infor['profile_full_name'];

				$listUserLinking[] = $obj;
			}
		}

		$aOpenProviders = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvidersByStatus($bDisplay = true);
		$listProvider = array();
		foreach ($aOpenProviders as $provider)
		{
			if (in_array($provider -> getName(), $serviceUserLinking) === false)
			{
				$obj = array();
				$obj['name'] = $provider -> getName();
				$obj['title'] = $provider -> getTitle();
				$obj['id'] = $provider -> getId();

				$listProvider[] = $obj;
			}
		}

		// 	end
		$this -> assign('sImgSrc', OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl() . 'img/');
		$this -> assign('listUserLinking', $listUserLinking);
		$this -> assign('listProvider', $listProvider);
		//	load css
		$jsUrl = OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticJsUrl() . 'ynsocialconnect.js';
		$cssUrl = OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticCssUrl() . 'ynsocialconnect.css';
		OW::getDocument() -> addStyleSheet($cssUrl);
		OW::getDocument() -> addScript($jsUrl);
		OW::getLanguage()->addKeyForJs('ynsocialconnect', 'txt_confirm_disconnect_acc_linking');
	}

	public function disconnect($params)
	{
		//	init
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		if (!isset($params['userLinkingId']) || !strlen(trim($params['userLinkingId'])))
		{
			$this -> redirect(OW::getRouter() -> urlForRoute('ynsocialconnect_user_user_linking'));
		}
		if (!isset($params['service']) || !strlen(trim($params['service'])))
		{
			$this -> redirect(OW::getRouter() -> urlForRoute('ynsocialconnect_user_user_linking'));
		}
		$userLinkingId = (int)$params['userLinkingId'];
		$service = $params['service'];
		$userId = (int)OW::getUser() -> getId();

		//	process
		$provider = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvider($service);

		////	remove ow_base_remote_auth
		$listRemoteAuth = YNSOCIALCONNECT_BOL_ExtendsRemoteAuthService::getInstance() -> findAll();
		$deletedRemoteAuth = NULL;
		foreach ($listRemoteAuth as $val)
		{
			if ($val -> userId == $userId && $val -> type == $service)
			{
				YNSOCIALCONNECT_BOL_ExtendsRemoteAuthService::getInstance() -> deleteById($val -> id);
				break;
			}
		}

		////	remove agent in social connect
		YNSOCIALCONNECT_BOL_AgentsService::getInstance() -> deleteByUserIdAndServiceId($userId, $provider -> id);

		////	remove account linking
		YNSOCIALCONNECT_BOL_UserlinkingService::getInstance() -> deleteById($userLinkingId);

		//// 	clear session
		if (isset($_SESSION['socialbridge_session'][$service]))
		{
			unset($_SESSION['socialbridge_session'][$service]);
		}

		//	end
		$this -> redirect(OW::getRouter() -> urlForRoute('ynsocialconnect_user_user_linking'));
	}

	public function linking($params)
	{
		if (!OW::getUser() -> isAuthenticated())
		{
			$this -> redirect(OW_URL_HOME);
			//throw new AuthenticateException();
		}
		if (!isset($params['service']) || !strlen(trim($params['service'])))
		{
			$this -> redirect(OW_URL_HOME);
			//throw new Redirect404Exception();
		}
		//	init
		$oBridge = YNSOCIALCONNECT_CLASS_SocialBridge::getInstance();
		$sService = isset($params['service']) ? strtolower($params['service']) : null;
		$sIdentity = null;
		$data = NULL;
		$type = 'bridge';
		$sUrlRedirect = OW::getRouter() -> urlForRoute('ynsocialconnect_user_user_linking');
		//	process
		if ($oBridge -> hasProvider($sService))
		{
			//	process Facebook, Twitter, LinkedIn

			$profile = null;
			//check enable API
			$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($sService);
			if ($clientConfig)
			{
				$core = new YNSOCIALBRIDGE_CLASS_Core();
				$oProvider = $core -> getInstance($sService);

				if (!empty($_SESSION['socialbridge_session'][$sService]))
				{
					try
					{
						$profile = $oProvider -> getOwnerInfo($_SESSION['socialbridge_session'][$sService]);
					} catch(Exception $e)
					{
						$profile = null;
					}
				}

				//
				if ($profile)
				{
					$sIdentity = isset($profile['identity']) ? $profile['identity'] : null;
					////	filter data
					$profile = YNSOCIALCONNECT_CLASS_SocialConnect::getInstance() -> filterProfile($profile, $sService);
					$data = $profile;
				}
			}

		} else
		{
			//	process with other services
			$type = 'not_bridge';
			$sIdentity = isset($_REQUEST['identity']) ? $_REQUEST['identity'] : null;
			$data = $_REQUEST;

		}
		
		if (NULL == $sIdentity)
		{
			$type = 'close_not_loading';
		} else {
			////		update ow_base_remote_auth
			$authAdapter = new YNSOCIALCONNECT_CLASS_AuthAdapter($sIdentity, $sService);
			$authAdapter -> register(OW::getUser() -> getId());
	
			////		update agent table in social connect
			$provider = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvider($sService);
	
			$entity = new YNSOCIALCONNECT_BOL_Agents();
			$entity -> userId = (int)OW::getUser() -> getId();
			$entity -> identity = $sIdentity;
			$entity -> serviceId = $provider -> id;
			$entity -> ordering = 0;
			$entity -> status = 'linking';
			$entity -> login = '0';
			$entity -> data = base64_encode(serialize($data));
			$entity -> tokenData = base64_encode(serialize($data));
			$entity -> token = time();
			$entity -> createdTime = time();
			$entity -> loginTime = time();
			$entity -> logoutTime = time();
			YNSOCIALCONNECT_BOL_AgentsService::getInstance() -> save($entity);
	
			////		delete old token and add new token in social bridge
			if ($oBridge -> hasProvider($sService))
			{
				//	remove old token
				$values = array(
					'service' => $sService,
					'userId' => OW::getUser() -> getId()
				);
				$tokenDto = $oProvider -> getToken($values);
				if ($tokenDto)
				{
					YNSOCIALBRIDGE_BOL_TokenService::getInstance() -> delete($tokenDto);
				}
				//	add new token
				$oProvider -> saveToken();
			}
	
			////		add user linking
			$entityUserlinking = new YNSOCIALCONNECT_BOL_Userlinking();
			$entityUserlinking -> userId = (int)OW::getUser() -> getId();
			$entityUserlinking -> identity = $sIdentity;
			$entityUserlinking -> serviceId = $provider -> id;
			YNSOCIALCONNECT_BOL_UserlinkingService::getInstance() -> save($entityUserlinking);			
		}

		//	end
		//// 	clear session
		if (isset($_SESSION['socialbridge_session'][$sService]))
		{
			unset($_SESSION['socialbridge_session'][$sService]);
		}
		$this -> assign('type', $type);
		$this -> assign('sUrlRedirect', $sUrlRedirect);
	}

}

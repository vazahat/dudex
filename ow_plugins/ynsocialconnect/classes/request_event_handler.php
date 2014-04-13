<?php

class YNSOCIALCONNECT_CLASS_RequestEventHandler
{
	/**
	 * Class instance
	 *
	 * @var YNSOCIALCONNECT_CLASS_RequestEventHandler
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_CLASS_RequestEventHandler
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}

		return self::$classInstance;
	}

	const CONSOLE_ITEM_KEY = 'ynsocialconnect';

	private function __construct()
	{

	}

	public function collectItems(BASE_CLASS_ConsoleItemCollector $event)
	{
		if (OW::getUser() -> isAuthenticated() == false)
		{
			$item = new YNSOCIALCONNECT_CMP_ViewLoginHeader();
			$position = OW::getConfig() -> getValue('ynsocialconnect', 'position_providers_on_header');
			$event -> addItem($item, (int)$position - 1);
		}
	}

	public function init()
	{
		$this->genericInit();
		
		OW::getEventManager() -> bind('console.collect_items', array(
			$this,
			'collectItems'
		));
	}
	
    public function genericInit()
    {
        OW::getEventManager()->bind('base.members_only_exceptions', array($this, "onCollectAccessExceptions"));
        OW::getEventManager()->bind('base.password_protected_exceptions', array($this, "onCollectAccessExceptions"));
        OW::getEventManager()->bind('base.splash_screen_exceptions', array($this, "onCollectAccessExceptions"));
    }
	
    public function onCollectAccessExceptions( BASE_CLASS_EventCollector $e ) {
        $e->add(array('controller' => 'YNSOCIALCONNECT_CTRL_SocialConnect', 'action' => 'index'));
		$e->add(array('controller' => 'YNSOCIALBRIDGE_CTRL_Socialbridge', 'action' => 'connectFacebook'));
		$e->add(array('controller' => 'YNSOCIALBRIDGE_CTRL_Socialbridge', 'action' => 'connectTwitter'));
		$e->add(array('controller' => 'YNSOCIALBRIDGE_CTRL_Socialbridge', 'action' => 'connectLinkedin'));
		$e->add(array('controller' => 'YNSOCIALCONNECT_CTRL_Sync', 'action' => 'index'));
		$e->add(array('controller' => 'YNSOCIALCONNECT_CTRL_Sync', 'action' => 'removeAvatar'));
    }

	public function onUserRegister(OW_Event $event)
	{
		//	init
		$params = $event -> getParams();
		$signupData = OW::getSession() -> get(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA);

		if (NULL != $params && is_array($params) && NULL != $signupData)
		{
			//	process
			////		update ow_base_remote_auth
			$authAdapter = new YNSOCIALCONNECT_CLASS_AuthAdapter($signupData['identity'], $signupData['service']);
			$authAdapter -> register($params['userId']);

			////		update agent table in social connect
			$provider = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvider($signupData['service']);

			$entity = new YNSOCIALCONNECT_BOL_Agents();
			$entity -> userId = (int)$params['userId'];
			$entity -> identity = $signupData['identity'];
			$entity -> serviceId = $provider -> id;
			$entity -> ordering = 0;
			$entity -> status = 'login';
			$entity -> login = '1';
			$entity -> data = base64_encode(serialize($signupData['user']));
			$entity -> tokenData = base64_encode(serialize($signupData['user']));
			$entity -> token = time();
			$entity -> createdTime = time();
			$entity -> loginTime = time();
			$entity -> logoutTime = time();
			YNSOCIALCONNECT_BOL_AgentsService::getInstance() -> save($entity);

			////add token in social bridge
			$oBridge = YNSOCIALCONNECT_CLASS_SocialBridge::getInstance();
			if ($oBridge -> hasProvider(strtolower($signupData['service'])))
			{
				$core = new YNSOCIALBRIDGE_CLASS_Core();
				$obj = $core -> getInstance($signupData['service']);
				$obj -> saveToken();
			}

			////update signup statistic
			YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> updateStatistics($signupData['service'], 'signup');

			////add user linking
			$entityUserlinking = new YNSOCIALCONNECT_BOL_Userlinking();
			$entityUserlinking -> userId = (int)$params['userId'];
			$entityUserlinking -> identity = $signupData['identity'];
			$entityUserlinking -> serviceId = $provider -> id;
			YNSOCIALCONNECT_BOL_UserlinkingService::getInstance() -> save($entityUserlinking);

			//	end
			if (isset($_SESSION['socialbridge_session'][$signupData['service']]))
			{
				unset($_SESSION['socialbridge_session'][$signupData['service']]);
			}
			////		clear session
			OW::getSession() -> delete(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA);

			//	update avatar
			////	if avatar doesn't exist, update with profile image
			if (in_array($signupData['service'], array(
				'facebook',
				'linkedin',
				'twitter'
			)))
			{
					$useProfilePhoto = OW::getSession() -> get(YNSOCIALCONNECT_CMP_ViewInJoinPage::SESSION_USE_PROFILE_PHOTO);
					if ($useProfilePhoto == null || $useProfilePhoto != 'not_use')
					{
						$avatar = BOL_AvatarService::getInstance() -> findByUserId($params['userId']);
						if (!$avatar)
						{
							$profilePicture = YNSOCIALCONNECT_CLASS_SocialConnect::getInstance() -> getPhotoUrlFromTokenData($signupData['user'], $signupData['service']);

							//	with facebook, linkedin, twitter
							if ($profilePicture != null)
							{
								$pluginfilesDir = Ow::getPluginManager() -> getPlugin('ynsocialconnect') -> getPluginFilesDir();
								$tmpImgPath = $pluginfilesDir . 'img_' . uniqid() . '.jpg';
								YNSOCIALCONNECT_CLASS_SocialConnect::getInstance() -> fetchImage($profilePicture, $tmpImgPath);
								BOL_AvatarService::getInstance() -> setUserAvatar($params['userId'], $tmpImgPath);
								@unlink($tmpImgPath);
							}
						}
					}
			}
			if(isset($params['quick_signup']) && $params['quick_signup'])
			{
				$userId = (int) $params['userId'];
			    $event = new OW_Event('feed.action', array(
		                'pluginKey' => 'base',
		                'entityType' => 'user_join',
		                'entityId' => $userId,
		                'userId' => $userId,
		                'replace' => true
		                ), array(
		                'string' => OW::getLanguage()->text('base', 'feed_user_join'),
		                'view' => array(
		                    'iconClass' => 'ow_ic_user'
		                )
		            ));
		        OW::getEventManager()->trigger($event);
			}
		}
	}

	public function onAddButton(BASE_CLASS_EventCollector $event)
	{
		$cssUrl = OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticCssUrl() . 'ynsocialconnect.css';
		OW::getDocument() -> addStyleSheet($cssUrl);

		$button = new YNSOCIALCONNECT_CMP_ViewLoginSignIn();
		$event -> add(array(
			'iconClass' => 'ynsc_item_sign_in_hidden',
			'markup' => $button -> render()
		));
	}

	public function onUserUnregister(OW_Event $event)
	{
		$params = $event -> getParams();
		$userId = (int)$params['userId'];
		//	delete agent
		YNSOCIALCONNECT_BOL_AgentsService::getInstance() -> deleteByUserId($userId);
		//	delete ALL user linking
		YNSOCIALCONNECT_BOL_UserlinkingService::getInstance() -> deleteByUserId($userId);
	}

	public function onUserLogout(OW_Event $event)
	{
		//	clear all data on session
		$key = 'joinData';
		$config = OW::getSession();
		if ($config -> isKeySet($key))
		{
			$config -> delete($key);
		}
		if ($config -> isKeySet(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA))
		{
			$config -> delete(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA);
		}
	}

	public function basePreferenceMenuItems(BASE_EventCollector $event)
	{
		//	init
		$router = OW_Router::getInstance();
		$language = OW::getLanguage();
		$menuItems = array();

		//	process
		$menuItem = new BASE_MenuItem();
		$menuItem -> setKey('ynsc_user_linking');
		$menuItem -> setLabel(OW::getLanguage() -> text('ynsocialconnect', 'menu_account_linking'));
		$menuItem -> setIconClass('ow_ic_moderator');
		$menuItem -> setUrl($router -> urlForRoute('ynsocialconnect_user_user_linking'));
		$menuItem -> setOrder(99);

		$event -> add($menuItem);
		//	end
	}
	
	public function addAdminNotification(BASE_CLASS_EventCollector $e)
	{		
		if (OW::getPluginManager() -> isPluginActive('ynsocialbridge') == false)
		{
			$language = OW::getLanguage();
			$e->add($language->text('ynsocialconnect', 'requires_configuration_message'));	
		}	
	}

}

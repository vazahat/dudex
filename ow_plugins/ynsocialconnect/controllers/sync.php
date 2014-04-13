<?php

class YNSOCIALCONNECT_CTRL_Sync  extends OW_ActionController
{
	const SESSION_SIGNUP_DATA = 'ynsc_signup';

	public function index($params)
	{
		//	init
		if (OW::getUser() -> isAuthenticated())
		{
			$this -> redirect(OW_URL_HOME);
			//throw new AuthenticateException();
		}
		if (!isset($params['service']) || !strlen(trim($params['service'])))
		{
			$this -> redirect(OW_URL_HOME);
			//throw new Redirect404Exception();
		}

		$oBridge = YNSOCIALCONNECT_CLASS_SocialBridge::getInstance();
		//	process
		$sService = isset($params['service']) ? strtolower($params['service']) : null;
		$type = 'bridge';
		$sIdentity = null;
		$data = NULL;
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
				$values = array(
					'service' => $sService,
					'userId' => OW::getUser() -> getId()
				);
				$tokenDto = $oProvider -> getToken($values);

				if (!empty($_SESSION['socialbridge_session'][$sService]))
				{
					try
					{
						$profile = $oProvider -> getOwnerInfo($_SESSION['socialbridge_session'][$sService]);
					} catch(Exception $e)
					{
						$profile = null;
					}
				} else if ($tokenDto)
				{
					$profile = $oProvider -> getOwnerInfo(array(
						'access_token' => $tokenDto -> accessToken,
						'secret_token' => $tokenDto -> secretToken,
						'user_id' => $tokenDto -> uid
					));
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
		} 
		else
		{
			//	process with other services
			$type = 'not_bridge';
			$sIdentity = isset($_REQUEST['identity']) ? $_REQUEST['identity'] : null;
			$data = $_REQUEST;

		}
		$provider = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvider($sService);
		$aUser = YNSOCIALCONNECT_BOL_AgentsService::getInstance() -> getUserByIdentityAndService($sIdentity, $sService, $provider -> id, $data);
		$sUrlRedirect = '';
		if (NULL == $aUser && NULL == $sIdentity)
		{
			if ($oBridge -> hasProvider($sService))
			{
				$type = 'close_not_loading';
				$this -> assign('type', $type);
				$this -> assign('sUrlRedirect', $sUrlRedirect);
			}
		}

		if ($aUser)
		{
			//	login again
			//	logining which happen in YNSOCIALCONNECT_BOL_AgentsDao after execute checkExistingAgent
			//	now, redirect by url in session
			// @formatter:off
			if(isset($_SESSION['ynsc_session']) 
				&& isset($_SESSION['ynsc_session']['urlRedirect'])
				&& strlen(trim($_SESSION['ynsc_session']['urlRedirect'])) > 0
				){
				$sUrlRedirect = $_SESSION['ynsc_session']['urlRedirect'];
			} else {
				$sUrlRedirect = OW_URL_HOME;
			}
			// @formatter:on
			//	update login statistic
			YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> updateStatistics($sService, 'login');

		} 
		else
		{
			//	sign up now
			//	saved data to session
			try
			{
				OW::getSession() -> set(self::SESSION_SIGNUP_DATA, array(
					'service' => $sService,
					'identity' => $sIdentity,
					'user' => $data
				));
			} 
			catch(Exception $e)
			{

			}
			$sUrlRedirect = OW::getRouter() -> urlForRoute('base_join');

			//	mapping profile in session
			$questions = $this -> __mappingProfile($data, $sService);
			//	update later signup statistic in quick signup

			//	check existed user by email
			$checkExist = false;
			$checkEmail = false;
			if (isset($data['email']))
			{
				$email = $data['email'];
				$aUser = BOL_UserService::getInstance() -> findByEmail($email);
				if ($aUser)
				{
					//	redirect to synchronize page
					$sUrlRedirect = OW::getRouter() -> urlFor('YNSOCIALCONNECT_CTRL_UserSync', 'index');
					$checkExist = true;
				}
				if($data['email'])
				{
					$checkEmail = true;
				}
			}
			
			$plugin = OW::getPluginManager() -> getPlugin('ynsocialconnect');
			$key = strtolower($plugin -> getKey());
			if(!OW::getConfig() -> getValue($key, 'signup_mode') && !$checkExist && $checkEmail && $questions['username'])
			{
				$username = $questions['username'];
        		$password = uniqid();
				$user = BOL_UserService::getInstance()->createUser($username, $password, $questions['email'], null, true);
				BOL_QuestionService::getInstance()->saveQuestionsData(array_filter($questions), $user->id);
			
				OW_User::getInstance() -> login($user->id);
				
	            $event = new OW_Event(OW_EventManager::ON_USER_REGISTER, array(
	                'userId' => $user->id,
	                'quick_signup' => true
	            ));
	            OW::getEventManager()->trigger($event);
				$sUrlRedirect = OW_URL_HOME;
			}
		}

		$this -> assign('type', $type);
		$this -> assign('sUrlRedirect', $sUrlRedirect);
		// 	end

	}

	private function __mappingProfile($profile, $sService)
	{
		$key = 'joinData';
		$joinData = OW::getSession() -> get($key);
		if (!isset($joinData) || !is_array($joinData))
		{
			$joinData = array();
		}
		
		$fields = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> findAliasList($sService);
		foreach ($fields as $question => $field) 
		{
			switch ($question) 
			{
				case 'username':
					//	username
					if (isset($profile[$field]))
					{
						$joinData[$question] = preg_replace("/[^A-Za-z0-9\+]/", "", $profile[$field]);
					} 
					break;
				default:
					if(isset($profile[$field]))
					{
						$joinData[$question] = $profile[$field];
					}
					break;
			}
			
		}
		//	set into session
		try
		{
			OW::getSession() -> set($key, $joinData);
		} catch(Exception $e)
		{
		}
		//session_write_close();
		return $joinData;
	}

	public function removeAvatar()
	{
		OW::getSession() -> set(YNSOCIALCONNECT_CMP_ViewInJoinPage::SESSION_USE_PROFILE_PHOTO, 'not_use');
		exit();
	}

}

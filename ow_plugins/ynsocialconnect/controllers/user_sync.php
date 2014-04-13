<?php

class YNSOCIALCONNECT_CTRL_UserSync  extends OW_ActionController
{
	public function index()
	{
		$this -> setPageHeading(OW::getLanguage() -> text('ynsocialconnect', 'h_synchronize_user'));
		//	init
		if (OW::getUser() -> isAuthenticated())
		{
			$this -> redirect(OW_URL_HOME);
			//throw new AuthenticateException();
		}

		$oBridge = YNSOCIALCONNECT_CLASS_SocialBridge::getInstance();
		$signupData = OW::getSession() -> get(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA);

		//	process
		$form = new YNSOCIALCONNECT_CLASS_UserSyncForm();
		$email = $signupData['user']['email'];
		if(!$email){
			$email = '';
		}

		if (OW::getRequest() -> isPost() && $form -> isValid($_POST))
		{
			$data = $form -> getValues();
			if ($data['form_name'] == YNSOCIALCONNECT_CLASS_UserSyncForm::FORM_NAME)
			{
				if (isset($_POST['no']))
				{
					//	sign up
					$this -> redirect(OW::getRouter() -> urlForRoute('base_join'));
				}
				if (isset($_POST['synchronize']))
				{
					//	synchronize with existed account
					$aUser = BOL_UserService::getInstance() -> findByEmail($email);
					$sUrlRedirect = '';
					if ($aUser)
					{
						////		update ow_base_remote_auth
						$authAdapter = new YNSOCIALCONNECT_CLASS_AuthAdapter($signupData['identity'], $signupData['service']);
						$authAdapter -> register($aUser -> id);

						////	add agent
						$provider = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvider($signupData['service']);

						$entity = new YNSOCIALCONNECT_BOL_Agents();
						$entity -> userId = (int)$aUser -> id;
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

						//		add token in social bridge
						if ($oBridge -> hasProvider(strtolower($signupData['service'])))
						{
							$core = new YNSOCIALBRIDGE_CLASS_Core();
							$oProvider = $core -> getInstance($signupData['service']);
							$values = array(
								'service' => strtolower($signupData['service']),
								'userId' => $aUser -> id
							);
							$tokenDto = $oProvider -> getToken($values);
							if (!$tokenDto)
							{
								//
								$obj = $core -> getInstance($signupData['service']);
								$obj -> saveToken();
							}
						}						////	update statistics with "sync" type
						YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> updateStatistics($signupData['service'], 'sync');

						////		add user linking
						$entityUserlinking = new YNSOCIALCONNECT_BOL_Userlinking();
						$entityUserlinking -> userId = (int)$aUser -> id;
						$entityUserlinking -> identity = $signupData['identity'];
						$entityUserlinking -> serviceId = $provider -> id;
						YNSOCIALCONNECT_BOL_UserlinkingService::getInstance() -> save($entityUserlinking);

						////	clear sign up session
						OW::getSession() -> delete(YNSOCIALCONNECT_CTRL_Sync::SESSION_SIGNUP_DATA);

						////	redirect
						$authResult = OW::getUser() -> authenticate($authAdapter);
						if ($authResult -> isValid())
						{
							if (isset($_SESSION['ynsc_session']) && isset($_SESSION['ynsc_session']['urlRedirect']) && strlen(trim($_SESSION['ynsc_session']['urlRedirect'])) > 0)
							{
								$sUrlRedirect = $_SESSION['ynsc_session']['urlRedirect'];
							} else
							{
								$sUrlRedirect = OW_URL_HOME;
							}
							$this -> redirect($sUrlRedirect);
						} else
						{
							$this -> redirect(OW_URL_HOME);
						}
					}
				}
			}

		}

		// 	end
		//// 	clear session
		if (isset($_SESSION['socialbridge_session'][$signupData['service']]))
		{
			unset($_SESSION['socialbridge_session'][$signupData['service']]);
		}
		$this -> addForm($form);
		$this -> assign('email', $email);
		$this -> assign('question', OW::getLanguage() -> text('ynsocialconnect', 'txt_sync_question', array('email' => $email)));
	}

}

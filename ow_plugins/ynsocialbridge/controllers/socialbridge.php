<?php
class YNSOCIALBRIDGE_CTRL_Socialbridge extends OW_ActionController
{
	private $menu;
	public function __construct()
	{
		$this -> setPageHeading(OW::getLanguage() -> text('ynsocialbridge', 'socialbridge_management_page_heading'));
		$this -> setPageHeadingIconClass('ow_yn_socialbridge');

		//Preference menu
		$contentMenu = new BASE_CMP_PreferenceContentMenu();
		$contentMenu -> getElement('socialbridge') -> setActive(true);
		$this -> addComponent('contentMenu', $contentMenu);
		
		$core = new YNSOCIALBRIDGE_CLASS_Core();
		$this->menu = $core->initMenu();
		$this -> addComponent('menu', $this->menu);

		//load css
		$cssUrl = OW::getPluginManager() -> getPlugin('ynsocialbridge') -> getStaticCssUrl() . 'ynsocialbridge.css';
		OW::getDocument() -> addStyleSheet($cssUrl);

		//put languages to database when chage
		OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('ynsocialbridge')->getRootDir() .
		'langs.zip','ynsocialbridge');
	}

	// manage connections
	public function index($params)
	{
		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}
		$el = $this->menu->getElement('connects');
		 if ( $el )
        {
            $el->setActive(true);
        }
		//get callback URL
		$callbackUrl = OW::getRouter() -> urlForRoute('ynsocialbridge-connects');
		$core = new YNSOCIALBRIDGE_CLASS_Core();

		$arrObjServices = array();
		foreach (array('facebook', 'twitter', 'linkedin') as $serviceName)
		{
			//check enable API
			$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($serviceName);
			if ($clientConfig -> apiParams)
			{
				$params = unserialize($clientConfig -> apiParams);
				if(!$params['key'] || !$params['secret'])
					continue;
				$obj = $core -> getInstance($serviceName);
				$values = array(
					'service' => $serviceName,
					'userId' => OW::getUser() -> getId()
				);
				$tokenDto = $obj -> getToken($values);
				$profile = null;
				$connect_url = "";
				$disconnect_url = OW::getRouter() -> urlForRoute('ynsocialbridge-disconnect') . "?service=" . $serviceName;
				if ($tokenDto)
				{
					if($serviceName == 'facebook')
					{
						$permissions = $obj -> hasPermission(array(
								'uid' => $tokenDto -> uid,
								'access_token' => $tokenDto -> accessToken
							));
						if($permissions)
						{
							$profile = @$obj -> getOwnerInfo(array(
								'access_token' => $tokenDto -> accessToken,
								'secret_token' => $tokenDto -> secretToken,
								'user_id' => $tokenDto -> uid
							));
							$_SESSION['socialbridge_session']['facebook']['access_token'] = $tokenDto -> accessToken;
						}
						else 
						{
							YNSOCIALBRIDGE_BOL_TokenService::getInstance() -> delete($tokenDto);
							$scope = "email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_photos,user_website";
							$connect_url = $obj -> getConnectUrl() . "?scope=" . $scope . "&" . http_build_query(array('callbackUrl' => $callbackUrl));
						}
					}
					else 
					{
						$profile = @$obj -> getOwnerInfo(array(
							'access_token' => $tokenDto -> accessToken,
							'secret_token' => $tokenDto -> secretToken,
							'user_id' => $tokenDto -> uid
						));
						$_SESSION['socialbridge_session'][$serviceName]['access_token'] =  $tokenDto -> accessToken;						
						$_SESSION['socialbridge_session'][$serviceName]['secret_token'] = $tokenDto -> secretToken;
						$_SESSION['socialbridge_session'][$serviceName]['user_id'] = $tokenDto -> uid;
					}
				}
				else
				{
					$scope = "";
					switch ($serviceName)
					{
						case 'facebook' :
							$scope = "email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_photos,user_website";
							break;

						case 'twitter' :
							$scope = "";
							break;

						case 'linkedin' :
							$scope = "r_basicprofile,rw_nus,r_network,w_messages";
							break;
					}
					$connect_url = $obj -> getConnectUrl() . "?scope=" . $scope . "&" . http_build_query(array('callbackUrl' => $callbackUrl));
				}
				$objService['serviceName'] = $serviceName;
				$objService['connectUrl'] = $connect_url;
				$objService['disconnectUrl'] = $disconnect_url;
				$objService['profile'] = $profile;
				$objService['logo'] = OW::getPluginManager() -> getPlugin('ynsocialbridge') -> getStaticUrl() . "img/" . $serviceName . ".jpg";
				$arrObjServices[] = $objService;

			}
		}
		//assign to view page
		$this -> assign('arrObjServices', $arrObjServices);
		$this -> assign('noImageUrl', OW::getPluginManager() -> getPlugin('ynsocialbridge') -> getStaticUrl() . "img/default_user.jpg");
	}


	// connect to facebook
	public function connectFacebook()
	{
		$scope = $_REQUEST['scope'];
		$is_agree = true;
		if (isset($_REQUEST['code']) && isset($_REQUEST['state']) && $_REQUEST['code'])
		{
			$core = new YNSOCIALBRIDGE_CLASS_Core();
			$obj = $core -> getInstance("facebook");
			$obj -> saveToken();
			$token = $obj -> getUserAccessToken();

			$_SESSION['socialbridge_session']['facebook']['access_token'] = $token;
			$_SESSION['socialbridge_session']['facebook']['access_token_time'] = time();
			if (!$token)
			{
				OW::getFeedback()->error("Invalid auth/bad request!");
				$this->redirect($_REQUEST['callbackUrl']);
			}

			try
			{
				$me = $obj -> _me;
				if (!$me)
				{
					OW::getFeedback()->error("Invalid auth/bad request!");
					$this->redirect($_REQUEST['callbackUrl']);
				}
			}
			catch(Exception $e)
			{
				$params['scope'] = $scope;
				$url = $obj -> getLoginUrl($params);
				$this -> redirect($url);
			}

		}
		else
		if (isset($_REQUEST['error']))
		{
			$callbackUrl = $_REQUEST['callbackUrl'];
			$this -> redirect($callbackUrl);			
		}
		else
		{
			$obj = YNSOCIALBRIDGE_CLASS_Core::getInstance("facebook");
			$params['scope'] = $scope;
			$sRedirectUrl = $obj -> getLoginUrl($params);
	
			$this -> redirect($sRedirectUrl);
		}
		$callbackUrl = $_REQUEST['callbackUrl'];
		$this -> assign('callbackUrl', $callbackUrl);
		

		$isFromSocialPublisher = isset($_REQUEST['isFromSocialPublisher'])?$_REQUEST['isFromSocialPublisher']:'';
		$pluginKey = isset($_REQUEST['pluginKey'])?$_REQUEST['pluginKey']:'';
		$entityId = isset($_REQUEST['entityId'])?$_REQUEST['entityId']:'';
		$entityType = isset($_REQUEST['entityType'])?$_REQUEST['entityType']:'';

		if (!empty($isFromSocialPublisher) && !empty($pluginKey) && !empty($entityId) && !empty($entityType))
		{
		    $script = "self.close();opener.parent.OWActiveFloatBox.close();
                        opener.parent.OW.ajaxFloatBox('YNSOCIALPUBLISHER_CMP_Popup', {pluginKey :'$pluginKey', entityType: '$entityType', entityId: $entityId}, {width:620, height:560, iconClass: 'ow_ic_user', title: ''});";

		    OW::getDocument()->addOnloadScript($script);
		}
	}

	// connect to twitter
	public function connectTwitter()
	{
		$returnURL = $_REQUEST['callbackUrl'];
		$core = new YNSOCIALBRIDGE_CLASS_Core();
		$obj = $core -> getInstance("twitter");
		// authorize
		if (!isset($_REQUEST['oauth_token']))
		{
			$url = $this -> curPageURL();
			$obj -> authorizeRequest(array('url' => $url));
		}
		else
		{
			$params['oauth_token'] = $_REQUEST['oauth_token'];
			$params['oauth_verifier'] = $_REQUEST['oauth_verifier'];
			$response = $obj -> getAuthAccessToken($params);
			$_SESSION['socialbridge_session']['twitter']['access_token'] = $response['oauth_token'];
			$_SESSION['socialbridge_session']['twitter']['secret_token'] = $response['oauth_token_secret'];
			$_SESSION['socialbridge_session']['twitter']['owner_id'] = $response['user_id'];
			$obj -> saveToken();
			$this -> assign('callbackUrl', $returnURL);

			$isFromSocialPublisher = isset($_REQUEST['isFromSocialPublisher'])?$_REQUEST['isFromSocialPublisher']:'';
			$pluginKey = isset($_REQUEST['pluginKey'])?$_REQUEST['pluginKey']:'';
			$entityId = isset($_REQUEST['entityId'])?$_REQUEST['entityId']:'';
			$entityType = isset($_REQUEST['entityType'])?$_REQUEST['entityType']:'';

			if (!empty($isFromSocialPublisher) && !empty($pluginKey) && !empty($entityId) && !empty($entityType))
			{
                $script = "self.close();opener.parent.OWActiveFloatBox.close();
                        opener.parent.OW.ajaxFloatBox('YNSOCIALPUBLISHER_CMP_Popup', {pluginKey :'$pluginKey', entityType: '$entityType', entityId: $entityId}, {width:620, height:560, iconClass: 'ow_ic_user', title: ''});";

                OW::getDocument()->addOnloadScript($script);
			}
		}
	}

	// connect to linkedin
	public function connectLinkedin()
	{
		$url = $_GET['callbackUrl'];
		$core = new YNSOCIALBRIDGE_CLASS_Core();
		$obj = $core -> getInstance("linkedin");
		$_REQUEST[$obj -> getGetType()] = (isset($_REQUEST[$obj -> getGetType()])) ? $_REQUEST[$obj -> getGetType()] : 'initiate';
		if ($_REQUEST[$obj -> getGetType()] == 'initiate')
		{
			$_GET['oauth_callback'] = $this -> curPageURL() . '&' . $obj -> getGetType() . '=initiate&' . $obj -> getGetResponse() . '=1';
			$obj = $core -> getInstance("linkedin");
			$_GET[$obj -> getGetResponse()] = (isset($_GET[$obj -> getGetResponse()])) ? $_GET[$obj -> getGetResponse()] : '';
			if (!$_GET[$obj -> getGetResponse()])
			{
				$params = array();
				if ($_GET['scope'])
				{
					$params['url_request'] = 'https://api.linkedin.com/uas/oauth/requestToken?scope=' . $_GET['scope'];
				}
				$response = $obj -> retrieveTokenRequest($params);
				if ($response['success'] === TRUE)
				{
					// split up the response and stick the LinkedIn portion in the user session
					$_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];

					// redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
					$this -> redirect($obj -> getUrlAuth() . $_SESSION['oauth']['linkedin']['request']['oauth_token']);
				}
				else
				{
					//Error time timestamp_refused
					if (isset($response['linkedin']['oauth_problem']) && $response['linkedin']['oauth_problem'] == 'timestamp_refused')
					{
						$tmp = (int)$response['linkedin']['oauth_acceptable_timestamps'];
						$_SESSION['delta_time_stamp'] = $tmp - time();

						$response = $obj -> retrieveTokenRequest($params);
						if ($response['success'] === TRUE)
						{
							// split up the response and stick the LinkedIn portion in the user session
							$_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];

							// redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
							$this -> redirect($obj -> getUrlAuth() . $_SESSION['oauth']['linkedin']['request']['oauth_token']);
						}
					}
					if (isset($response['linkedin']['oauth_problem']) && $response['linkedin']['oauth_problem'] == 'signature_invalid')
					{
						OW::getFeedback()->error("Invalid auth/bad request!");
						$this->redirect($url);
					}
					// bad token request
				}
			}
			else
			{
				if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']))
				{
					// LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the
					// verifier to request the user's real secret key
					$response = $obj -> retrieveTokenAccess($_GET['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']);
					if ($response['success'] === TRUE)
					{
						// the request went through without an error, gather user's 'access' tokens
						$_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
						// set the user as authorized for future quick reference
						$_SESSION['oauth']['linkedin']['authorized'] = TRUE;

						// now we have the session 'access' tokens, request the linkedin id for the user and store that with keys in SESSION
						$response = $obj -> getProfile();
						if ($response['info']['http_code'] == 200)
						{
							if (class_exists('SimpleXMLElement'))
							{
								$response['linkedin'] = new SimpleXMLElement($response['linkedin']);
								$_SESSION['oauth']['linkedin']['id'] = (string)$response['linkedin'] -> id;
							}
							else
							{
								echo "Missing SimpleXMLElement class...  please install this extension or use a different method to process the XML response.";
							}
							$datatopost = array('_q' => 1);
							$index = 0;
							$datatopost['contact'] = "mycontact";
							$datatopost['service'] = "linkedin";
							$_SESSION['socialbridge_session']['linkedin']['access_token'] = $_SESSION['oauth']['linkedin']['access']['oauth_token'];
							$_SESSION['socialbridge_session']['linkedin']['secret_token'] = $_SESSION['oauth']['linkedin']['access']['oauth_token_secret'];
							$obj -> saveToken();
							$this -> assign('callbackUrl', $url);

							$isFromSocialPublisher = isset($_REQUEST['isFromSocialPublisher'])?$_REQUEST['isFromSocialPublisher']:'';
							$pluginKey = isset($_REQUEST['pluginKey'])?$_REQUEST['pluginKey']:'';
							$entityId = isset($_REQUEST['entityId'])?$_REQUEST['entityId']:'';
							$entityType = isset($_REQUEST['entityType'])?$_REQUEST['entityType']:'';

							if (!empty($isFromSocialPublisher) && !empty($pluginKey) && !empty($entityId) && !empty($entityType))
							{
                                $script = "self.close();opener.parent.OWActiveFloatBox.close();
                                            opener.parent.OW.ajaxFloatBox('YNSOCIALPUBLISHER_CMP_Popup', {pluginKey :'$pluginKey', entityType: '$entityType', entityId: $entityId}, {width:620, height:560, iconClass: 'ow_ic_user', title: ''});";

							    OW::getDocument()->addOnloadScript($script);
							}
						}
						else
						{
							// bad data returned from LinkedIn get call
							echo "Bad get data returned:\n\nRESPONSE:\n\n" . print_r($response, TRUE) . "\n\nLINKEDIN OBJ:\n\n" . print_r($obj, TRUE);
						}
					}
					else
					{
						// bad token access
						echo "Bad access token call:\n\nRESPONSE:\n\n" . print_r($response, TRUE) . "\n\nLINKEDIN OBJ:\n\n" . print_r($obj, TRUE);
					}
				}
				else
				{
					$this -> assign('callbackUrl', $url);
				}
			}
		}
	}

	//disconnect
	public function disconnect()
	{
		$serviceName = $_REQUEST['service'];
		$core = new YNSOCIALBRIDGE_CLASS_Core();
		//clear session
		if(isset($_SESSION['socialbridge_session'][$serviceName]))
			unset($_SESSION['socialbridge_session'][$serviceName]);

		//remove token
		$obj = $core -> getInstance($serviceName);
		$values = array(
			'service' => $serviceName,
			'userId' => OW::getUser() -> getId()
		);
		$tokenDto = $obj -> getToken($values);
		if($tokenDto)
			YNSOCIALBRIDGE_BOL_TokenService::getInstance() -> delete($tokenDto);
		$this -> redirect(OW::getRouter() -> urlForRoute('ynsocialbridge-connects'));
	}

	function curPageURL()
	{
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		}
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

}

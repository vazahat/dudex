<?php

defined('SOCIALCONNECT_CENTRALIZE_URL') or define('SOCIALCONNECT_CENTRALIZE_URL', 'http://openid.younetid.com/auth/phpfox.php');

class YNSOCIALCONNECT_CLASS_SocialConnect
{
	/**
	 * Class instance
	 *
	 * @var YNSOCIALCONNECT_CLASS_SocialConnect
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_CLASS_SocialConnect
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}

		return self::$classInstance;
	}

	public function __construct()
	{
	}

	public function getReturnUrl($sService, $sCallbackUrl = '')
	{
		$oBridge = YNSOCIALCONNECT_CLASS_SocialBridge::getInstance();
		$sUrl = '';

		if ($oBridge -> hasProvider($sService))
		{

			$core = new YNSOCIALBRIDGE_CLASS_Core();
			if (in_array(strtolower($sService), array(
				'facebook',
				'twitter',
				'linkedin'
			)))
			{
				//check enable API
				$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($sService);
				if ($clientConfig)
				{
					$obj = $core -> getInstance($sService);
					$tokenDto = null;
					// if (empty($_SESSION['socialbridge_session'][$sService]))
					// {
					// $values = array(
					// 'service' => $sService,
					// 'userId' => OW::getUser() -> getId()
					// );
					// $tokenDto = $obj -> getToken($values);
					// }
					$scope = "";
					switch ($sService)
					{
						case 'facebook' :
							$scope = "email,user_about_me,user_birthday,user_hometown,user_interests,user_location,user_photos,user_website";
							if (!empty($_SESSION['socialbridge_session'][$sService]['access_token']) || $tokenDto)
							{
								if ($tokenDto)
								{
									$_SESSION['socialbridge_session'][$sService]['access_token'] = $tokenDto -> accessToken;
								}
								$uid = $obj -> getOwnerId(array('access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']));
								$permissions = $obj -> hasPermission(array(
									'uid' => $uid,
									'access_token' => $_SESSION['socialbridge_session'][$sService]['access_token']
								));
								// @formatter:off
								if (empty($permissions[0]['email']) 
									|| empty($permissions[0]['user_about_me'])
									|| empty($permissions[0]['user_birthday'])
									|| empty($permissions[0]['user_hometown'])
									|| empty($permissions[0]['user_interests'])
									|| empty($permissions[0]['user_location'])
									|| empty($permissions[0]['user_photos'])
									|| empty($permissions[0]['user_website'])
									)
								{
									$connect_url = ($obj -> getConnectUrl() . '?scope=' . $scope . '&' . http_build_query(array('callbackUrl' => $sCallbackUrl)));
								} else
								{
									$connect_url = ($sCallbackUrl);
								}
								// @formatter:on
							} else
							{
								$connect_url = ($obj -> getConnectUrl() . '?scope=' . $scope . '&' . http_build_query(array('callbackUrl' => $sCallbackUrl)));
							}
							break;

						case 'twitter' :
							$scope = "";
							if (!empty($_SESSION['socialbridge_session'][$sService]['access_token']) || $tokenDto)
							{
								$connect_url = ($sCallbackUrl);
							} else
							{
								$connect_url = $obj -> getConnectUrl() . "?scope=" . $scope . "&" . http_build_query(array('callbackUrl' => $sCallbackUrl));
							}

							break;

						case 'linkedin' :
							$scope = "r_basicprofile,rw_nus,r_network,w_messages";
							if (!empty($_SESSION['socialbridge_session'][$sService]['access_token']) || $tokenDto)
							{
								$connect_url = ($sCallbackUrl);
							} else
							{
								$connect_url = $obj -> getConnectUrl() . "?scope=" . $scope . "&" . http_build_query(array('callbackUrl' => $sCallbackUrl));
							}

							break;
					}
					$sUrl = $connect_url;
				}

			}
		} else
		{
			$sUrl = SOCIALCONNECT_CENTRALIZE_URL . '?' . http_build_query(array(
				'service' => $sService,
				'returnurl' => $sCallbackUrl,
			));
		}
		return $sUrl;
	}

	public function getProfile($data, $sService = '')
	{
		$profile = array();
		// @formatter:off
		$key = array('username', 'email', 'displayname', 'gender', 'first-name', 'last-name'
				, 'id' , 'email-address', 'picture-url', 'link', 'name', 'screen_name', 'id_str'
				, 'profile_image_url', 'followers_count', 'identity'
		);
		// @formatter:on

		foreach ($key as $v)
		{
			if (isset($data[$v]))
			{
				$profile[$v] = $data[$v];
			}

		}

		return $profile;
	}

	public function filterProfile($data, $service = '')
	{
		if ($service == 'flickr2')
		{
			$service = 'flickr';
		}

		$infor = array();

		switch($service)
		{
			case 'linkedin' :
				// @formatter:off
				$key = array('id', 'first-name', 'last-name', 'formatted-name', 'headline', 'distance'
						, 'current-status' , 'current-status-timestamp', 'num-connections', 'num-connections-capped'
						, 'picture-url', 'public-profile-url', 'identity', 'first_name', 'last_name'
						, 'username', 'displayname', 'picture', 'photo_url', 'email', 'service'
				);
				// @formatter:on
				foreach ($key as $v)
				{
					if (isset($data[$v]))
					{
						$infor[$v] = $data[$v];
					}

				}
				break;
			default :
				$infor = $data;
				break;
		}
		return $infor;
	}

	public function getAccountLinkingInfor($token, $service = '')
	{
		if ($service == 'flickr2')
		{
			$service = 'flickr';
		}
		//	init
		$infor = array();
		$infor['profile_image'] = OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl() . 'img/no_image_icon.png';
		$infor['profile_full_name'] = '';

		//	process
		//	realname
		if (isset($token['displayname']))
		{
			// facebook, twitter, linkedin
			$infor['profile_full_name'] = $token['displayname'];
		} else if (isset($token['full_name']))
		{
			// other services
			$infor['profile_full_name'] = $token['full_name'];
		}

		if (isset($token['picture']))
		{
			$infor['profile_image'] = $token['picture'];
		} else if (isset($token['ProfileImageUrl']))
		{
			$infor['profile_image'] = $token['ProfileImageUrl'];
		} else if (isset($token['FlickProfileUrl']))
		{
			//$infor['profile_image'] = $token['FlickProfileUrl'];
		}

		//	end
		return $infor;
	}

	public function fetchImage($photo_url, $tmpfile)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $photo_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		$data = curl_exec($ch);
		curl_close($ch);

		@file_put_contents($tmpfile, $data);
	}

	public function getPhotoUrlFromTokenData($data, $service)
	{
		$profilePicture = null;
		switch ($service)
		{
			case 'facebook' :
				$profilePicture = isset($data['photo_url']) ? $data['photo_url'] : null;
				break;
			case 'linkedin' :
				$profilePicture = isset($data['photo_url']) ? $data['photo_url'] : null;
				break;
			case 'twitter' :
				$profilePicture = isset($data['photo_url']) ? $data['photo_url'] : null;
				break;

			default :
				$profilePicture = null;
				break;
		}

		return $profilePicture;
	}
	
	public function checkSocialBridgePlugin($provider)
	{
		if (!$plugin = BOL_PluginService::getInstance() -> findPluginByKey('ynsocialbridge'))
		{
			return false;
		}
		else
		{
			if (!$plugin->isActive() || !YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($provider))
			{
				return false;
			}
		}
		return true;
	}
	

}

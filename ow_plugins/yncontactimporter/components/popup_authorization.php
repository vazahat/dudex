<?php
class YNCONTACTIMPORTER_CMP_PopupAuthorization extends OW_Component
{
    public function __construct( $providerId )
    {
    	parent::__construct();
		$provider = YNCONTACTIMPORTER_BOL_ProviderService::getInstance()->findProviderById($providerId);
		$importUrl = OW::getRouter()->urlForRoute('yncontactimporter-import');
		$des = "";
		if($provider->type == 'social')
		{
			$des = OW::getLanguage() -> text('yncontactimporter', 'social_require', array('provider' => ucfirst($provider->name)));
		}
		else
		{
			$des = OW::getLanguage() -> text('yncontactimporter', 'email_login', array('provider' => ucfirst($provider->name)));
		}
		$this->assign("des", $des);
		//check permission to get contacts
		$url = "";
		
		if(in_array($provider->name, array('facebook', 'twitter', 'linkedin')))
		{
			$core = new YNSOCIALBRIDGE_CLASS_Core();
			$obj = $core -> getInstance($provider->name);
			$tokenDto = null;
			if(empty($_SESSION['socialbridge_session'][$provider->name]))
			{
				$values = array(
					'service' => $provider->name,
					'userId' => OW::getUser() -> getId()
				);
				$tokenDto = $obj -> getToken($values);
			}
		}
		switch ($provider->name) 
		{
			case 'facebook':
				if (!empty($_SESSION['socialbridge_session'][$provider->name]) || $tokenDto)
				{
					if($tokenDto)
					{
						$_SESSION['socialbridge_session'][$provider->name]['access_token'] = $tokenDto->accessToken;
					}
					$uid = $obj->getOwnerId(array('access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']));
					$permissions = $obj->hasPermission(array(
						            'uid' => $uid,
						            'access_token' => $_SESSION['socialbridge_session'][$provider->name]['access_token']
						            ));
			        if ( empty($permissions[0]['publish_stream']) || empty($permissions[0]['xmpp_login'])) 
			        {
						$url = $obj -> getConnectUrl() . '?scope=publish_stream,xmpp_login' . '&' . http_build_query(array('callbackUrl' => $importUrl));
					}
					else
					{
						$url = $importUrl."?service=".$provider->name;
					}
				}
				else 
				{
					$url = $obj -> getConnectUrl() . '?scope=publish_stream,xmpp_login' . '&' . http_build_query(array('callbackUrl' => $importUrl));
				}
			break;
			
			case'twitter':
				if (!empty($_SESSION['socialbridge_session'][$provider->name]) || $tokenDto)
				{
					if($tokenDto)
					{
						$_SESSION['socialbridge_session'][$provider->name]['access_token'] = $tokenDto->accessToken;
						$_SESSION['socialbridge_session'][$provider->name]['secret_token'] = $tokenDto->secretToken;
						$_SESSION['socialbridge_session'][$provider->name]['owner_id'] = $tokenDto->uid;
					}
					$url = $importUrl."?service=".$provider->name;
				}
				else 
				{
					$url = $obj -> getConnectUrl().'?' . http_build_query(array('callbackUrl' => $importUrl));
				}
			break;
				
			case 'linkedin':
				if (!empty($_SESSION['socialbridge_session'][$provider->name]) || $tokenDto)
				{
					if($tokenDto)
					{
						$_SESSION['socialbridge_session'][$provider->name]['access_token'] = $tokenDto->accessToken;
						$_SESSION['socialbridge_session'][$provider->name]['secret_token'] = $tokenDto->secretToken;
					}
					$url = $importUrl."?service=".$provider->name;
				}
				else 
				{
					$url = $obj -> getConnectUrl().'?scope=r_network,w_messages&' . http_build_query(array('callbackUrl' => $importUrl));
				}
			break;
			
			case 'hotmail':
				$url = "http://openid.younetid.com/v3/contact/index.php?service=live&login=1&".http_build_query(array(
					'callbackUrl' => $importUrl."?service=".$provider->name));
			break;
			
			default:
				$url = "http://openid.younetid.com/v3/contact/index.php?service=".$provider->name."&".http_build_query(array(
					'callbackUrl' => $importUrl."?service=".$provider->name));
			break;
		}
		$this->assign('importUrl', $url);
    }
}

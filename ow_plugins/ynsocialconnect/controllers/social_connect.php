<?php


class YNSOCIALCONNECT_CTRL_SocialConnect  extends OW_ActionController
{
	public function index($params)
	{
		//	init
		if (!isset($params['service']) || !strlen($service = trim($params['service'])))
		{
			$this -> redirect(OW_URL_HOME);
			//throw new Redirect404Exception();
		}
		$type = '';
		if (!isset($params['type']) || !strlen(trim($params['type'])))
		{
			$type = '';
		} else
		{
			$type = trim($params['type']);
		}

		//	process
		$sCallbackUrl = '';
		switch ($type)
		{
			case 'sync' :
				if (OW::getUser() -> isAuthenticated())
				{
					$this -> redirect(OW_URL_HOME);
				}

				$sCallbackUrl = OW::getRouter() -> urlFor('YNSOCIALCONNECT_CTRL_Sync', 'index', array('service' => $service));
				break;

			case 'linking' :
				if (!OW::getUser() -> isAuthenticated())
				{
					$this -> redirect(OW_URL_HOME);
				}
				$sCallbackUrl = OW::getRouter() -> urlFor('YNSOCIALCONNECT_CTRL_Userlinking', 'linking', array('service' => $service));
				break;

			default :
				if (OW::getUser() -> isAuthenticated())
				{
					$this -> redirect(OW_URL_HOME);
				}

				$sCallbackUrl = OW::getRouter() -> urlFor('YNSOCIALCONNECT_CTRL_Sync', 'index', array('service' => $service));
				break;
		}

	
		$sUrl = YNSOCIALCONNECT_CLASS_SocialConnect::getInstance() -> getReturnUrl($service, $sCallbackUrl);

		// 	end
		$this -> redirect($sUrl);
	}

}

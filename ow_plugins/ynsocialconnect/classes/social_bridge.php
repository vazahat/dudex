<?php

class YNSOCIALCONNECT_CLASS_SocialBridge
{
	/**
	 * Class instance
	 *
	 * @var YNSOCIALCONNECT_CLASS_SocialBridge
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_CLASS_SocialBridge
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}

		return self::$classInstance;
	}

	protected $providers;

	public function __construct()
	{
		$this -> initSetting();
	}

	public function initSetting()
	{
		$facebook = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig('facebook');
		if (isset($facebook) && NULL != $facebook)
		{
			$this -> providers['facebook'] = TRUE;
		} else
		{
			$this -> providers['facebook'] = FALSE;
		}
		$twitter = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig('twitter');
		if (isset($twitter) && NULL != $twitter)
		{
			$this -> providers['twitter'] = TRUE;
		} else
		{
			$this -> providers['twitter'] = FALSE;
		}
		$linkedin = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig('linkedin');
		if (isset($linkedin) && NULL != $linkedin)
		{
			$this -> providers['linkedin'] = TRUE;
		} else
		{
			$this -> providers['linkedin'] = FALSE;
		}
	}

	public function hasProvider($sService)
	{
		return (isset($this -> providers[$sService]) && $this -> providers[$sService]) ? $this -> providers[$sService] : FALSE;
	}

	public function getProvider($sService)
	{
		static $oProviders = array();

		$sService = strtolower($sService);

		if (!isset($oProviders[$sService]))
		{
			if (!$this -> hasProvider($sService))
			{
				throw new Exception('system does not support provider ' . $sService);
			}
			$core = new YNSOCIALBRIDGE_CLASS_Core();
			$oProviders[$sService] = $core -> getInstance($sService);
		}
		return $oProviders[$sService];
	}

}

<?php

class YNSOCIALCONNECT_BOL_ExtendsRemoteAuthService
{

	private $remoteAuthDao;

	private static $classInstance;

	protected function __construct()
	{
		$this -> remoteAuthDao = BOL_RemoteAuthDao::getInstance();
	}

	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}

		return self::$classInstance;
	}

	public function findAll($cacheLifeTime = 0, $tags = array())
	{
		return $this -> remoteAuthDao -> findAll($cacheLifeTime, $tags);
	}

	public function deleteById($id)
	{
		return $this -> remoteAuthDao -> deleteById($id);
	}

}

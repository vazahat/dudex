<?php

class YNSOCIALCONNECT_BOL_UserlinkingService
{
	/*
	 * @var YNSOCIALCONNECT_BOL_UserlinkingService
	 */
	private static $classInstance;

	/*
	 @var
	 */
	private $objectDao;

	private function __construct()
	{
		$this -> objectDao = YNSOCIALCONNECT_BOL_UserlinkingDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_UserlinkingService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}
		return self::$classInstance;
	}

	public function save(YNSOCIALCONNECT_BOL_Userlinking $entity)
	{
		return $this -> objectDao -> save($entity);
	}

	public function deleteByUserIdAndServiceId($userId, $serviceId)
	{
		return $this -> objectDao -> deleteByUserIdAndServiceId($userId, $serviceId);
	}

	public function deleteByUserId($userId)
	{
		return $this -> objectDao -> deleteByUserId($userId);
	}

	public function findByUserId($userId)
	{
		return $this -> objectDao -> findByUserId($userId);
	}

	public function deleteById($id)
	{
		return $this -> objectDao -> deleteById($id);
	}

}

<?php
/**
 * YNSOCIALCONNECT_BOL_AgentsService
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.bol
 * @since 1.0
 */

class YNSOCIALCONNECT_BOL_AgentsService
{
	/*
	 * @var YNSOCIALCONNECT_BOL_AgentsService
	 */
	private static $classInstance;

	/*
	@var YNSOCIALCONNECT_BOL_AgentsDao
	*/
	private $agentsDao;

	private function __construct()
	{
		$this -> agentsDao = YNSOCIALCONNECT_BOL_AgentsDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_AgentsService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}
		return self::$classInstance;
	}

	public function getAgentByIdentityAndService($sIdentity, $sService, $iServiceId = null)
	{
		return $this -> agentsDao -> getAgentByIdentityAndService($sIdentity, $sService, $iServiceId);
	}

	public function checkExistingAgent($sIdentity, $sService, $iServiceId = null, $profile = array())
	{
		return $this -> agentsDao -> checkExistingAgent($sIdentity, $sService, $iServiceId, $profile);
	}

	public function getUserByIdentityAndService($sIdentity, $sService, $iServiceId = null, $profile = array())
	{
		return $this -> agentsDao -> getUserByIdentityAndService($sIdentity, $sService, $iServiceId, $profile);
	}

	public function save(YNSOCIALCONNECT_BOL_Agents $entity)
	{
		return $this -> agentsDao -> save($entity);
	}

	public function deleteByUserId($userId)
	{
		return $this -> agentsDao -> deleteByUserId($userId);
	}
	
	public function deleteByUserIdAndServiceId($userId, $serviceId)
	{
		return $this -> agentsDao -> deleteByUserIdAndServiceId($userId, $serviceId);
	}

}

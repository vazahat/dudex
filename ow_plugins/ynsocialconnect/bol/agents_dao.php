<?php
/**
 * YNSOCIALCONNECT_BOL_AgentsDao
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.bol
 * @since 1.0
 */
class YNSOCIALCONNECT_BOL_AgentsDao extends OW_BaseDao
{
	/**
	 * Class constructor
	 *
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * Class instance
	 *
	 * @var YNSOCIALCONNECT_BOL_AgentsDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_AgentsDao
	 */
	public static function getInstance()
	{
		if (self::$classInstance === null)
		{
			self::$classInstance = new self();
		}

		return self::$classInstance;
	}

	/**
	 * @see OW_BaseDao::getDtoClassName()
	 *
	 */
	public function getDtoClassName()
	{
		return 'YNSOCIALCONNECT_BOL_Agents';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialconnect_agents';
	}

	public function checkExistingAgent($sIdentity, $sService, $iServiceId = null, $profile = array())
	{
		if(NULL == $sIdentity || NULL == $sService)
		{
			return NULL;
		}
		
		$authAdapter = new YNSOCIALCONNECT_CLASS_AuthAdapter($sIdentity, $sService);
		if ($authAdapter -> isRegistered())
		{
			//	already registered
			$authResult = OW::getUser() -> authenticate($authAdapter);
			if ($authResult -> isValid())
			{
				$agent = $this -> getAgentByIdentityAndService($sIdentity, $sService, $iServiceId);
				if (NULL == $agent)
				{
					//	add new agent
					if (null == $iServiceId)
					{
						$provider = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvider($sService);
						$iServiceId = $provider -> getId();
					}

					$entity = new YNSOCIALCONNECT_BOL_Agents();
					$entity -> userId = (int)$authResult -> getUserId();
					$entity -> identity = $sIdentity;
					$entity -> serviceId = $iServiceId;
					$entity -> ordering = 0;
					$entity -> status = 'login';
					$entity -> login = '1';
					$entity -> data = base64_encode(serialize($profile));
					$entity -> tokenData = base64_encode(serialize($profile));
					$entity -> token = time();
					$entity -> createdTime = time();
					$entity -> loginTime = time();
					$entity -> logoutTime = time();
					$this -> save($entity);

					//	already registerd in OxWall
					return array(
						'type' => 'oxwall',
						'result' => $authResult
					);

				} else
				{
					//	already registed in YNSocialConnect (OxWall also)
					return array(
						'type' => 'both',
						'result' => $agent
					);
				}
			}
		}

		//	not yet registered in OxWall and YNSocialConnect
		return false;

	}

	/**
	 * Return YNSOCIALCONNECT_BOL_Agents object or NULL
	 */
	public function getAgentByIdentityAndService($sIdentity, $sService, $iServiceId = null)
	{
		if(NULL == $sIdentity || NULL == $sService)
		{
			return NULL;
		}
		
		if (null == $iServiceId)
		{
			$provider = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getProvider($sService);
			$iServiceId = $provider -> getId();
		}
		//      init sql query
		//$sIdentity = trim($sIdentity, '/');
		$sqlQuery = "
		                        SELECT 	`yna`.* 
		
		                        FROM  {$this->getTableName()}  AS `yna`
		                        WHERE 1=1 
		                      ";
		$sqlQuery .= " AND identity = :sIdentity";
		$sqlQuery .= " AND serviceId = :iServiceId";

		return $this -> dbo -> queryForObject($sqlQuery, $this -> getDtoClassName(), array(
			'sIdentity' => $sIdentity,
			'iServiceId' => $iServiceId
		));
	}

	public function getUserByIdentityAndService($sIdentity, $sService, $iServiceId = null, $profile = array())
	{
		$aAgent = $this -> checkExistingAgent($sIdentity, $sService, $iServiceId, $profile);
		// if agent does not exsits
		if ($aAgent === false)
		{
			return false;
		}

		$iUserId = NULL;
		if ($aAgent['type'] == 'oxwall')
		{
			$iUserId = intval($aAgent['result'] -> getUserId());
		} else if ($aAgent['type'] == 'both')
		{
			$iUserId = intval($aAgent['result'] -> getUserId());
		}

		if (NULL != $iUserId)
		{
			$aUser = BOL_UserService::getInstance() -> findUserById($iUserId);
			if (NULL != $aUser)
			{
				return $aUser;
			}
		}
		return false;
	}

	public function deleteByUserId($userId)
	{
		$example = new OW_Example();
		$example -> andFieldEqual('userId', $userId);

		return $this -> deleteByExample($example);
	}
	
	public function deleteByUserIdAndServiceId($userId, $serviceId)
	{
		$example = new OW_Example();
		$example -> andFieldEqual('userId', $userId);
		$example -> andFieldEqual('serviceId', $serviceId);

		return $this -> deleteByExample($example);
	}

}

<?php

class YNSOCIALCONNECT_BOL_UserlinkingDao extends OW_BaseDao
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
	 * @var YNSOCIALCONNECT_BOL_UserlinkingDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_UserlinkingDao
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
		return 'YNSOCIALCONNECT_BOL_Userlinking';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialconnect_user_linking';
	}

	public function deleteByUserIdAndServiceId($userId, $serviceId)
	{
		$example = new OW_Example();
		$example -> andFieldEqual('userId', $userId);
		$example -> andFieldEqual('serviceId', $serviceId);

		return $this -> deleteByExample($example);
	}

	public function deleteByUserId($userId)
	{
		$example = new OW_Example();
		$example -> andFieldEqual('userId', $userId);

		return $this -> deleteByExample($example);
	}

	public function findByUserId($userId)
	{
		//      init sql query
		$servicesDao = YNSOCIALCONNECT_BOL_ServicesDao::getInstance();
		$agentsDao = YNSOCIALCONNECT_BOL_AgentsDao::getInstance();
		$sqlQuery = "
	                        SELECT 	`ynu`.id, `ynu`.userId, `ynu`.identity, `ynu`.serviceId, `yns`.name, `yns`.title, `yna`.tokenData 	
	                        FROM  {$this->getTableName()}  AS `ynu`
	                        INNER JOIN `" . $servicesDao -> getTableName() . "` AS `yns` ON(`yns`.`id` = `ynu`.`serviceId`) 
	                        INNER JOIN `" . $agentsDao -> getTableName() . "` AS `yna` ON(`yna`.`userId` = `ynu`.`userId` AND `yna`.`serviceId` = `ynu`.`serviceId`  AND `yna`.`identity` = `ynu`.`identity` )
	                        WHERE 1=1 
		                      ";
		$sqlQuery .= " AND `ynu`.userId = :userId";
		$sqlQuery .= " ORDER BY `yns`.ordering ASC, `ynu`.id ASC ";

		return $this -> dbo -> queryForList($sqlQuery, array('userId' => $userId));
	}

}

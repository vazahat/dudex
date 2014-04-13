<?php
class YNCONTACTIMPORTER_BOL_StatisticDao extends OW_BaseDao
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
	 * @var YNCONTACTIMPORTER_BOL_StatisticDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_StatisticDao
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
		return 'YNCONTACTIMPORTER_BOL_Statistic';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'yncontactimporter_statistic';
	}
	 /**
	 * find statistic by userId
	 *
	 * @param $friendId
	 * @return YNCONTACTIMPORTER_BOL_Invitation
	 */
	 public function updateStatistic($userId, $total)
	 {
	 	$example = new OW_Example();
		if($userId)
		{
			$example->andFieldEqual('userId', $userId);
		}
		
		$statistic = $this->findObjectByExample($example);
		if(!$statistic)
		{
			$statistic = new YNCONTACTIMPORTER_BOL_Statistic();
			$statistic -> userId = $userId;
		}
		$statistic -> totalSent = $statistic -> totalSent + $total;
		
		$this -> save($statistic);
	 }
	 /**
	 * get top inviters
	 * @param array $params
	 * @return array YNCONTACTIMPORTER_BOL_Statistic
	 */
	public function getTopInviters($params = array())
	{
		$example = new OW_Example();
		$example -> setLimitClause(0, 10);
		$example -> setOrder("`totalSent` DESC");
		return $this->findListByExample($example);
	}
	
	 /**
	 * find statistic by userId
	 *
	 * @param $userId
	 * @return YNCONTACTIMPORTER_BOL_Statistic
	 */
	 public function findByUserId($userId)
	 {
	 	$example = new OW_Example();
		if($friendId)
		{
			$example->andFieldEqual('userId', $userId);
		}
		
		return $this->findObjectByExample($example);
	 }
}

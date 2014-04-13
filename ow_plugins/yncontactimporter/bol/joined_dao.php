<?php
class YNCONTACTIMPORTER_BOL_JoinedDao extends OW_BaseDao
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
	 * @var YNCONTACTIMPORTER_BOL_JoinedDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_JoinedDao
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
		return 'YNCONTACTIMPORTER_BOL_Joined';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'yncontactimporter_joined';
	}
	
	 /**
	 * get total joined by userId
	 *
	 * @param $userId
	 * @return YNCONTACTIMPORTER_BOL_Statistic
	 */
	 public function getTotalJoined($userId)
	 {
	 	$query = "SELECT COUNT(*) FROM " . $this -> getTableName();
		if($userId)
		{
			$query .= " WHERE inviterId = ". $userId;
		}
		return $this->dbo->queryForColumn($query);
	 }
}

<?php
class YNCONTACTIMPORTER_BOL_PendingDao extends OW_BaseDao
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
	 * @var PendingDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_PendingDao
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
		return 'YNCONTACTIMPORTER_BOL_Pending';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'yncontactimporter_pending';
	}
	/**
	 * get All providers
	 * 
	 * @param array
	 * 
	 * @return array BOL_Mail
	 */
	 public function getAllPendingEmailsByUserId($params = array())
	 {
		//clear email have sent
		$delete_query = "DELETE FROM ".$this->getTableName(). " WHERE `emailId` NOT IN (SELECT ".OW_DB_PREFIX."base_mail.id FROM " . OW_DB_PREFIX."base_mail) AND ".$this->getTableName().".userID = ".$params['userId'];
		$this->dbo->query($delete_query);
		
		//get pending email
		$query = "SELECT ".OW_DB_PREFIX."base_mail.*, ".$this->getTableName().".id as pendingId FROM " . OW_DB_PREFIX."base_mail INNER JOIN ".
					$this->getTableName() ." ON ( ". $this->getTableName().".emailId = ".OW_DB_PREFIX."base_mail.id AND ".$this->getTableName().".userID = ".$params['userId']." )";
		if(!empty($params['search']))
		{
			$query .= " WHERE recipientEmail LIKE '%".$params['search']."%' ";
		}
		$query .= " ORDER BY id DESC";
		$arr_params = array();
		if(isset($params['first']) && isset($params['count']))
		{
			$arr_params = array('first' => $params['first'], 'count' => $params['count']);
			$query .= " LIMIT :first, :count";
		}
		return $this->dbo->queryForList($query, $arr_params);
	 }
	 /**
	 * get All providers
	 * 
	 * @param array
	 * 
	 * @return int
	 */
	 public function countPendingEmailsByUserId($params = array())
	 {
		$query = "SELECT COUNT(*) FROM " . OW_DB_PREFIX."base_mail INNER JOIN ".
					$this->getTableName() ." ON ( ". $this->getTableName().".emailId = ".OW_DB_PREFIX."base_mail.id AND ".$this->getTableName().".userID = ".$params['userId']." )";
		if(!empty($params['search']))
		{
			$query .= " WHERE recipientEmail LIKE '%".$params['search']."%' ";
		}
		return $this->dbo->queryForColumn($query);
	 }
}

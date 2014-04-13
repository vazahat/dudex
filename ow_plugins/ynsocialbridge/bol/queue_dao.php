<?php
class YNSOCIALBRIDGE_BOL_QueueDao extends OW_BaseDao
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
	 * @var QueueDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALBRIDGE_BOL_QueueDao
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
		return 'YNSOCIALBRIDGE_BOL_Queue';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialbridge_queue';
	}
}

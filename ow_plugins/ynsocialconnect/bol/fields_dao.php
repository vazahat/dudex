<?php
/**
 * YNSOCIALCONNECT_BOL_FieldsDao
 *
 */
class YNSOCIALCONNECT_BOL_FieldsDao extends OW_BaseDao
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
	 * @var YNSOCIALCONNECT_BOL_FieldsDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_FieldsDao
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
		return 'YNSOCIALCONNECT_BOL_Fields';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialconnect_fields';
	}

	public function findByQuestion($question, $service)
	{
		$example = new OW_Example();
		$example -> andFieldEqual('service', $service);
		$example -> andFieldEqual('question', $question);
		
		return $this -> findObjectByExample($example);
	}
	public function findByService($service)
	{
		$example = new OW_Example();
		$example -> andFieldEqual('service', $service);
		
		return $this -> findListByExample($example);
	}
}

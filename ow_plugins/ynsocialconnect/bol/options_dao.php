<?php
/**
 * YNSOCIALCONNECT_BOL_OptionsDao
 *
 */
class YNSOCIALCONNECT_BOL_OptionsDao extends OW_BaseDao
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
	 * @var YNSOCIALCONNECT_BOL_OptionsDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_OptionsDao
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
		return 'YNSOCIALCONNECT_BOL_Options';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialconnect_options';
	}

	public function getOptionsByService($service)
	{
		$example = new OW_Example();
		$example -> andFieldEqual('service', $service);
		
		return $this -> findListByExample($example);
	}
}

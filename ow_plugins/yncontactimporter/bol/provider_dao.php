<?php
class YNCONTACTIMPORTER_BOL_ProviderDao extends OW_BaseDao
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
	 * @var ProviderDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_ProviderDao
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
		return 'YNCONTACTIMPORTER_BOL_Provider';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'yncontactimporter_provider';
	}
	/**
	 * get All providers
	 * 
	 * @param OW_Example
	 * 
	 * @return array YNCONTACTIMPORTER_BOL_Provider
	 */
	 public function getAllProviders(OW_Example $example)
	 {
	 	return $this->findListByExample($example);
	 }
}

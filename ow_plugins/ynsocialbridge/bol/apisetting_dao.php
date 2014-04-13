<?php
class YNSOCIALBRIDGE_BOL_ApisettingDao extends OW_BaseDao
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
	 * @var YNSOCIALBRIDGE_BOL_ApisettingDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALBRIDGE_BOL_ApisettingDao
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
		return 'YNSOCIALBRIDGE_BOL_Apisetting';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialbridge_apisetting';
	}
	 /**
     * get Config
     *
     * @param string $name
     * @return YNSOCIALBRIDGE_BOL_Apisetting
     */
    public function getConfig( $name )
    {
        if ( !$name )
            return null;
		$example = new OW_Example();
        $example->andFieldEqual('apiName', $name);
        return $this->findObjectByExample($example);
    }
}

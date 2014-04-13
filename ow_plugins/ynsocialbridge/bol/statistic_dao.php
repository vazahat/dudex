<?php
class YNSOCIALBRIDGE_BOL_StatisticDao extends OW_BaseDao
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
	 * @var StatisticDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALBRIDGE_BOL_StatisticDao
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
		return 'YNSOCIALBRIDGE_BOL_Statistic';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialbridge_statistic';
	}
	/**
     * find User Statistic
     *
     * @param array $params
     * @return YNSOCIALBRIDGE_BOL_Statistic
     */
    public function getTotalInviteOfDay($params = array() )
    {
    	if ( !$params )
            return null;
		$example = new OW_Example();
        $example->andFieldEqual('uid', $params['uid']);
		$example->andFieldEqual('date', $params['date']);
		$example->andFieldEqual('service', $params['service']);
        return $this->findObjectByExample($example);
	}
}

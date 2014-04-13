<?php
class YNSOCIALBRIDGE_BOL_TokenDao extends OW_BaseDao
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
	 * @var TokenDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALBRIDGE_BOL_TokenDao
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
		return 'YNSOCIALBRIDGE_BOL_Token';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialbridge_token';
	}
	/**
     * find User Token
     *
     * @param array $params
     * @return YNSOCIALBRIDGE_BOL_Token
     */
    public function findUserToken( $params = array() )
    {
    	if ( !$params )
            return null;
		$example = new OW_Example();
        $example->andFieldEqual('service', $params['service']);
		$example->andFieldEqual('userId', $params['userId']);
        return $this->findObjectByExample($example);
	}
}

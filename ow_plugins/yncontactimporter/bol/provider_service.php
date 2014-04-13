<?php
class YNCONTACTIMPORTER_BOL_ProviderService
{
	/*
	 * @var YNCONTACTIMPORTER_BOL_ProviderService
	 */
	private static $classInstance;

	/*
	@var YNCONTACTIMPORTER_BOL_ProviderDao
	*/
	private $providerDao;

	private function __construct()
	{
		$this -> providerDao = YNCONTACTIMPORTER_BOL_ProviderDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNCONTACTIMPORTER_BOL_ProviderService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}

	/**
	 * @var YNCONTACTIMPORTER_BOL_Provider
	 *
	 */
	public function save($providerDto)
	{
		$providerDao = $this -> providerDao;
		return $providerDao -> save($providerDto);
	}

	/**
	 * find All Provider
	 *
	 * @param array $params
	 * @return array YNCONTACTIMPORTER_BOL_Provider
	 */
	public function getAllProviders($params = array())
	{
		$example = new OW_Example();
		if (isset($params['limit']))
		{
			$example -> setLimitClause(0, $params['limit']);
		}
		if(isset($params['enable']))
		{
			$example->andFieldEqual('enable', $params['enable']);
		}
		$example -> setOrder("`order`");
		return $this -> providerDao -> getAllProviders($example);
	}

	/**
	 * delete Provider
	 *
	 * @param YNCONTACTIMPORTER_BOL_Provider
	 * @return void
	 */
	public function delete(YNCONTACTIMPORTER_BOL_Provider $providerDto)
	{
		$providerDao = $this -> providerDao;
		return $providerDao -> delete($providerDto);
	}

	/**
	 * find provider by id
	 *
	 * @param $providerID
	 * @return YNCONTACTIMPORTER_BOL_Provider
	 */
	public function findProviderById($providerId = 0)
	{
		if(!$providerId)
			return null;
		return $this->providerDao->findById($providerId);
	}

}

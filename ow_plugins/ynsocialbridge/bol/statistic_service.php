<?php
class YNSOCIALBRIDGE_BOL_StatisticService
{
	/*
	 * @var YNSOCIALBRIDGE_BOL_StatisticService
	 */
	private static $classInstance;

	/*
	@var YNSOCIALBRIDGE_BOL_StatisticDao
	*/
	private $statisticDao;

	private function __construct()
	{
		$this -> statisticDao = YNSOCIALBRIDGE_BOL_StatisticDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALBRIDGE_BOL_StatisticService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
			self::$classInstance = new self();
		return self::$classInstance;
	}
	/**
	 * @var YNSOCIALBRIDGE_BOL_Statistic
	 * 
	 */
	public function save(YNSOCIALBRIDGE_BOL_Statistic $statisticDto)
	{
		$statisticDao = $this -> statisticDao;
		return $statisticDao -> save($statisticDto);
	}
	/**
     * find User Statistic
     *
     * @param array $params
     * @return YNSOCIALBRIDGE_BOL_Statistic
     */
    public function getTotalInviteOfDay( $params = array() )
    {
    	return $this->statisticDao->getTotalInviteOfDay($params);
	}
	
	/**
     * delete Statistic
     *
     * @param YNSOCIALBRIDGE_BOL_Statistic
     * @return void
     */
     public function delete(YNSOCIALBRIDGE_BOL_Statistic $statisticDto)
	 {
		$statisticDao = $this -> statisticDao;
		return $statisticDao -> delete($statisticDto);
	 }
}

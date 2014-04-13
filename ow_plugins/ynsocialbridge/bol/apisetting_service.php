<?php
class YNSOCIALBRIDGE_BOL_ApisettingService
{
    /*
     * @var YNSOCIALBRIDGE_BOL_ApisettingService
     */
    private static $classInstance;

    /*
      @var YNSOCIALBRIDGE_BOL_ApisettingDao
     */
    private $apisettingDao;

    private function __construct()
    {
        $this->apisettingDao = YNSOCIALBRIDGE_BOL_ApisettingDao::getInstance();
    }
        /**
     * Returns class instance
     *
     * @return YNSOCIALBRIDGE_BOL_ApisettingService
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
            self::$classInstance = new self();

        return self::$classInstance;
    }
	/**
	 * @var YNSOCIALBRIDGE_BOL_Apisetting
	 * 
	 */
    public function save( $apisettingDto )
    {
        $apisettingDao = $this->apisettingDao;
        return $apisettingDao->save($apisettingDto);
    }
	 /**
     * get Config
     *
     * @param string $name
     * @return YNSOCIALBRIDGE_BOL_Apisetting
     */
    public function getConfig( $name )
    {
    	return $this->apisettingDao->getConfig($name);
	}
}

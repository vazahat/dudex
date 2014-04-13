<?php
class YNMEDIAIMPORTER_BOL_SchedulerDao extends OW_BaseDao 
{
	/**
     * Constructor.
     *
     */
    protected function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Singleton instance.
     *
     * @var YNMEDIAIMPORTER_BOL_SchedulerDao
     */
    private static $classInstance;
 
    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return YNMEDIAIMPORTER_BOL_SchedulerDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
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
        return 'YNMEDIAIMPORTER_BOL_Scheduler';
    }
 
    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ynmediaimporter_schedulers';
    }
    
    
}
<?php
class YNMEDIAIMPORTER_BOL_SchedulerService
{
	private static $classInstance;
	private function __construct()
	{
		
	}
	
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}
		return self::$classInstance;
	}
	
	public function getSchedulerList()
    {
        return YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->findAll();
    }
    
    public function addScheduler($params)
    {
    	$scheduler = new YNMEDIAIMPORTER_BOL_Scheduler();
    	
    	$scheduler->status = "0";
    	if ( isset($params['status']) && $params['status'] != '' )
    		$scheduler->status = $params['status'];
    	
    	$scheduler->last_run = "0";
    	if ( isset($params['last_run']) && $params['last_run'] != '' )
    		$scheduler->last_run = $params['last_run'];
    	
    	if ( isset($params['user_id']) && $params['user_id'] != '' )
    		$scheduler->user_id = $params['user_id'];
    	
    	if ( isset($params['owner_id']) && $params['owner_id'] != '' )
    		$scheduler->owner_id = $params['owner_id'];
    	
    	if ( isset($params['owner_type']) && $params['owner_type'] != '' )
    		$scheduler->owner_type = $params['owner_type'];
    	
    	if ( isset($params['params']) && $params['params'] != '' )
    		$scheduler->params = $params['params'];
    	
    	YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->save($scheduler);
    	return $scheduler;
    }
    
    public function deleteScheduler($id)
    {
    	if ($id > 0)
    	{
    		YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->deleteById($id);
    	}
    }
    
    public function deleteSchedulers($idList)
    {
    	if (count($idList) > 0)
    	{
    		YNMEDIAIMPORTER_BOL_SchedulerDao::getInstance()->deleteByIdList($idList);
    	}
    }
    
}
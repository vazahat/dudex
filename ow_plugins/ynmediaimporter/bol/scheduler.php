<?php
class YNMEDIAIMPORTER_BOL_Scheduler extends OW_Entity 
{
	/**
	 * @var int
	 */
	public $id;
	
	
	/**
	 * @var int
	 */
	public $status;
	
	
	/**
	 * @var int
	 */
	public $last_run;
	
	
	/**
	 * @var int
	 */
	public $user_id;
	

	/**
	 * @var int
	 */
	public $owner_id;
	
	
	/**
	 * @var string
	 */
	public $owner_type;
	
	
	/**
	 * @var string
	 */
	public $params;
}
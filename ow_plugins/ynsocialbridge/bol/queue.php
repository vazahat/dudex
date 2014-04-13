<?php
class YNSOCIALBRIDGE_BOL_Queue extends OW_Entity
{
	/**
	 * @var integer
	 */
	public $tokenId;
	/**
	 * @var integer
	 */
	public $userId;
	/**
	 * @var string
	 */
	public $service;
	/**
	 * @var string
	 */
	public $type;
	/**
	 * @var text
	 */
	public $extraParams;
	/**
	 * @var datetime
	 */
	public $lastRun;
	/**
	 * @var datetime
	 */
	public $nextRun;
	/**
	 * @var tinyint
	 */
	public $priority;
	/**
	 * @var integer
	 */
	public $errorId;
	/**
	 * @var text
	 */
	public $errorMessage;
	/**
	 * @var tinyint
	 */
	public $status;
}
?>
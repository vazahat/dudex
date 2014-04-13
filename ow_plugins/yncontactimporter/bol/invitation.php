<?php
class YNCONTACTIMPORTER_BOL_Invitation extends OW_Entity
{
	/**
	 * @var integer
	 */
	public $userId;
	/**
	 * @var string
	 */
	public $type;
	/**
	 * @var string
	 */
	public $provider;
	/**
	 * @var string
	 */
	public $friendId;
	
	/**
	 * email or name
	 * @var string
	 */
	public $email;
	/**
	 * @var integer
	 */
	public $sentTime;
	/**
	 * @var tiny
	 */
	public $isUsed;
	/**
	 * @var text
	 */
	public $message;
}
?>
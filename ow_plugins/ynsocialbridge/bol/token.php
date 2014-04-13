<?php
class YNSOCIALBRIDGE_BOL_Token extends OW_Entity
{
	/**
	 * @var string
	 */
	public $accessToken;
	/**
	 * @var string
	 */
	public $secretToken;
	/**
	 * @var integer
	 */
	public $userId;
	/**
	 * @var string
	 */
	public $uid;
	/**
	 * @var string
	 */
	public $service;
	/**
	 * @var integer
	 */
	public $timestamp;
}
?>
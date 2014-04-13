<?php
/**
 * YNSOCIALCONNECT_BOL_Agents
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.bol
 * @since 1.0
 */

class YNSOCIALCONNECT_BOL_Agents extends OW_Entity
{
	/**
	 * @var integer
	 */
	public $userId;
	/**
	 * @var string
	 */
	public $identity;
	/**
	 * @var integer
	 */
	public $serviceId;
	/**
	 * @var integer
	 */
	public $ordering;
	/**
	 * @var string
	 */
	public $status;
	/**
	 * @var integer
	 */
	public $login;
	/**
	 * @var string
	 */
	public $data;
	/**
	 * @var string
	 */
	public $tokenData;
	/**
	 * @var string
	 */
	public $token;
	/**
	 * @var integer
	 */
	public $createdTime;
	/**
	 * @var  integer
	 */
	public $loginTime;
	/**
	 * @var integer
	 */
	public $logoutTime;

	public function getUserId()
	{
		return $this -> userId;
	}

	public function setUserId($userId)
	{
		$this -> userId = $userId;
	}

	public function getIdentity()
	{
		return $this -> identity;
	}

	public function setIdentity($identity)
	{
		$this -> identity = $identity;
	}

	public function getServiceId()
	{
		return $this -> serviceId;
	}

	public function setServiceId($serviceId)
	{
		$this -> serviceId = $serviceId;
	}

	public function getOrdering()
	{
		return $this -> ordering;
	}

	public function setOrdering($ordering)
	{
		$this -> ordering = $ordering;
	}

	public function getStatus()
	{
		return $this -> status;
	}

	public function setStatus($status)
	{
		$this -> status = $status;
	}

	public function getLogin()
	{
		return $this -> login;
	}

	public function setLogin($login)
	{
		$this -> login = $login;
	}

	public function getData()
	{
		return $this -> data;
	}

	public function setData($data)
	{
		$this -> data = $data;
	}

	public function getTokenData()
	{
		return $this -> tokenData;
	}

	public function setTokenData($tokenData)
	{
		$this -> tokenData = $tokenData;
	}

	public function getToken()
	{
		return $this -> token;
	}

	public function setToken($token)
	{
		$this -> token = $token;
	}

	public function getCreatedTime()
	{
		return $this -> createdTime;
	}

	public function setCreatedTime($createdTime)
	{
		$this -> createdTime = $createdTime;
	}

	public function getLoginTime()
	{
		return $this -> loginTime;
	}

	public function setLoginTime($loginTime)
	{
		$this -> loginTime = $loginTime;
	}

	public function getLogoutTime()
	{
		return $this -> logoutTime;
	}

	public function setLogoutTime($logoutTime)
	{
		$this -> logoutTime = $logoutTime;
	}

}
?>
<?php

class YNSOCIALCONNECT_BOL_Userlinking extends OW_Entity
{
	public $userId;
	
	public $identity;
	
	public $serviceId;
	
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

}
?>
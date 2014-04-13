<?php
/**
 * YNSOCIALCONNECT_BOL_Services
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.bol
 * @since 1.0
 */

class YNSOCIALCONNECT_BOL_Services extends OW_Entity
{
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var integer
	 */
	public $privacy;
	/**
	 * @var integer
	 */
	public $connect;
	/**
	 * @var string
	 */
	public $protocol;
	/**
	 * @var string
	 */
	public $mode;
	/**
	 * @var integer
	 */
	public $w;
	/**
	 * @var integer
	 */
	public $h;
	/**
	 * @var integer
	 */
	public $ordering;
	/**
	 * @var integer
	 */
	public $isActive;
	/**
	 * @var string
	 */
	public $params;
	/**
	 * @var integer
	 */
	public $totalSignup;
	/**
	 * @var integer
	 */
	public $totalSync;
	/**
	 * @var integer
	 */
	public $totalLogin;
	public function getName()
	{
		return $this -> name;
	}

	public function setName($name)
	{
		$this -> name = $name;
	}

	public function getTitle()
	{
		return $this -> title;
	}

	public function setTitle($title)
	{
		$this -> title = $title;
	}

	public function getPrivacy()
	{
		return $this -> privacy;
	}

	public function setPrivacy($privacy)
	{
		$this -> privacy = $privacy;
	}

	public function getConnect()
	{
		return $this -> connect;
	}

	public function setConnect($connect)
	{
		$this -> connect = $connect;
	}

	public function getProtocol()
	{
		return $this -> protocol;
	}

	public function setProtocol($protocol)
	{
		$this -> protocol = $protocol;
	}

	public function getMode()
	{
		return $this -> mode;
	}

	public function setMode($mode)
	{
		$this -> mode = $mode;
	}

	public function getW()
	{
		return $this -> w;
	}

	public function setW($w)
	{
		$this -> w = $w;
	}

	public function getH()
	{
		return $this -> h;
	}

	public function setH($h)
	{
		$this -> h = $h;
	}

	public function getOrdering()
	{
		return $this -> ordering;
	}

	public function setOrdering($ordering)
	{
		$this -> ordering = $ordering;
	}

	public function getIsActive()
	{
		return $this -> isActive;
	}

	public function setIsActive($isActive)
	{
		$this -> isActive = $isActive;
	}

	public function getParams()
	{
		return $this -> params;
	}

	public function setParams($params)
	{
		$this -> params = $params;
	}

	public function getTotalSignup()
	{
		return $this -> totalSignup;
	}

	public function setTotalSignup($totalSignup)
	{
		$this -> totalSignup = $totalSignup;
	}

	public function getTotalSync()
	{
		return $this -> totalSync;
	}

	public function setTotalSync($totalSync)
	{
		$this -> totalSync = $totalSync;
	}

	public function getTotalLogin()
	{
		return $this -> totalLogin;
	}

	public function setTotalLogin($totalLogin)
	{
		$this -> totalLogin = $totalLogin;
	}

}
?>
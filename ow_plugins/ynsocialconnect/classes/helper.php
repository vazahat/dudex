<?php

class YNSOCIALCONNECT_CLASS_Helper
{
	private static $classInstance;
	private $isViewMore;

	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}

		return self::$classInstance;
	}

	private function __construct()
	{
		// init
		$this -> isViewMore = '0';
	}

	public function getIsViewMore()
	{
		return $this -> isViewMore;
	}

	public function setIsViewMore($isViewMore)
	{
		$this -> isViewMore = $isViewMore;
	}

}

<?php
/**
 * YNSOCIALCONNECT_BOL_ServicesDao
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.bol
 * @since 1.0
 */

class YNSOCIALCONNECT_BOL_ServicesDao extends OW_BaseDao
{
	/**
	 * constants to define list of API PROVIDERS
	 */
	static private $serviceTypes = array(
		'facebook' => 'api',
		'twitter' => 'api',
		'linkedin' => 'api'
	);

	static private $disableSupported = array('hyves' => 1);

	/**
	 * Class constructor
	 *
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * Class instance
	 *
	 * @var YNSOCIALCONNECT_BOL_ServicesDao
	 */
	private static $classInstance;

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_ServicesDao
	 */
	public static function getInstance()
	{
		if (self::$classInstance === null)
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
		return 'YNSOCIALCONNECT_BOL_Services';
	}

	/**
	 * @see OW_BaseDao::getTableName()
	 *
	 */
	public function getTableName()
	{
		return OW_DB_PREFIX . 'ynsocialconnect_services';
	}

	/**
	 * Get enalbe providers
	 */
	public function getEnabledProviders($iLimit = 5, $iLimitSelected = 20, $bDisplay = true)
	{
		if ((int)$iLimit < 0)
		{
			$iLimit = 0;
		}
		if ((int)$iLimitSelected < 0)
		{
			$iLimitSelected = 0;
		}

		//      init sql query
		$sqlQuery = "
                        SELECT 	`yns`.* 

                        FROM  {$this->getTableName()}  AS `yns`
                        WHERE 1=1 
                      ";
		$sqlQuery .= " AND isActive = 1 ";
		$sqlQuery .= " ORDER BY ordering ASC ";
		$sqlQuery .= " LIMIT 0, :iLimitSelected ";

		return $this -> dbo -> queryForObjectList($sqlQuery, $this -> getDtoClassName(), array('iLimitSelected' => $iLimitSelected));
	}

	/**
	 * Get open providers
	 */
	public function getOpenProviders($iLimit = 5, $iLimitSelected = 20, $bDisplay = true)
	{
		if ((int)$iLimit < 0)
		{
			$iLimit = 0;
		}
		if ((int)$iLimitSelected < 0)
		{
			$iLimitSelected = 0;
		}

		$sCond = ($bDisplay == true) ? 'isActive = 1' : '';
		//      init sql query
		$sqlQuery = "
                        SELECT 	`yns`.* 

                        FROM  {$this->getTableName()}  AS `yns`
                        WHERE 1=1 
                      ";
		$sqlQuery .= " AND $sCond ";
		$sqlQuery .= " ORDER BY ordering ASC ";
		$sqlQuery .= " LIMIT 0, :iLimitSelected ";

		return $this -> dbo -> queryForObjectList($sqlQuery, $this -> getDtoClassName(), array('iLimitSelected' => $iLimitSelected));
	}

	public function getProvidersByStatus($bDisplay = true)
	{

		$sCond = ($bDisplay == true) ? 'isActive = 1' : 'isActive = 0';
		//      init sql query
		$sqlQuery = "
                        SELECT 	`yns`.* 

                        FROM  {$this->getTableName()}  AS `yns`
                        WHERE 1=1 
                      ";
		$sqlQuery .= " AND $sCond ";
		$sqlQuery .= " ORDER BY ordering ASC ";

		return $this -> dbo -> queryForObjectList($sqlQuery, $this -> getDtoClassName(), array());
	}

	public function getProvider($sService = "")
	{
		if ($sService == "")
		{
			return false;
		}
		if ($sService == 'flickr2')
		{
			$sService = 'flickr';
		}

		//      init sql query
		$sqlQuery = "
                        SELECT 	`yns`.* 

                        FROM  {$this->getTableName()}  AS `yns`
                        WHERE 1=1 
                      ";
		$sqlQuery .= " AND name = :sService";

		return $this -> dbo -> queryForObject($sqlQuery, $this -> getDtoClassName(), array('sService' => $sService));
	}

	public function updateStatistics($sService, $sType)
	{
		if ($sService == 'flickr2')
		{
			$sService = 'flickr';
		}

		$sType = 'total' . ucfirst(strtolower($sType));

		$sqlQuery = "UPDATE `" . $this -> getTableName() . "` SET $sType = $sType + 1
    				WHERE `name` = :sService";

		$this -> dbo -> query($sqlQuery, array('sService' => $sService));

	}

	public function getAllProviders()
	{
		//      init sql query
		$sqlQuery = "
                        SELECT 	`yns`.* 

                        FROM  {$this->getTableName()}  AS `yns`
                        WHERE 1=1 
                      ";
		$sqlQuery .= " ORDER BY ordering ASC ";

		return $this -> dbo -> queryForObjectList($sqlQuery, $this -> getDtoClassName(), array());
	}

	public function updateOrderByServiceId($serviceId, $order)
	{
		$sqlQuery = "UPDATE `" . $this -> getTableName() . "` SET ordering = :order
    				WHERE `id` = :serviceId";

		$this -> dbo -> query($sqlQuery, array(
			'order' => $order,
			'serviceId' => $serviceId
		));
	}

	public function updateActiveById($serviceId, $active)
	{
		$sqlQuery = "UPDATE `" . $this -> getTableName() . "` SET isActive = :active
    				WHERE `id` = :serviceId";

		$this -> dbo -> query($sqlQuery, array(
			'active' => $active,
			'serviceId' => $serviceId
		));
	}

	public function updateActiveStatusAllServices($active = '1')
	{
		$sqlQuery = "UPDATE `" . $this -> getTableName() . "` SET isActive = :active
    				WHERE 1=1 ";

		$this -> dbo -> query($sqlQuery, array('active' => $active));
	}

}

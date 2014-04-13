<?php
class TOPLINK_BOL_ToplinkDao extends OW_BaseDao{
    private static $classInstance;
    public static function getInstance(){
        if ( self::$classInstance === null ){
            self::$classInstance = new self();
        }
        return self::$classInstance;
    }

    protected function __construct(){
        parent::__construct();
    }

    public function getDtoClassName(){
        return 'TOPLINK_BOL_Toplink';
    }

    public function getTableName(){
        return OW_DB_PREFIX . 'toplink_item';
    }
	
	public function findBasedOnUser( $userLevel ){
		$daoPermTable = TOPLINK_BOL_ToplinkPermissionDao::getInstance()->getTableName();
		$sql = "SELECT " . $this->getTableName() . ".* FROM " . $this->getTableName() . " LEFT JOIN " . $daoPermTable . " ON " . $daoPermTable . ".itemid = " . $this->getTableName() . ".id WHERE " . $daoPermTable . ".availablefor = " . $userLevel . ";";
		return $this->dbo->queryForObjectList( $sql, $this->getDtoClassName(), array(), 0, array() );
	}
}
?>
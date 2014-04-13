<?php
class TOPLINK_BOL_ToplinkPermissionDao extends OW_BaseDao{
	
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
        return 'TOPLINK_BOL_ToplinkPermission';
    }

    public function getTableName(){
        return OW_DB_PREFIX . 'toplink_permission';
    }	
}
?>
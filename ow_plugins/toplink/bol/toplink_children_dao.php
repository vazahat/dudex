<?php
class TOPLINK_BOL_ToplinkChildrenDao extends OW_BaseDao{
	
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
        return 'TOPLINK_BOL_ToplinkChildren';
    }
	
    public function getTableName(){
        return OW_DB_PREFIX . 'toplink_children';
    }
	
	public function findByParentId( $parentID ){
		$exp = new OW_Example();
		$exp->andFieldEqual( 'childof',$parentID );
		return $this->findIdListByExample( $exp );
	}
	
	public function findObjByParentId( $parentID ){
		$exp = new OW_Example();
		$exp->andFieldEqual( 'childof',$parentID );
		return $this->findListByExample( $exp );
	}
	
}
?>
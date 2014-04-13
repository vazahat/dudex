<?php
final class TOPLINK_BOL_Service{
    private function __construct(){}
    private static $classInstance;
	
	public static $visibility = array(
		1 => 'admin',
		2 => 'registered',
		3 => 'guest'
	);

    public static function getInstance(){
        if ( self::$classInstance === null ){
            self::$classInstance = new self();
        }
        return self::$classInstance;
    }
	
	/*
	visibleTo => current user type
	OW::getUser()->isAuthenticated(); //user, ! guest
	OW::getUser()->isAdmin();
	*/
	public function getToplink( $all = false ){
		if( OW::getUser()->isAuthenticated() ){
			if( OW::getUser()->isAdmin() ){
				if( $all ){
					$allToplink = TOPLINK_BOL_ToplinkDao::getInstance()->findAll();	
				}else{
					$allToplink = TOPLINK_BOL_ToplinkDao::getInstance()->findBasedOnUser( 1 );
				}
			}else{
				$allToplink = TOPLINK_BOL_ToplinkDao::getInstance()->findBasedOnUser( 2 );
			}
		}else{
			$allToplink = TOPLINK_BOL_ToplinkDao::getInstance()->findBasedOnUser( 3 );
		}
		return $allToplink;
	}
	
	public function getTopLinkById( $id ){
		return TOPLINK_BOL_ToplinkDao::getInstance()->findById( $id );
	}
	
	public function getTopLinkChildIdByParentId( $id ){
		return TOPLINK_BOL_ToplinkChildrenDao::getInstance()->findByParentId( $id );
	}
	
	public function getTopLinkChildObjectByParentId( $id ){
		return TOPLINK_BOL_ToplinkChildrenDao::getInstance()->findObjByParentId( $id );
	}
	
	public function getTopLinkPermissionById( $id ){
		$example = new OW_EXAMPLE();
		$example->andFieldEqual( 'itemid', $id );
		$listOfItem = TOPLINK_BOL_ToplinkPermissionDao::getInstance()->findListByExample( $example );
		
		return $listOfItem;
	}
	
    public function saveToplink( TOPLINK_BOL_Toplink $toplinkitems, $permission = null ){
		TOPLINK_BOL_ToplinkDao::getInstance()->save( $toplinkitems );
		
		if( !empty( $toplinkitems->id ) ){
			$newId = OW::getDbo()->getInsertId();
			//print_r( $toplinkitems );
		}
		
		$example = new OW_EXAMPLE();
		$example->andFieldEqual( 'itemid', $toplinkitems->id );
		$listOfItem = TOPLINK_BOL_ToplinkPermissionDao::getInstance()->findListByExample( $example );
		
		if( !empty( $listOfItem ) ){
			if( !empty( $permission ) ){
				//get saved permission
				foreach( $listOfItem as $permissionObj ){
					$visibleFor[$permissionObj->id] = $permissionObj->availablefor;
				}
				
				//if perm item is reduced, remove also from table
				$removeInstalled = array_diff( $visibleFor, $permission );
				if( !empty( $removeInstalled ) ){
					foreach( $removeInstalled as $id => $myperm ){
						TOPLINK_BOL_ToplinkPermissionDao::getInstance()->deleteById( $id );
					}
				}
				
				//if perm is added, add also into table
				$installNew = array_diff( $permission, $visibleFor );
				if( !empty( $installNew ) ){
					foreach( $installNew as $id ){
						$toplinkPermission = new TOPLINK_BOL_ToplinkPermission();
						$toplinkPermission->itemid = $toplinkitems->id;
						$toplinkPermission->availablefor = $id;
						TOPLINK_BOL_ToplinkPermissionDao::getInstance()->save( $toplinkPermission );
					}
				}
			}else{
				//if permission is not provided, remove except for admin
				foreach( $listOfItem as $permissionObj ){
					if( $permissionObj->availablefor != 1 ){
						TOPLINK_BOL_ToplinkPermissionDao::getInstance()->deleteById( $permissionObj->availablefor );
					}
				}
			}
		}else{
			if( !empty( $permission ) ){
				//if no perm found in table, add allow to admin
				foreach( $permission as $perm ){
					$toplinkPermission = new TOPLINK_BOL_ToplinkPermission();
					$toplinkPermission->itemid = $toplinkitems->id;
					$toplinkPermission->availablefor = $perm;
					TOPLINK_BOL_ToplinkPermissionDao::getInstance()->save( $toplinkPermission );
				}
			}else{
				//if no perm found in table, add allow to admin
				$toplinkPermission = new TOPLINK_BOL_ToplinkPermission();
				$toplinkPermission->itemid = $toplinkitems->id;
				$toplinkPermission->availablefor = 1;
				TOPLINK_BOL_ToplinkPermissionDao::getInstance()->save( $toplinkPermission );
			}
		}
		return $newId;
    }

    public function removeToplink( $id ){
		$thistoplink = $this->getTopLinkById( $id );
		$iconfile = $thistoplink->icon;
		if( preg_match( '/^\//', $iconfile ) ){
			$avatarService = BOL_AvatarService::getInstance();
			$avatarService->removeAvatarImage( $avatarService->getAvatarsDir() . preg_replace( '/^\//','',$iconfile ) );
		}
        TOPLINK_BOL_ToplinkDao::getInstance()->deleteById( $id );
    }
	
	public function saveTopLinkChild( TOPLINK_BOL_ToplinkChildren $child ){
		TOPLINK_BOL_ToplinkChildrenDao::getInstance()->save( $child );
	}
	
	public function removeToplinkChild( $id ){
		TOPLINK_BOL_ToplinkChildrenDao::getInstance()->deleteById( $id );
	}
}
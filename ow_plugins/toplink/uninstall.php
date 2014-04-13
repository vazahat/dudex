<?php
	$toplinks = TOPLINK_BOL_Service::getInstance()->getToplink( true );
	if( !empty( $toplinks ) ){
		foreach( $toplinks as $toplink ){
			TOPLINK_BOL_Service::getInstance()->removeToplink( $toplink->id );
		}
	}
	
	$authorization = OW::getAuthorization();
	$groupName = 'toplink';
	$authorization->deleteGroup( $groupName );
	$authorization->deleteAction( $groupName, 'show_toplink' );
?>
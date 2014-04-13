<?php
	OW::getRouter()->addRoute(new OW_Route('toplink.admin', 'admin/plugins/toplink', "TOPLINK_CTRL_Admin", 'toplinklist'));
	OW::getRouter()->addRoute(new OW_Route('toplink.admin2', 'admin/plugins/toplink/:id', "TOPLINK_CTRL_Admin", 'toplinklist'));
	OW::getRouter()->addRoute(new OW_Route('toplink.remove', 'admin/plugins/toplink/remove/:id', "TOPLINK_CTRL_Admin", 'removelink'));
	OW::getRouter()->addRoute(new OW_Route('toplink.save', 'admin/plugins/toplink/save', "TOPLINK_CTRL_Admin", 'savelink'));
	
	require_once dirname(__FILE__) . DS .  'classes' . DS . 'event_handler.php';
	
	TOPLINK_CLASS_EventHandler::getInstance()->init();
?>
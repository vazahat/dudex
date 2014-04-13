<?php
OW::getNavigation()->deleteMenuItem('yncontactimporter', 'friends_inviter');
//remove widget
BOL_ComponentAdminService::getInstance()->deleteWidget('YNCONTACTIMPORTER_CMP_Widget'); 
?>

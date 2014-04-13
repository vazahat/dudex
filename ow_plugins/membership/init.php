<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
OW::getRouter()->addRoute(new OW_Route('membership_admin', 'admin/membership', 'MEMBERSHIP_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('membership_admin_subscribe', 'admin/membership/subscribe', 'MEMBERSHIP_CTRL_Admin', 'subscribe'));
OW::getRouter()->addRoute(new OW_Route('membership_admin_browse_users_st', 'admin/membership/users', 'MEMBERSHIP_CTRL_Admin', 'users'));
OW::getRouter()->addRoute(new OW_Route('membership_admin_browse_users', 'admin/membership/users/type/:typeId', 'MEMBERSHIP_CTRL_Admin', 'users'));
OW::getRouter()->addRoute(new OW_Route('membership_admin_edit', 'admin/membership/:id/edit', 'MEMBERSHIP_CTRL_Admin', 'edit'));
OW::getRouter()->addRoute(new OW_Route('membership_admin_add', 'admin/membership/add', 'MEMBERSHIP_CTRL_Admin', 'add'));
OW::getRouter()->addRoute(new OW_Route('membership_subscribe', 'membership/subscribe', 'MEMBERSHIP_CTRL_Subscribe', 'index'));

$plugin = OW::getPluginManager()->getPlugin('membership');

$classesToAutoload = array(
    'RadioGroupItemField' => $plugin->getRootDir() . 'classes' . DS . 'radio_group_item_field.php'
);

OW::getAutoloader()->addClassArray($classesToAutoload);

MEMBERSHIP_CLASS_EventHandler::getInstance()->init();
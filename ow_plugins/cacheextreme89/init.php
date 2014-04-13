<?php

define('CACHEEXTREME_DIR_ROOT', dirname(__FILE__));
// BOL_LanguageService::getInstance()->addPrefix('cacheextreme','Cache Extreme');

OW::getRouter()->addRoute(new OW_Route('cacheextreme.admin', 'admin/plugins/cacheextreme', 'CACHEEXTREME_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('cacheextreme.about', 'admin/plugins/cacheextreme/about', 'CACHEEXTREME_CTRL_Admin', 'about'));

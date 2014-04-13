<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

OW::getRouter()->addRoute(
    new OW_Route('usercredits.admin', 'admin/plugins/user-credits/', 'USERCREDITS_CTRL_Admin', 'index')
);

OW::getRouter()->addRoute(
    new OW_Route('usercredits.admin_packs', 'admin/plugins/user-credits/packs', 'USERCREDITS_CTRL_Admin', 'packs')
);

OW::getRouter()->addRoute(
    new OW_Route('usercredits.buy_credits', 'user-credits/buy-credits', 'USERCREDITS_CTRL_BuyCredits', 'index')
);

USERCREDITS_CLASS_EventHandler::getInstance()->init();
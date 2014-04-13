<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

BOL_BillingService::getInstance()->deactivateProduct('user_credits_pack');

OW::getConfig()->saveConfig('usercredits', 'is_once_initialized', 0);
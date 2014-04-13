<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
BOL_BillingService::getInstance()->deactivateProduct('membership_plan');

OW::getNavigation()->deleteMenuItem('membership', 'subscribe_page_heading');

BOL_ComponentAdminService::getInstance()->deleteWidget('MEMBERSHIP_CMP_MyMembershipWidget');
BOL_ComponentAdminService::getInstance()->deleteWidget('MEMBERSHIP_CMP_UserMembershipWidget');
BOL_ComponentAdminService::getInstance()->deleteWidget('MEMBERSHIP_CMP_PromoWidget');
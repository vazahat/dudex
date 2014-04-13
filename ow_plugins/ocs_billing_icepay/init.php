<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the "License");
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://opensource.org/licenses/CPAL-1.0. Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License.
 * The Initial Developer of the Original Code is Oxwall CandyStore (http://oxcandystore.com/).
 * All portions of the code written by Oxwall CandyStore are Copyright (c) 2013. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2013 Oxwall CandyStore. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Oxwall CandyStore
 * Attribution URL: http://oxcandystore.com/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * /init.php
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_billing_icepay
 * @since 1.5.1
 */

OW::getRouter()->addRoute(new OW_Route('ocsbillingicepay.admin', 'admin/ocs-billing-icepay', 'OCSBILLINGICEPAY_CTRL_Admin', 'index'));
OW::getRouter()->addRoute(new OW_Route('ocsbillingicepay.order_form', 'ocs-billing-icepay/order', 'OCSBILLINGICEPAY_CTRL_Order', 'form'));
OW::getRouter()->addRoute(new OW_Route('ocsbillingicepay.postback', 'ocs-billing-icepay/order/postback', 'OCSBILLINGICEPAY_CTRL_Order', 'postback'));
OW::getRouter()->addRoute(new OW_Route('ocsbillingicepay.completed', 'ocs-billing-icepay/order/completed/', 'OCSBILLINGICEPAY_CTRL_Order', 'completed'));

function ocsbillingicepay_add_admin_notification( BASE_CLASS_EventCollector $coll )
{
    $billingService = BOL_BillingService::getInstance();
    $gwKey = OCSBILLINGICEPAY_CLASS_IcepayAdapter::GATEWAY_KEY;
    if ( !mb_strlen($billingService->getGatewayConfigValue($gwKey, 'merchantId')) || !mb_strlen($billingService->getGatewayConfigValue($gwKey, 'encryptionCode')) )
    {
        $coll->add(
            OW::getLanguage()->text(
                'ocsbillingicepay',
                'plugin_configuration_notice',
                array('url' => OW::getRouter()->urlForRoute('ocsbillingicepay.admin'))
            )
        );
    }
}

OW::getEventManager()->bind('admin.add_admin_notification', 'ocsbillingicepay_add_admin_notification');


function ocsbillingicepay_add_access_exception( BASE_CLASS_EventCollector $e )
{
    $e->add(array('controller' => 'OCSBILLINGICEPAY_CTRL_Order', 'action' => 'postback'));
}

OW::getEventManager()->bind('base.members_only_exceptions', 'ocsbillingicepay_add_access_exception');
OW::getEventManager()->bind('base.password_protected_exceptions', 'ocsbillingicepay_add_access_exception');
OW::getEventManager()->bind('base.splash_screen_exceptions', 'ocsbillingicepay_add_access_exception');

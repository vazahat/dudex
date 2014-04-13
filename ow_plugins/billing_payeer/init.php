<?php

OW::getRouter()->addRoute(new OW_Route('billing_payeer_order_form', 'billing-payeer/order', 'BILLINGPAYEER_CTRL_Order', 'form'));
OW::getRouter()->addRoute(new OW_Route('billing_payeer_notify', 'billing-payeer/order/notify', 'BILLINGPAYEER_CTRL_Order', 'notify'));
OW::getRouter()->addRoute(new OW_Route('billing_payeer_completed', 'billing-payeer/order/completed/', 'BILLINGPAYEER_CTRL_Order', 'completed'));
OW::getRouter()->addRoute(new OW_Route('billing_payeer_canceled', 'billing-payeer/order/canceled/', 'BILLINGPAYEER_CTRL_Order', 'canceled'));
OW::getRouter()->addRoute(new OW_Route('billing_payeer_admin', 'admin/billing-payeer', 'BILLINGPAYEER_CTRL_Admin', 'index'));

function payeer_add_admin_notification( BASE_CLASS_EventCollector $coll )
{
    $billingService = BOL_BillingService::getInstance();

    if ( !mb_strlen($billingService->getGatewayConfigValue(BILLINGPAYEER_CLASS_PayeerAdapter::GATEWAY_KEY, 'm_key')) &&
    !mb_strlen($billingService->getGatewayConfigValue(BILLINGPAYEER_CLASS_PayeerAdapter::GATEWAY_KEY, 'm_shop'))   ){
        $coll->add(
            OW::getLanguage()->text(
                'billingpayeer', 
                'plugin_configuration_notice', 
                array('url' => OW::getRouter()->urlForRoute('billing_payeer_admin'))
            )
        );
    }
}

OW::getEventManager()->bind('admin.add_admin_notification', 'payeer_add_admin_notification');

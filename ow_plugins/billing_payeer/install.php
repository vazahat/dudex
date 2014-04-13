<?php

$billingService = BOL_BillingService::getInstance();

$gateway = new BOL_BillingGateway();
$gateway->gatewayKey = 'billingpayeer';
$gateway->adapterClassName = 'BILLINGPAYEER_CLASS_PayeerAdapter';
$gateway->active = 0;
$gateway->mobile = 0;
$gateway->recurring = 1;
$gateway->currencies = 'USD,RUB';

$billingService->addGateway($gateway);

$billingService->addConfig('billingpayeer', 'm_key', '');
$billingService->addConfig('billingpayeer', 'm_shop', '');
$billingService->addConfig('billingpayeer', 'm_curr', 'rur');
$billingService->addConfig('billingpayeer', 'lang', 'ru');

OW::getPluginManager()->addPluginSettingsRouteName('billingpayeer', 'billing_payeer_admin');

$path = OW::getPluginManager()->getPlugin('billingpayeer')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'billingpayeer');

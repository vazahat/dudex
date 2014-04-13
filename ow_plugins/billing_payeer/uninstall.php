<?php

$billingService = BOL_BillingService::getInstance();


$billingService->deleteConfig('billingpayeer', 'm_key');
$billingService->deleteConfig('billingpayeer', 'm_shop');
$billingService->deleteConfig('billingpayeer', 'm_curr');
$billingService->deleteConfig('billingpayeer', 'lang');

$billingService->deleteGateway('billingpayeer');

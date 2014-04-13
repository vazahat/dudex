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
 * ICEPAY order pages controller.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_billing_icepay.controllers
 * @since 1.5.1
 */
class OCSBILLINGICEPAY_CTRL_Order extends OW_ActionController
{
    public function form ()
    {
        $billingService = BOL_BillingService::getInstance();
        $adapter = new OCSBILLINGICEPAY_CLASS_IcepayAdapter();
        
        $lang = OW::getLanguage();
        
        $sale = $billingService->getSessionSale();
        
        if ( !$sale )
        {
            $url = $billingService->getSessionBackUrl();
            if ( $url != null )
            {
                OW::getFeedback()->warning($lang->text('base', 'billing_order_canceled'));
                $billingService->unsetSessionBackUrl();
                $this->redirect($url);
            }
            else
            {
                $this->redirect($billingService->getOrderFailedPageUrl());
            }
        }
        
        $productAdapter = $billingService->getProductAdapter($sale->entityKey);
        if ( $productAdapter )
        {
            $productUrl = $productAdapter->getProductOrderUrl();
        }
        
        if ( $billingService->prepareSale($adapter, $sale) )
        {
            $country = $adapter->detectCountry();
            $language = $adapter->getLanguageByCountry($country);
            
            require_once OW::getPluginManager()->getPlugin('ocsbillingicepay')->getClassesDir() . 'api' . DS . 'icepay_api_basic.php';
            
            try
            {
                if ( !$country )
                {
                    $country = '00';
                }
                $paymentObj = new Icepay_PaymentObject();
                $paymentObj->setAmount($sale->totalAmount * 100)
                    ->setCountry($country)
                    ->setLanguage($language)
                    ->setReference($sale->hash)
                    ->setDescription(strip_tags($sale->entityDescription))
                    ->setCurrency($sale->currency)
                    ->setOrderID($sale->id);
                    
                $gwKey = OCSBILLINGICEPAY_CLASS_IcepayAdapter::GATEWAY_KEY;
                $merchantId = $billingService->getGatewayConfigValue($gwKey, 'merchantId');
                $encryptionCode = $billingService->getGatewayConfigValue($gwKey, 'encryptionCode');
                
                $basicmode = Icepay_Basicmode::getInstance();
                
                $basicmode->setMerchantID($merchantId)
                    ->setSecretCode($encryptionCode)
                    ->validatePayment($paymentObj);
                    
                $url = $basicmode->getURL();
                
                $billingService->unsetSessionSale();
                
                header("Location: " . $url);
                exit;
            }
            catch ( Exception $e )
            {
                OW::getFeedback()->warning($e->getMessage());
                
                $url = isset($productUrl) ? $productUrl : $billingService->getOrderFailedPageUrl();
                $this->redirect($url);
            }
        }
        else
        {
            OW::getFeedback()->warning($lang->text('base', 'billing_order_init_failed'));
            
            $url = isset($productUrl) ? $productUrl : $billingService->getOrderFailedPageUrl();
            $this->redirect($url);
        }
    }
    
    public function postback ()
    {
        $logger = OW::getLogger('ocsbillingicepay');
        $logger->addEntry(print_r($_REQUEST, true), 'postback.data-array');

        if ( empty($_REQUEST['Reference']) )
        {
            $logger->addEntry("Empty reference", 'postback.reference');
            $logger->writeLog();
            exit();
        }

        require_once OW::getPluginManager()->getPlugin('ocsbillingicepay')->getClassesDir() . 'api' . DS . 'icepay_api_basic.php';
   
        $gwKey = OCSBILLINGICEPAY_CLASS_IcepayAdapter::GATEWAY_KEY;
        $billingService = BOL_BillingService::getInstance();
        
        $merchantId = $billingService->getGatewayConfigValue($gwKey, 'merchantId');
        $encryptionCode = $billingService->getGatewayConfigValue($gwKey, 'encryptionCode');
        
        $icepay = new Icepay_Postback();
        $icepay->setMerchantID($merchantId)
            ->setSecretCode($encryptionCode)
            ->doIPCheck();
        
        try
        {
            if ( $icepay->validate() )
            {
                $hash = trim($_REQUEST['Reference']);
                $transId = trim($_REQUEST['TransactionID']);
                $sale = $billingService->getSaleByHash($hash);
                
                if ( !$sale || !mb_strlen($transId) )
                {
                    $logger->addEntry("Sale not found", 'postback.sale');
                    $logger->writeLog();
                    exit();
                }
                
                $adapter = new OCSBILLINGICEPAY_CLASS_IcepayAdapter();
                
                if ( !$billingService->saleDelivered($transId, $sale->gatewayId) )
                {
                    $sale->transactionUid = $transId;
                    if ( $billingService->verifySale($adapter, $sale) )
                    {
                        $sale = $billingService->getSaleById($sale->id);
                        $productAdapter = $billingService->getProductAdapter($sale->entityKey);
                        if ( $productAdapter )
                        {
                            $billingService->deliverSale($productAdapter, $sale);
                        }
                    }
                }
                
                $logger->addEntry("Validated!", 'validate-status');
            }
            else
            {
                $logger->addEntry("Unable to validate postback data", 'validate-status');
            }
        }
        catch ( Exception $e )
        {
            $logger->addEntry($e->getMessage(), 'validate-exception');
        }
        
        $logger->writeLog();
        exit();
    }
    
    public function completed ()
    {
        if ( $_REQUEST['Status'] == 'ERR' )
        {
            $this->redirect(BOL_BillingService::getInstance()->getOrderFailedPageUrl());
        }
        
        $hash = ! empty($_REQUEST['Reference']) ? trim($_REQUEST['Reference']) : null;
        
        $this->redirect(BOL_BillingService::getInstance()->getOrderCompletedPageUrl($hash));
    }
}
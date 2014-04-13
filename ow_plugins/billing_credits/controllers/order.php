<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class BILLINGCREDITS_CTRL_Order extends OW_ActionController {

    public function form() {
        $adapter = new BILLINGCREDITS_CLASS_CreditsAdapter();
        $billingService = BOL_BillingService::getInstance();
        $lang = OW::getLanguage();

        $sale = $billingService->getSessionSale();

        if (!$sale) {
            $url = $billingService->getSessionBackUrl();
            if ($url != null) {
                OW::getFeedback()->warning($lang->text('base', 'billing_order_canceled'));
                $billingService->unsetSessionBackUrl();
                $this->redirect($url);
            } else {
                $this->redirect($billingService->getOrderFailedPageUrl());
            }
        }

        $formId = uniqid('order_form-');
        $this->assign('formId', $formId);

        $js = '$("#' . $formId . '").submit()';
        OW::getDocument()->addOnloadScript($js);

        $fields = $adapter->getFields();
        $this->assign('fields', $fields);

        if ($billingService->prepareSale($adapter, $sale)) {
            $sale->totalAmount = floatval($sale->totalAmount);
            $this->assign('sale', $sale);

            $masterPageFileDir = OW::getThemeManager()->getMasterPageTemplate('blank');
            OW::getDocument()->getMasterPage()->setTemplate($masterPageFileDir);

            $billingService->unsetSessionSale();
        } else {
            $productAdapter = $billingService->getProductAdapter($sale->entityKey);

            if ($productAdapter) {
                $productUrl = $productAdapter->getProductOrderUrl();
            }

            OW::getFeedback()->warning($lang->text('base', 'billing_order_init_failed'));
            $url = isset($productUrl) ? $productUrl : $billingService->getOrderFailedPageUrl();

            $this->redirect($url);
        }
    }

    public function purchase() {
        $creditValue = (float) $_POST['creditValue'];
        $buyingUser = (int) $_POST['buyingUser'];
        $itemName = $_POST['itemName'];
        $itemPrice = $_POST['itemPrice'];
        $itemCurrency = $_POST['itemCurrency'];
        $saleHash = $_POST['custom'];
        $transId = $_POST['transId'];

        $billingService = BOL_BillingService::getInstance();
        $adapter = new BILLINGCREDITS_CLASS_CreditsAdapter();

        $sale = $billingService->getSaleByHash($saleHash);

        if ($sale && $sale->status != BOL_BillingSaleDao::STATUS_DELIVERED) {
            $sale->transactionUid = $transId;

            if ($billingService->verifySale($adapter, $sale)) {
                $sale = $billingService->getSaleById($sale->id);

                $productAdapter = $billingService->getProductAdapter($sale->entityKey);

                if ($productAdapter) {
                    $billingService->deliverSale($productAdapter, $sale);

                    USERCREDITS_BOL_CreditsService::getInstance()->decreaseBalance($buyingUser, $creditValue);

                    $actionId = 0;

                    foreach (USERCREDITS_BOL_ActionDao::getInstance()->findActionsByPluginKey('billingcredits') as $action) {
                        if ($action->actionKey == 'creditsbuy') {
                            $actionId = $action->id;
                        }
                    }

                    if ($actionId) {
                        $log = new USERCREDITS_BOL_Log();
                        $log->actionId = $actionId;
                        $log->userId = $buyingUser;
                        $log->amount = (int) $creditValue;
                        $log->logTimestamp = time();

                        USERCREDITS_BOL_LogDao::getInstance()->save($log);
                    }
                }
            }
        }

        $this->redirect(BOL_BillingService::getInstance()->getOrderCompletedPageUrl());
    }

    public function buybycredits() {
        $billingService = BOL_BillingService::getInstance();
        if (!isset($_POST['amount'])) {
            $this->redirect($billingService->getOrderFailedPageUrl());
        }

        $userId = OW::getUser()->getId();

        $itemName = $_POST['itemName'];
        $amount = (float) $_POST['amount'];
        $currency = $_POST['currency'];
        $availableCredits = USERCREDITS_BOL_CreditsService::getInstance()->getCreditsBalance($userId);

        $creditValue = $billingService->getGatewayConfigValue('billingcredits', 'creditValue');

        $totalCreditsRequired = round($amount * $creditValue);

        if ($availableCredits < $totalCreditsRequired) {
            $this->assign('formUrl', $billingService->getOrderCancelledPageUrl());
        } else {
            $this->assign('formUrl', OW::getRouter()->urlFor('BILLINGCREDITS_CTRL_Order', 'purchase'));
        }

        $this->assign('itemName', $itemName);
        $this->assign('amount', $amount);
        $this->assign('currency', $currency);
        $this->assign('availableCredits', $availableCredits);
        $this->assign('totalCreditsRequired', $totalCreditsRequired);
        $this->assign('buyingUser', $userId);
        $this->assign('custom', $_POST['custom']);
        $this->assign('transId', $_POST['transId']);
    }

}
<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2013, Purusothaman Ramanujam
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
class BILLINGCREDITS_CLASS_CreditsAdapter implements OW_BillingAdapter {

    const GATEWAY_KEY = 'billingcredits';

    private $billingService;

    public function __construct() {
        $this->billingService = BOL_BillingService::getInstance();
    }

    public function prepareSale(BOL_BillingSale $sale) {
        return $this->billingService->saveSale($sale);
    }

    public function verifySale(BOL_BillingSale $sale) {
        return $this->billingService->saveSale($sale);
    }

    public function getFields($params = null) {
        return array(
            'formActionUrl' => $this->getOrderFormActionUrl()
        );
    }

    public function getOrderFormUrl() {
        return OW::getRouter()->urlForRoute('billing_credits_order_form');
    }

    public function getLogoUrl() {
        $plugin = OW::getPluginManager()->getPlugin('billingcredits');

        return $plugin->getStaticUrl() . 'img/credits_logo.png';
    }

    private function getOrderFormActionUrl() {
        return OW::getRouter()->urlFor('BILLINGCREDITS_CTRL_Order', 'buybycredits');
    }

    public function isVerified($post) {
        return true;
    }

}
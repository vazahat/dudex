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
 * ICEPAY admin controller
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_billing_icepay.controllers
 * @since 1.5.1
 */
class OCSBILLINGICEPAY_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function index ()
    {
        $billingService = BOL_BillingService::getInstance();
        $language = OW::getLanguage();
        
        $form = new IcepayConfigForm();
        $this->addForm($form);
        
        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            $res = $form->process();
            OW::getFeedback()->info($language->text('ocsbillingicepay', 'settings_updated'));
            $this->redirect();
        }
        
        $adapter = new OCSBILLINGICEPAY_CLASS_IcepayAdapter();
        $this->assign('logoUrl', $adapter->getLogoUrl());
        $gateway = $billingService->findGatewayByKey(OCSBILLINGICEPAY_CLASS_IcepayAdapter::GATEWAY_KEY);
        $this->assign('gateway', $gateway);
        $this->assign('activeCurrency', $billingService->getActiveCurrency());
        $supported = $billingService->currencyIsSupported($gateway->currencies);
        $this->assign('currSupported', $supported);
        
        $logo = OW::getPluginManager()->getPlugin('ocsbillingicepay')->getStaticUrl() . 'img/oxwallcandystore-logo.jpg';
        $this->assign('logo', $logo);
        
        $this->setPageHeading(OW::getLanguage()->text('ocsbillingicepay', 'config_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_app');
    }
}

class IcepayConfigForm extends Form
{
    public function __construct ()
    {
        parent::__construct('icepay-config-form');
        
        $language = OW::getLanguage();
        $billingService = BOL_BillingService::getInstance();
        
        $gwKey = OCSBILLINGICEPAY_CLASS_IcepayAdapter::GATEWAY_KEY;
        
        $merch = new TextField('merchantId');
        $merch->setValue($billingService->getGatewayConfigValue($gwKey, 'merchantId'));
        $merch->setRequired(true);
        $this->addElement($merch);
        
        $encCode = new TextField('encryptionCode');
        $encCode->setValue($billingService->getGatewayConfigValue($gwKey, 'encryptionCode'));
        $encCode->setRequired(true);
        $this->addElement($encCode);
        
        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('ocsbillingicepay', 'btn_save'));
        $this->addElement($submit);
    }
    
    public function process ()
    {
        $values = $this->getValues();
        
        $billingService = BOL_BillingService::getInstance();
        $gwKey = OCSBILLINGICEPAY_CLASS_IcepayAdapter::GATEWAY_KEY;
        
        $billingService->setGatewayConfigValue($gwKey, 'merchantId', $values['merchantId']);
        $billingService->setGatewayConfigValue($gwKey, 'encryptionCode', $values['encryptionCode']);
    }
}
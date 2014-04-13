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
class BILLINGCREDITS_CTRL_Admin extends ADMIN_CTRL_Abstract {

    public function index() {
        $language = OW::getLanguage();
        $billingService = BOL_BillingService::getInstance();

        $adminForm = new Form('adminForm');

        $element = new TextField('creditValue');
        $element->setRequired(true);
        $element->setLabel($language->text('billingcredits', 'admin_usd_credit_value'));
        $element->setDescription($language->text('billingcredits', 'admin_usd_credit_value_desc'));
        $element->setValue($billingService->getGatewayConfigValue('billingcredits', 'creditValue'));
        $validator = new FloatValidator(0.1);
        $validator->setErrorMessage($language->text('billingcredits', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $adminForm->addElement($element);

        $element = new Submit('saveSettings');
        $element->setValue($language->text('billingcredits', 'admin_save_settings'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $billingService->setGatewayConfigValue('billingcredits', 'creditValue', $values['creditValue']);

                OW::getFeedback()->info($language->text('billingcredits', 'user_save_success'));
            }
        }

        $this->addForm($adminForm);

        $this->setPageHeading(OW::getLanguage()->text('billingcredits', 'config_page_heading'));
        $this->setPageTitle(OW::getLanguage()->text('billingcredits', 'config_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_app');
    }

}
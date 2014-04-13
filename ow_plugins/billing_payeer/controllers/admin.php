<?php

class BILLINGPAYEER_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function index()
    {
        $billingService = BOL_BillingService::getInstance();
        $language = OW::getLanguage();

        $payeerConfigForm = new PayEerConfigForm();
        $this->addForm($payeerConfigForm);

        if ( OW::getRequest()->isPost() && $payeerConfigForm->isValid($_POST) )
        {
            $res = $payeerConfigForm->process();
            OW::getFeedback()->info($language->text('billingpayeer', 'settings_updated'));
            $this->redirect();
        }

        $adapter = new BILLINGPAYEER_CLASS_PayEerAdapter();
        $this->assign('logoUrl', $adapter->getLogoUrl());

        $gateway = $billingService->findGatewayByKey(BILLINGPAYEER_CLASS_PayeerAdapter::GATEWAY_KEY);
        $this->assign('gateway', $gateway);

        $this->assign('activeCurrency', $billingService->getActiveCurrency());

        $supported = $billingService->currencyIsSupported($gateway->currencies);
        $this->assign('currSupported', $supported);

        $this->setPageHeading(OW::getLanguage()->text('billingpayeer', 'config_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_app');
    }
}

class PayEerConfigForm extends Form
{

    public function __construct()
    {
        parent::__construct('payeer-config-form');

        $language = OW::getLanguage();
        $billingService = BOL_BillingService::getInstance();
        $gwKey = BILLINGPAYEER_CLASS_PayeerAdapter::GATEWAY_KEY;

        $element = new TextField('m_key');
        $element->setValue($billingService->getGatewayConfigValue($gwKey, 'm_key'));
        $this->addElement($element);

        $element = new TextField('m_shop');
        $element->setValue($billingService->getGatewayConfigValue($gwKey, 'm_shop'));
        $this->addElement($element);

        $element = new Selectbox('m_curr');
        $element
            ->setValue($billingService->getGatewayConfigValue($gwKey, 'm_curr'))
            ->setHasInvitation(false)
            ->addOption('RUB', 'RUB')
            ->addOption('usd', 'USD');
        $this->addElement($element);
        
        $element = new Selectbox('lang');
        $element
            ->setValue($billingService->getGatewayConfigValue($gwKey, 'lang'))
            ->setHasInvitation(false)
            ->addOption('ru', 'Русский')
            ->addOption('en', 'English');
        $this->addElement($element);
        
        $element = new Selectbox('tabNum');
        $element
            ->setValue($billingService->getGatewayConfigValue($gwKey, 'tabNum'))
            ->setHasInvitation(false)
            ->addOption('1', 'Electronic Systems')
            ->addOption('2', 'Cash / Bank Transfers')
            ->addOption('3', 'Terminals')
            ->addOption('4', 'SMS payments');
        $this->addElement($element);

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('billingpayeer', 'btn_save'));
        $this->addElement($submit);
    }

    public function process()
    {
        $values = $this->getValues();

        $billingService = BOL_BillingService::getInstance();
        $gwKey = BILLINGPAYEER_CLASS_PayeerAdapter::GATEWAY_KEY;

        $billingService->setGatewayConfigValue($gwKey, 'm_key', $values['m_key']);
        $billingService->setGatewayConfigValue($gwKey, 'm_shop', $values['m_shop']);
        $billingService->setGatewayConfigValue($gwKey, 'm_curr', $values['m_curr']);
        $billingService->setGatewayConfigValue($gwKey, 'lang', $values['lang']);
        $billingService->setGatewayConfigValue($gwKey, 'tabNum', $values['tabNum']);
    }
}

<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * User credits order page controller.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.user_credits.controllers
 * @since 1.0
 */
class USERCREDITS_CTRL_BuyCredits extends OW_ActionController
{

    public function index()
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }
        
        $form = new BuyCreditsForm();
        $this->addForm($form);

        $creditService = USERCREDITS_BOL_CreditsService::getInstance();
        
        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            $values = $form->getValues();
            $lang = OW::getLanguage();
            $userId = OW::getUser()->getId();
            
            $billingService = BOL_BillingService::getInstance();

            if ( empty($values['gateway']['url']) || empty($values['gateway']['key']) 
                    || !$gateway = $billingService->findGatewayByKey($values['gateway']['key'])
                    || !$gateway->active )
            {
                OW::getFeedback()->error($lang->text('base', 'billing_gateway_not_found'));
                $this->redirect();
            }
            
            if ( !$pack = $creditService->findPackById($values['pack']) )
            {
                OW::getFeedback()->error($lang->text('usercredits', 'pack_not_found'));
                $this->redirect();
            }

            // create pack product adapter object
            $productAdapter = new USERCREDITS_CLASS_UserCreditsPackProductAdapter();
            
            // sale object
            $sale = new BOL_BillingSale();
            $sale->pluginKey = 'usercredits';
            $sale->entityDescription = $creditService->getPackTitle($pack->price, $pack->credits);
            $sale->entityKey = $productAdapter->getProductKey();
            $sale->entityId = $pack->id;
            $sale->price = floatval($pack->price);
            $sale->period = 30;
            $sale->userId = $userId ? $userId : 0;
            $sale->recurring = 0;
    
            $saleId = $billingService->initSale($sale, $values['gateway']['key']);
    
            if ( $saleId )
            {
                // sale Id is temporarily stored in session
                $billingService->storeSaleInSession($saleId);
                $billingService->setSessionBackUrl($productAdapter->getProductOrderUrl());
    
                // redirect to gateway form page 
                OW::getApplication()->redirect($values['gateway']['url']);
            }
        }

        $lang = OW::getLanguage();

        $packs = $creditService->getPackList();
        $this->assign('packs', $packs);
        
        $this->setPageHeading($lang->text('usercredits', 'buy_credits_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_user');
        OW::getDocument()->setTitle($lang->text('usercredits', 'meta_title_buy_credits'));
        
        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'base', 'dashboard');
    }
}

/**
 * Buy credits form class
 */
class BuyCreditsForm extends Form
{

    public function __construct()
    {
        parent::__construct('buy-credits-form');

        $packs = USERCREDITS_BOL_CreditsService::getInstance()->getPackList();
        
        $packField = new RadioField('pack');
        $packField->setRequired();
        $value = 0;
        foreach ( $packs as $p )
        {
            $packField->addOption($p['id'], $p['title']);
            if ( $value == 0 )
            {
                $value = $p['id'];
            }
        }
        $packField->setValue($value);
        $this->addElement($packField);

        $gatewaysField = new BillingGatewaySelectionField('gateway');
        $gatewaysField->setRequired(true);
        $this->addElement($gatewaysField);

        $submit = new Submit('buy');
        $submit->setValue(OW::getLanguage()->text('base', 'checkout'));
        $this->addElement($submit);
    }
}
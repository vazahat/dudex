<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliates settings form class.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.classes
 * @since 1.5.3
 */
class OCSAFFILIATES_CLASS_SettingsForm extends Form
{
    public function __construct( $name )
    {
        parent::__construct($name);
        
        $lang = OW::getLanguage();
        
        $period = new TextField('period');
        $period->setRequired(true);
        $period->setLabel($lang->text('ocsaffiliates', 'settings_timeout'));
        $this->addElement($period);

        $status = new Selectbox('status');
        $status->setRequired(true);
        $status->setHasInvitation(false);
        $options = array(
            'active' => $lang->text('ocsaffiliates', 'status_active'),
            'unverified' => $lang->text('ocsaffiliates', 'status_unverified')
        );
        $status->addOptions($options);
        $status->setLabel($lang->text('ocsaffiliates', 'settings_status'));
        $this->addElement($status);
        
        $clickAmount = new TextField('clickAmount');
        $clickAmount->setRequired(true);
        $clickAmount->setLabel($lang->text('ocsaffiliates', 'settings_click_amount'));
        $clickAmount->addValidator(new FloatValidator());
        $this->addElement($clickAmount);
        
        $regAmount = new TextField('regAmount');
        $regAmount->setRequired(true);
        $regAmount->setLabel($lang->text('ocsaffiliates', 'settings_reg_amount'));
        $regAmount->addValidator(new FloatValidator());
        $this->addElement($regAmount);
        
        $saleCommission = new Selectbox('saleCommission');
        $saleCommission->setRequired(true);
        $options = array(
        	'amount' => $lang->text('ocsaffiliates', 'commission_amount'),
            'percent' => $lang->text('ocsaffiliates', 'commission_percent')
        );
        $saleCommission->addOptions($options);
        $saleCommission->setLabel($lang->text('ocsaffiliates', 'settings_sale_commission'));
        $this->addElement($saleCommission);
        
        $saleAmount = new TextField('saleAmount');
        $saleAmount->setLabel($lang->text('ocsaffiliates', 'settings_sale_amount'));
        $saleAmount->addValidator(new FloatValidator());
        $this->addElement($saleAmount);
        
        $salePercent = new TextField('salePercent');
        $salePercent->setLabel($lang->text('ocsaffiliates', 'settings_sale_percent'));
        $salePercent->addValidator(new FloatValidator());
        $this->addElement($salePercent);

        $showRates = new CheckboxField('showRates');
        $showRates->setLabel($lang->text('ocsaffiliates', 'show_rates'));
        $this->addElement($showRates);

        $allowBanners = new CheckboxField('allowBanners');
        $allowBanners->setLabel($lang->text('ocsaffiliates', 'allow_banners'));
        $this->addElement($allowBanners);

        $terms = new CheckboxField('terms');
        $terms->setLabel($lang->text('ocsaffiliates', 'enable_terms'));
        $this->addElement($terms);
        
        $submit = new Submit('save');
        $submit->setLabel($lang->text('ocsaffiliates', 'save'));
        $this->addElement($submit);
    }
}
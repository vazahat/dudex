<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate register payout form
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.classes
 * @since 1.5.3
 */
class OCSAFFILIATES_CLASS_RegisterPayoutForm extends Form
{
    public function __construct( $name, $affiliateId = null )
    {
        parent::__construct($name);
        
        $this->setAction(OW::getRouter()->urlForRoute('ocsaffiliates.action_register_payout'));
        $this->setAjax(true);
        $lang = OW::getLanguage();

        $amount = new TextField('amount');
        $amount->setLabel($lang->text('ocsaffiliates', 'amount_paid'));
        $amount->setRequired(true);
        $this->addElement($amount);

        $affiliate = new HiddenField('affiliateId');
        $affiliate->setRequired(true);
        $this->addElement($affiliate);

        $byCredits = new CheckboxField('byCredits');
        $byCredits->setLabel($lang->text('ocsaffiliates', 'deposit_credits'));
        $this->addElement($byCredits);

        $submit = new Submit('add');
        $submit->setValue($lang->text('ocsaffiliates', 'register_btn'));
        $this->addElement($submit);
        
        $this->bindJsFunction(Form::BIND_SUCCESS, "function(data){
            if ( !data.result ) {
                OW.error(data.error);
            }
            else {
                document.location.reload();
            }
        }");
    }
}
<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate signup form
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.classes
 * @since 1.5.3
 */
class OCSAFFILIATES_CLASS_SignupForm extends Form
{
    public function __construct( $name )
    {
        parent::__construct($name);
        
        $this->setAction(OW::getRouter()->urlForRoute('ocsaffiliates.action_signup'));
        $this->setAjax();
        $lang = OW::getLanguage();
        
        $affName = new TextField('name');
        $affName->setRequired(true);
        $affName->setLabel($lang->text('ocsaffiliates', 'affiliate_name'));
        $this->addElement($affName);
        
        $email = new TextField('email');
        $email->setRequired(true);
        $email->setLabel($lang->text('ocsaffiliates', 'email'));
        $email->addValidator(new EmailValidator());
        $this->addElement($email);
        
        $password = new PasswordField('password');
        $password->setRequired(true);
        $password->setLabel($lang->text('ocsaffiliates', 'password'));
        $this->addElement($password);
        
        $payment = new Textarea('payment');
        $payment->setRequired(true);
        $payment->setLabel($lang->text('ocsaffiliates', 'payment_details'));
        $this->addElement($payment);

        if ( OW::getConfig()->getValue('ocsaffiliates', 'terms_agreement') )
        {
            $terms = new CheckboxField('terms');
            $validator = new RequiredValidator();
            $validator->setErrorMessage($lang->text('ocsaffiliates', 'terms_required_msg'));
            $terms->addValidator($validator);
            $this->addElement($terms);
        }
        
        $submit = new Submit('signup');
        $submit->setValue($lang->text('ocsaffiliates', 'signup_btn'));
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
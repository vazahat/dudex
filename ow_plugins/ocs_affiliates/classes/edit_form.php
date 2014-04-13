<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate edit form
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.classes
 * @since 1.5.3
 */
class OCSAFFILIATES_CLASS_EditForm extends Form
{
    public function __construct( $name, $mode )
    {
        parent::__construct($name);
        
        $this->setAction(OW::getRouter()->urlForRoute('ocsaffiliates.action_edit'));
        $this->setAjax();
        $lang = OW::getLanguage();


        $idField = new HiddenField('affiliateId');
        $this->addElement($idField);

        $modeField = new HiddenField('mode');
        $modeField->setValue($mode);
        $this->addElement($modeField);

        if ( $mode == 'admin' )
        {
            $emailVerified = new CheckboxField('emailVerified');
            $emailVerified->setLabel($lang->text('ocsaffiliates', 'email_verified'));
            $this->addElement($emailVerified);

            $status = new Selectbox('status');
            $status->setLabel($lang->text('ocsaffiliates', 'status'));
            $status->setHasInvitation(false);
            $status->setRequired(true);
            $options = array(
                'active' => $lang->text('ocsaffiliates', 'status_active'),
                'unverified' => $lang->text('ocsaffiliates', 'status_unverified')
            );
            $status->setOptions($options);
            $this->addElement($status);
        }
        
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
        $password->setLabel($lang->text('ocsaffiliates', 'password'));
        $this->addElement($password);
        
        $payment = new Textarea('payment');
        $payment->setRequired(true);
        $payment->setLabel($lang->text('ocsaffiliates', 'payment_details'));
        $this->addElement($payment);
        
        $submit = new Submit('save');
        $submit->setValue($lang->text('ocsaffiliates', 'edit'));
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
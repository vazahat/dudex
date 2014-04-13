<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate email verification form
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.classes
 * @since 1.5.3
 */
class OCSAFFILIATES_CLASS_VerificationForm extends Form
{
    public function __construct( $name )
    {
        parent::__construct($name);
        
        $this->setAction(OW::getRouter()->urlForRoute('ocsaffiliates.action_resend'));
        $this->setAjax();
        $lang = OW::getLanguage();
                
        $submit = new Submit('resend');
        $submit->setValue($lang->text('ocsaffiliates', 'resend_btn'));
        $this->addElement($submit);
        
        $this->bindJsFunction(Form::BIND_SUCCESS, "function(data){
            if ( !data.result ) {
                OW.error(data.error);
            }
            else {
                OW.info(data.message);
            }
        }");
    }
}
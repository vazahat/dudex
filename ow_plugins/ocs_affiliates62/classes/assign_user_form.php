<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate assign user form
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.classes
 * @since 1.5.3
 */
class OCSAFFILIATES_CLASS_AssignUserForm extends Form
{
    public function __construct( $name, $affiliateId = null )
    {
        parent::__construct($name);
        
        $this->setAction(OW::getRouter()->urlForRoute('ocsaffiliates.action_assign_user'));
        $this->setAjax();
        $this->setAjaxResetOnSuccess(false);
        $lang = OW::getLanguage();

        $user = new TextField('user');
        $user->setLabel($lang->text('ocsaffiliates', 'assign_to_username'));
        $user->setInvitation($lang->text('ocsaffiliates', 'username'));
        $user->setHasInvitation(true);
        $user->setRequired(true);
        $this->addElement($user);

        $affiliate = new HiddenField('affiliateId');
        $affiliate->setRequired(true);
        $this->addElement($affiliate);

        $submit = new Submit('assign');
        $submit->setValue($lang->text('ocsaffiliates', 'assign_btn'));
        $this->addElement($submit);
        
        $this->bindJsFunction(Form::BIND_SUCCESS, "function(data){
            if ( data.result == 'false' ) {
                OW.error(data.error);
            }
            else {
                document.location.reload();
            }
        }");
    }
}
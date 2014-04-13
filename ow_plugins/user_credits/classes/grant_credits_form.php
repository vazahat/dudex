<?php

/**
 * Copyright (c) 2013, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow_plugins.usercredits.classes
 * @since 1.5.2
 */
class USERCREDITS_CLASS_GrantCreditsForm extends Form
{
    public function __construct()
    {
        parent::__construct('grant-credits-form');

        $this->setAjax(true);
        $this->setAjaxResetOnSuccess(false);
        $this->setAction(OW::getRouter()->urlFor('USERCREDITS_CTRL_Ajax', 'grantCredits'));

        $lang = OW::getLanguage();

        $userIdField = new HiddenField('userId');
        $userIdField->setRequired(true);
        $this->addElement($userIdField);

        $amount = new TextField('amount');
        $amount->setRequired(true);
        $this->addElement($amount);

        $submit = new Submit('grant');
        $submit->setValue($lang->text('usercredits', 'grant'));
        $this->addElement($submit);

        $js = 'owForms["'.$this->getName().'"].bind("success", function(data){
            if ( data.error != undefined ) {
                OW.error(data.error);
                return;
            }

            if ( data.message != undefined ) {
                OW.info(data.message);
                document.grantCreditsFloatBox.close();
                if ( data.credits == "0" ) {
                    window.setTimeout(function(){
                        document.location.reload();
                    }, 600);

                    return;
                }
            }
        });';

        OW::getDocument()->addOnloadScript($js);
    }
}
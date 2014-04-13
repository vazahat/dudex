<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/** 
 * 
 *
 * @author Kairat Bakytow <kainisoft@gmail.com>
 * @package ow_plugins.profileprogressbar.classes
 * @since 1.0
 */
class PROFILEPROGRESSBAR_CLASS_HintForm extends Form
{
    public function __construct()
    {
        parent::__construct('hint-form');
        
        $this->setAjax(TRUE);
        $this->setAction(OW::getRouter()->urlForRoute('profileprogressbar.admin_hint'));
        $this->setAjaxResetOnSuccess(FALSE);
        
        $this->bindJsFunction('success', 'function(data)
        {
            $("#profile-progressbar").tipTip({content: data.content});

            OW.info("Settings successfully saved");
        }');
        
        $checkBox = new CheckboxField('show-hint');
        
        if ( (bool)OW::getConfig()->getValue('profileprogressbar', 'show_hint') )
        {
            $checkBox->addAttribute('checked', 'checked');
        }
        
        $checkBox->setLabel(OW::getLanguage()->text('profileprogressbar', 'show_hint_label'));
        $checkBox->setDescription(OW::getLanguage()->text('profileprogressbar', 'show_hint_desc'));
        $this->addElement($checkBox);
        
        $hintText = new WysiwygTextarea('hint-text');
        $hintText->setRequired();
        $hintText->setSize(WysiwygTextarea::SIZE_L);
        $hintText->setValue(OW::getLanguage()->text('profileprogressbar', 'hint_text'));
        $hintText->setLabel(OW::getLanguage()->text('profileprogressbar', 'hint_label'));
        $hintText->setDescription(OW::getLanguage()->text('profileprogressbar', 'hint_desc'));
        $this->addElement($hintText);
        
        $submit = new Submit('save');
        $submit->setValue('Save');
        $this->addElement($submit);
    }
}

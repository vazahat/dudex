<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class CREDITS_CTRL_Admin extends ADMIN_CTRL_Abstract {

    public function __construct() {
        parent::__construct();

        if (OW::getRequest()->isAjax()) {
            return;
        }

        $this->setPageHeading(OW::getLanguage()->text('credits', 'admin_settings_title'));
        $this->setPageTitle(OW::getLanguage()->text('credits', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function index() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');

        $element = new TextField('logsPerPage');
        $element->setRequired(true);
        $element->setValue($config->getValue('credits', 'logsPerPage'));
        $element->setLabel($language->text('credits', 'logs_per_page'));
        $element->addValidator(new IntValidator(1));
        $adminForm->addElement($element);

        $element = new CheckboxField('enableEmail');
        $element->setLabel(OW::getLanguage()->text('credits', 'admin_enable_email'));
        $element->setDescription(OW::getLanguage()->text('credits', 'admin_enable_email_desc'));
        $element->setValue($config->getValue('credits', 'enableEmail'));
        $adminForm->addElement($element);

        $element = new CheckboxField('enablePM');
        $element->setLabel(OW::getLanguage()->text('credits', 'admin_enable_pm'));
        $element->setDescription(OW::getLanguage()->text('credits', 'admin_enable_pm_desc'));
        $element->setValue($config->getValue('credits', 'enablePM'));
        $adminForm->addElement($element);
        
        $element = new CheckboxField('enableNotification');
        $element->setLabel(OW::getLanguage()->text('credits', 'admin_enable_notification'));
        $element->setDescription(OW::getLanguage()->text('credits', 'admin_enable_notification_desc'));
        $element->setValue($config->getValue('credits', 'enableNotification'));
        $adminForm->addElement($element); 
        
        $element = new Submit('saveSettings');
        $element->setValue(OW::getLanguage()->text('credits', 'admin_save_settings'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $config->saveConfig('credits', 'logsPerPage', $values['logsPerPage']);
                $config->saveConfig('credits', 'enableEmail', $values['enableEmail']);
                $config->saveConfig('credits', 'enablePM', $values['enablePM']);
                $config->saveConfig('credits', 'enableNotification', $values['enableNotification']);                                                
                OW::getFeedback()->info($language->text('credits', 'save_sucess_msg'));
            }
        }

        $this->addForm($adminForm);
    }

}
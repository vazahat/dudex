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
class SPONSORS_CTRL_Admin extends ADMIN_CTRL_Abstract {

    public function __construct() {
        parent::__construct();

        if (OW::getRequest()->isAjax()) {
            return;
        }

        $language = OW::getLanguage();
        $menu = new BASE_CMP_ContentMenu();

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('admin-index');
        $menuItem->setLabel($language->text('sponsors', 'admin_tab_general_title'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('sponsors_admin'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(1);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('admin-list');
        $menuItem->setLabel($language->text('sponsors', 'admin_sponsors_list'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('sponsors_admin_list'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(2);
        $menu->addElement($menuItem);

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('admin-add');
        $menuItem->setLabel($language->text('sponsors', 'admin_add_sponsor'));
        $menuItem->setUrl(OW::getRouter()->urlForRoute('sponsors_admin_add'));
        $menuItem->setIconClass('ow_ic_gear_wheel');
        $menuItem->setOrder(3);
        $menu->addElement($menuItem);

        $this->addComponent('menu', $menu);
        $this->menu = $menu;
    }

    public function index() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');

        $element = new TextField('minimumPayment');
        $element->setRequired(true);
        $element->setValue($config->getValue('sponsors', 'minimumPayment'));
        $validator = new FloatValidator(0);
        $validator->setErrorMessage($language->text('sponsors', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'minimum_payment_required'));
        $element->setDescription($language->text('sponsors', 'minimum_payment_required_desc'));
        $adminForm->addElement($element);

        $element = new TextField('topSponsorsCount');
        $element->setRequired(true);
        $element->setValue($config->getValue('sponsors', 'topSponsorsCount'));
        $validator = new IntValidator(1);
        $validator->setErrorMessage($language->text('sponsors', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsors_count_displayed'));
        $element->setDescription($language->text('sponsors', 'sponsors_count_displayed_desc'));
        $adminForm->addElement($element);

        $element = new TextField('sponsorValidity');
        $element->setRequired(true);
        $element->setValue($config->getValue('sponsors', 'sponsorValidity'));
        $validator = new IntValidator(1);
        $validator->setErrorMessage($language->text('sponsors', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsorship_validatity'));
        $element->setDescription($language->text('sponsors', 'sponsorship_validatity_desc'));
        $adminForm->addElement($element);

        $element = new CheckboxField('alwaysSingleFlip');
        $element->setLabel($language->text('sponsors', 'always_single_flip'));
        $element->setDescription($language->text('sponsors', 'always_single_flip_desc'));
        $element->setValue($config->getValue('sponsors', 'alwaysSingleFlip'));
        $adminForm->addElement($element);

        $element = new CheckboxField('autoApprove');
        $element->setLabel($language->text('sponsors', 'auto_approve'));
        $element->setDescription($language->text('sponsors', 'auto_approve_desc'));
        $element->setValue($config->getValue('sponsors', 'autoApprove'));
        $adminForm->addElement($element);

        $element = new CheckboxField('newSponsorLinkAtLast');
        $element->setLabel($language->text('sponsors', 'show_new_sponsor_link_last'));
        $element->setDescription($language->text('sponsors', 'show_new_sponsor_link_last_desc'));
        $element->setValue($config->getValue('sponsors', 'newSponsorLinkAtLast'));
        $adminForm->addElement($element);

        $element = new CheckboxField('onlyAdminCanAdd');
        $element->setLabel($language->text('sponsors', 'only_admin_add_sponsors'));
        $element->setDescription($language->text('sponsors', 'only_admin_add_sponsors_desc'));
        $element->setValue($config->getValue('sponsors', 'onlyAdminCanAdd'));
        $adminForm->addElement($element);

        $element = new TextField('cutoffDay');
        $element->setRequired(true);
        $element->setValue($config->getValue('sponsors', 'cutoffDay'));
        $validator = new IntValidator(1, 20);
        $validator->setErrorMessage($language->text('sponsors', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'cutoff_date_notify'));
        $element->setDescription($language->text('sponsors', 'cutoff_date_notify_desc'));
        $adminForm->addElement($element);

        $element = new Submit('saveSettings');
        $element->setValue(OW::getLanguage()->text('sponsors', 'admin_save_settings'));
        $adminForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $values = $adminForm->getValues();
                $config->saveConfig('sponsors', 'minimumPayment', $values['minimumPayment']);
                $config->saveConfig('sponsors', 'alwaysSingleFlip', $values['alwaysSingleFlip']);
                $config->saveConfig('sponsors', 'topSponsorsCount', $values['topSponsorsCount']);
                $config->saveConfig('sponsors', 'sponsorValidity', $values['sponsorValidity']);
                $config->saveConfig('sponsors', 'autoApprove', $values['autoApprove']);
                $config->saveConfig('sponsors', 'newSponsorLinkAtLast', $values['newSponsorLinkAtLast']);
                $config->saveConfig('sponsors', 'onlyAdminCanAdd', $values['onlyAdminCanAdd']);
                $config->saveConfig('sponsors', 'cutoffDay', $values['cutoffDay']);

                OW::getFeedback()->info($language->text('sponsors', 'user_save_success'));
            }
        }

        $this->addForm($adminForm);

        $this->setPageHeading(OW::getLanguage()->text('sponsors', 'admin_settings_title'));
        $this->setPageTitle(OW::getLanguage()->text('sponsors', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function lists() {
        $sponsors = SPONSORS_BOL_Service::getInstance()->getSponsors(0, 1, 0);

        $this->assign('sponsors', $sponsors);

        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sponsors')->getStaticCssUrl() . 'admin-style.css');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sponsors')->getStaticJsUrl() . 'jquery.tablesorter.min.js');

        $this->setPageHeading(OW::getLanguage()->text('sponsors', 'site_sponsors_heading'));
        $this->setPageTitle(OW::getLanguage()->text('sponsors', 'site_sponsors_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function sponsor() {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $sponsorForm = new Form('sponsorForm');
        $sponsorForm->setEnctype('multipart/form-data');

        $element = new TextField('sponsorName');
        $element->setRequired(true);
        $element->setLabel($language->text('sponsors', 'sponsor_name'));
        $element->setInvitation($language->text('sponsors', 'sponsor_name_desc'));
        $element->setHasInvitation(true);
        $sponsorForm->addElement($element);

        $element = new TextField('sponsorEmail');
        $element->setRequired(true);
        $validator = new EmailValidator();
        $validator->setErrorMessage($language->text('sponsors', 'invalid_email_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsor_email'));
        $element->setInvitation($language->text('sponsors', 'sponsor_email_desc'));
        $element->setHasInvitation(true);
        $sponsorForm->addElement($element);

        $element = new TextField('sponsorWebsite');
        $element->setRequired(true);
        $validator = new UrlValidator();
        $validator->setErrorMessage($language->text('sponsors', 'invalid_url_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsor_website'));
        $element->setInvitation($language->text('sponsors', 'sponsor_website_desc'));
        $element->setHasInvitation(true);
        $sponsorForm->addElement($element);

        $element = new TextField('sponsorAmount');
        $element->setRequired(true);
        $element->setValue($config->getValue('sponsors', 'minimumPayment'));
        $minAmount = $config->getValue('sponsors', 'minimumPayment');
        $validator = new FloatValidator(0);
        $validator->setErrorMessage($language->text('sponsors', 'invalid_amount_value'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsor_payment_amount'));
        $element->setInvitation($language->text('sponsors', 'admin_payment_amount_desc'));
        $element->setHasInvitation(true);
        $sponsorForm->addElement($element);

        $element = new FileField('sponsorImage');
        $element->setLabel($language->text('sponsors', 'sponsorsh_image_file'));
        $sponsorForm->addElement($element);

        $element = new Submit('addSponsor');
        $element->setValue(OW::getLanguage()->text('sponsors', 'add_sponsor_btn'));
        $sponsorForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($sponsorForm->isValid($_POST)) {
                $values = $sponsorForm->getValues();

                $allowedImageExtensions = array('jpg', 'jpeg', 'gif', 'png', 'tiff');

                $sponsorImageFile = "defaultSponsor.jpg";

                if (isset($_FILES['sponsorImage']) && in_array(UTIL_File::getExtension($_FILES['sponsorImage']['name']), $allowedImageExtensions)) {
                    $backupPath = OW::getPluginManager()->getPlugin('sponsors')->getUserFilesDir() . $_FILES['sponsorImage']['name'];
                    move_uploaded_file($_FILES['sponsorImage']['tmp_name'], $backupPath);

                    $sponsorImageFile = $_FILES['sponsorImage']['name'];
                }

                $sponsor = new SPONSORS_BOL_Sponsor();
                $sponsor->name = $values['sponsorName'];
                $sponsor->email = $values['sponsorEmail'];
                $sponsor->website = $values['sponsorWebsite'];
                $sponsor->price = $values['sponsorAmount'];
                $sponsor->image = $sponsorImageFile;
                $sponsor->userId = OW::getUser()->getId() ? OW::getUser()->getId() : 0;
                $sponsor->status = $config->getValue('sponsors', 'autoApprove') == '1' ? 1 : 0;
                $sponsor->validity = $config->getValue('sponsors', 'sponsorValidity');
                $sponsor->timestamp = time();

                if (SPONSORS_BOL_Service::getInstance()->addSponsor($sponsor)) {
                    if ($sponsor->status == 1)
                        OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_live_notification'));
                    else
                        OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_live_notification_after_approval'));
                }
                else {
                    OW::getFeedback()->error(OW::getLanguage()->text('sponsors', 'sponsor_add_error'));
                }
            }
        }

        $this->addForm($sponsorForm);

        $fields = array();
        foreach ($sponsorForm->getElements() as $element) {
            if (!($element instanceof HiddenField)) {
                $fields[$element->getName()] = $element->getName();
            }
        }

        $this->assign('formData', $fields);


        $this->setPageHeading(OW::getLanguage()->text('sponsors', 'add_sponsor_heading'));
        $this->setPageTitle(OW::getLanguage()->text('sponsors', 'add_sponsor_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function edit($params) {
        if (!isset($params['id']) || !($id = (int) $params['id'])) {
            throw new Redirect404Exception();
            return;
        }

        $language = OW::getLanguage();
        $config = OW::getConfig();

        $sponsor = SPONSORS_BOL_Service::getInstance()->findSponsorById($id);

        if (!$sponsor->id) {
            throw new Redirect404Exception();
            return;
        }

        $sponsorForm = new Form('sponsorForm');
        $sponsorForm->setEnctype('multipart/form-data');

        $element = new TextField('sponsorName');
        $element->setRequired(true);
        $element->setLabel($language->text('sponsors', 'sponsor_name'));
        $element->setInvitation($language->text('sponsors', 'sponsor_name_desc'));
        $element->setValue($sponsor->name);
        $element->setHasInvitation(true);
        $sponsorForm->addElement($element);

        $element = new TextField('sponsorEmail');
        $element->setRequired(true);
        $validator = new EmailValidator();
        $validator->setErrorMessage($language->text('sponsors', 'invalid_email_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsor_email'));
        $element->setInvitation($language->text('sponsors', 'sponsor_email_desc'));
        $element->setValue($sponsor->email);
        $element->setHasInvitation(true);
        $sponsorForm->addElement($element);

        $element = new TextField('sponsorWebsite');
        $element->setRequired(true);
        $validator = new UrlValidator();
        $validator->setErrorMessage($language->text('sponsors', 'invalid_url_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsor_website'));
        $element->setInvitation($language->text('sponsors', 'sponsor_website_desc'));
        $element->setHasInvitation(true);
        $element->setValue($sponsor->website);
        $sponsorForm->addElement($element);

        $element = new TextField('sponsorAmount');
        $element->setRequired(true);
        $minAmount = $config->getValue('sponsors', 'minimumPayment');
        $validator = new FloatValidator(0);
        $validator->setErrorMessage($language->text('sponsors', 'invalid_amount_value'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsor_payment_amount'));
        $element->setInvitation($language->text('sponsors', 'admin_payment_amount_desc'));
        $element->setHasInvitation(true);
        $element->setValue($sponsor->price);
        $sponsorForm->addElement($element);

        $element = new TextField('sponsorValidity');
        $element->setRequired(true);
        $element->setValue($sponsor->validity);
        $validator = new IntValidator(0);
        $validator->setErrorMessage($language->text('sponsors', 'invalid_numeric_format'));
        $element->addValidator($validator);
        $element->setLabel($language->text('sponsors', 'sponsorship_validatity'));
        $element->setInvitation($language->text('sponsors', 'sponsorship_validatity_desc'));
        $element->setHasInvitation(true);
        $sponsorForm->addElement($element);

        $element = new FileField('sponsorImage');
        $element->setLabel($language->text('sponsors', 'sponsorsh_image_file'));
        $sponsorForm->addElement($element);

        $element = new Submit('editSponsor');
        $element->setValue(OW::getLanguage()->text('sponsors', 'edit_sponsor_btn'));
        $sponsorForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($sponsorForm->isValid($_POST)) {
                $values = $sponsorForm->getValues();

                $allowedImageExtensions = array('jpg', 'jpeg', 'gif', 'png', 'tiff');

                $sponsorImageFile = "";

                if (isset($_FILES['sponsorImage']) && in_array(UTIL_File::getExtension($_FILES['sponsorImage']['name']), $allowedImageExtensions)) {
                    $backupPath = OW::getPluginManager()->getPlugin('sponsors')->getUserFilesDir() . $_FILES['sponsorImage']['name'];
                    move_uploaded_file($_FILES['sponsorImage']['tmp_name'], $backupPath);

                    $sponsorImageFile = $_FILES['sponsorImage']['name'];
                }

                $sponsor->name = $values['sponsorName'];
                $sponsor->email = $values['sponsorEmail'];
                $sponsor->website = $values['sponsorWebsite'];
                $sponsor->price = $values['sponsorAmount'];

                if (!empty($sponsorImageFile)) {
                    $sponsor->image = $sponsorImageFile;
                }

                $sponsor->userId = $sponsor->userId;
                $sponsor->status = $sponsor->status;
                $sponsor->validity = $values['sponsorValidity'];

                if (SPONSORS_BOL_Service::getInstance()->addSponsor($sponsor)) {

                    OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_edit_ok'));
                } else {
                    OW::getFeedback()->error(OW::getLanguage()->text('sponsors', 'sponsor_edit_error'));
                }
            }
        }

        $this->addForm($sponsorForm);

        $fields = array();
        foreach ($sponsorForm->getElements() as $element) {
            if (!($element instanceof HiddenField)) {
                $fields[$element->getName()] = $element->getName();
            }
        }

        $this->assign('formData', $fields);
        $this->assign('currentLogoImage', OW::getPluginManager()->getPlugin('sponsors')->getUserFilesUrl() . $sponsor->image);

        $this->setPageHeading(OW::getLanguage()->text('sponsors', 'edit_sponsor_heading'));
        $this->setPageTitle(OW::getLanguage()->text('sponsors', 'edit_sponsor_heading'));
        $this->setPageHeadingIconClass('ow_ic_edit');
    }

    public function delete($params) {
        if (isset($params['id'])) {
            SPONSORS_BOL_Service::getInstance()->delete($params['id']);
            OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'delete_sponsor_ok'));
        }

        $this->redirect(OW::getRouter()->urlForRoute('sponsors_admin_list'));
    }

    public function disapprove($params) {
        if (isset($params['id'])) {
            SPONSORS_BOL_Service::getInstance()->disapprove($params['id']);
            OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_status_changed'));
        }

        $this->redirect(OW::getRouter()->urlForRoute('sponsors_admin_list'));
    }

    public function approve($params) {
        if (isset($params['id'])) {
            SPONSORS_BOL_Service::getInstance()->approve($params['id']);
            OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_status_changed'));
        }

        $this->redirect(OW::getRouter()->urlForRoute('sponsors_admin_list'));
    }

}

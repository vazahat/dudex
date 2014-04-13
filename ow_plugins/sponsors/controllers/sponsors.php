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
class SPONSORS_CTRL_Sponsors extends OW_ActionController {

    private $allowedImageExtensions;

    public function __construct() {
        parent::__construct();

        $this->allowedImageExtensions = array('jpg', 'jpeg', 'gif', 'png', 'tiff');
    }

    public function index() {
        $count = OW::getConfig()->getValue('sponsors', 'topSponsorsCount');

        $sponsors = SPONSORS_BOL_Service::getInstance()->getSponsors($count, 0, 1);

        $this->assign('sponsors', $sponsors);
        $this->assign('backgroundUrl', OW::getPluginManager()->getPlugin('sponsors')->getStaticUrl() . 'background.jpg');
        $this->assign('newSponsorImage', OW::getPluginManager()->getPlugin('sponsors')->getStaticUrl() . 'sponsor.png');
        $this->assign('newSponsorLinkAtLast', OW::getConfig()->getValue('sponsors', 'newSponsorLinkAtLast'));
        $this->assign('newSponsorLink', OW::getRouter()->uriForRoute('sponsors_sponsor'));
        $this->assign('alwaysSingleFlip', OW::getConfig()->getValue('sponsors', 'alwaysSingleFlip'));
        $this->assign('onlyAdminCanAdd', OW::getConfig()->getValue('sponsors', 'onlyAdminCanAdd'));

        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sponsors')->getStaticCssUrl() . 'style.css');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery-ui-1.8.9.custom.min.js');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sponsors')->getStaticJsUrl() . 'jquery.flip.min.js');

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

        if ($config->getValue('sponsors', 'minimumPayment') > 0) {
            $element = new TextField('sponsorAmount');
            $element->setRequired(true);
            $element->setValue($config->getValue('sponsors', 'minimumPayment'));
            $minAmount = $config->getValue('sponsors', 'minimumPayment');
            $validator = new FloatValidator($minAmount);
            $validator->setErrorMessage($language->text('sponsors', 'invalid_sponsor_amount', array('minAmount' => $minAmount)));
            $element->addValidator($validator);
            $element->setLabel($language->text('sponsors', 'sponsor_payment_amount'));
            $element->setInvitation($language->text('sponsors', 'sponsor_payment_amount_desc', array('minAmount' => $minAmount)));
            $element->setHasInvitation(true);
            $sponsorForm->addElement($element);
        }

        $element = new FileField('sponsorImage');
        $element->setLabel($language->text('sponsors', 'sponsorsh_image_file'));
        $sponsorForm->addElement($element);

        if ($config->getValue('sponsors', 'minimumPayment') > 0) {
            $element = new BillingGatewaySelectionField('gateway');
            $element->setRequired(true);
            $element->setLabel($language->text('sponsors', 'payment_gatway_selection'));
            $sponsorForm->addElement($element);
        }

        $element = new Submit('becomeSponsor');
        $element->setValue(OW::getLanguage()->text('sponsors', 'become_sponsor_btn'));
        $sponsorForm->addElement($element);

        if (OW::getRequest()->isPost()) {
            if ($sponsorForm->isValid($_POST)) {
                $values = $sponsorForm->getValues();

                if (isset($_FILES['sponsorImage']) && in_array(UTIL_File::getExtension($_FILES['sponsorImage']['name']), $this->allowedImageExtensions)) {
                    $backupPath = OW::getPluginManager()->getPlugin('sponsors')->getUserFilesDir() . $_FILES['sponsorImage']['name'];
                    move_uploaded_file($_FILES['sponsorImage']['tmp_name'], $backupPath);

                    $sponsorImageFile = $_FILES['sponsorImage']['name'];
                } else {
                    $sponsorImageFile = "defaultSponsor.jpg";
                }

                if (isset($values['sponsorAmount']) && $values['gateway']) {
                    $billingService = BOL_BillingService::getInstance();

                    if (empty($values['gateway']['url']) || empty($values['gateway']['key'])
                            || !$gateway = $billingService->findGatewayByKey($values['gateway']['key'])
                            || !$gateway->active) {
                        OW::getFeedback()->error($language->text('base', 'billing_gateway_not_found'));
                        $this->redirect();
                    }

                    $productAdapter = new SPONSORS_CLASS_SponsorProductAdapter();

                    $sale = new BOL_BillingSale();
                    $sale->pluginKey = 'sponsors';
                    $sale->entityDescription = $language->text('sponsors', 'sponsor_payment_gateway_text');
                    $sale->entityKey = $productAdapter->getProductKey();
                    $sale->entityId = time();
                    $sale->price = floatval($values['sponsorAmount']);
                    $sale->period = null;
                    $sale->userId = OW::getUser()->getId() ? OW::getUser()->getId() : 0;
                    $sale->recurring = 0;

                    $extraData = array();
                    $extraData['sponsorName'] = $values['sponsorName'];
                    $extraData['sponsorEmail'] = $values['sponsorEmail'];
                    $extraData['sponsorWebsite'] = $values['sponsorWebsite'];
                    $extraData['sponsorAmount'] = $values['sponsorAmount'];
                    $extraData['sponsorImage'] = $sponsorImageFile;
                    $extraData['status'] = $config->getValue('sponsors', 'autoApprove') == '1' ? 1 : 0;
                    $extraData['validity'] = $config->getValue('sponsors', 'sponsorValidity');

                    $sale->setExtraData($extraData);

                    $saleId = $billingService->initSale($sale, $values['gateway']['key']);

                    if ($saleId) {
                        $billingService->storeSaleInSession($saleId);
                        $billingService->setSessionBackUrl($productAdapter->getProductOrderUrl());

                        OW::getApplication()->redirect($values['gateway']['url']);
                    }
                } else {
                    $sponsor = new SPONSORS_BOL_Sponsor();
                    $sponsor->name = $values['sponsorName'];
                    $sponsor->email = $values['sponsorEmail'];
                    $sponsor->website = $values['sponsorWebsite'];
                    $sponsor->price = 0;
                    $sponsor->image = $sponsorImageFile;
                    $sponsor->userId = OW::getUser()->getId() ? OW::getUser()->getId() : 0;
                    $sponsor->status = $config->getValue('sponsors', 'autoApprove') == '1' ? 1 : 0;
                    $sponsor->validity = $config->getValue('sponsors', 'sponsorValidity');
                    $sponsor->timestamp = time();

                    if (SPONSORS_BOL_Service::getInstance()->addSponsor($sponsor)) {
                        if ($sponsor->status == 1) {
                            OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_live_notification'));
                        } else {
                            OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_live_notification_after_approval'));
                        }
                    } else {
                        OW::getFeedback()->error(OW::getLanguage()->text('sponsors', 'sponsor_add_error'));
                    }
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


        $this->setPageHeading(OW::getLanguage()->text('sponsors', 'become_sponsor_heading'));
        $this->setPageTitle(OW::getLanguage()->text('sponsors', 'become_sponsor_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

}
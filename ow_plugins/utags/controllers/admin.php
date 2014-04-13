<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.controllers
 */
class UTAGS_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $form = new UTAGS_SettingForm();
        $this->addForm($form);

        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            $form->process($_POST);
            OW::getFeedback()->info(OW::getLanguage()->text('utags', 'settings_saved_message'));
            $this->redirect();
        }

        OW::getDocument()->setHeading(OW::getLanguage()->text('utags', 'heading_configuration'));
        OW::getDocument()->setHeadingIconClass('ow_ic_gear_wheel');

        $this->assign('copyPhoto', OW::getConfig()->getValue('utags', 'copy_photo'));
        $this->assign("pluginUrl", "http://www.oxwall.org/store/item/682");
    }
}

class UTAGS_SettingForm extends Form
{
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('configForm');

        $language = OW::getLanguage();

        $values = OW::getConfig()->getValues('utags');

        $field = new CheckboxField('copy_photo');
        $field->setId('copy_photo_check');
        $field->setValue($values['copy_photo']);
        $this->addElement($field);

        $field = new TextField('photo_album_name');
        $field->setValue(OW::getLanguage()->text('utags', 'default_photo_album_name'));
        $field->setRequired();
        $this->addElement($field);
        
        $field = new CheckboxField('crop_photo');
        $field->setValue($values['crop_photo']);
        $this->addElement($field);

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('utags', 'config_save_label'));
        $this->addElement($submit);
    }

    /**
     * Updates user settings configuration
     *
     * @return boolean
     */
    public function process($post)
    {
        $values = $this->getValues();
        $config = OW::getConfig();

        $config->saveConfig('utags', 'copy_photo', empty($values['copy_photo']) ? 0 : 1);
        $config->saveConfig('utags', 'crop_photo', empty($values['crop_photo']) ? 0 : 1);

        if ( !empty($values['copy_photo']) )
        {
            $languageService = BOL_LanguageService::getInstance();
            $langKey = $languageService->findKey('utags', 'default_photo_album_name');
            if ( !empty($langKey) )
            {
                $langValue = $languageService->findValue($languageService->getCurrent()->getId(), $langKey->getId());

                if ( $langValue === null )
                {
                    $langValue = new BOL_LanguageValue();
                    $langValue->setKeyId($langKey->getId());
                    $langValue->setLanguageId($languageService->getCurrent()->getId());
                }

                $languageService->saveValue(
                    $langValue->setValue($values['photo_album_name'])
                );
            }
        }

        return true;
    }
}
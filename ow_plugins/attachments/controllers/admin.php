<?php

class ATTACHMENTS_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    private $activePlugins = array();

    public function __construct()
    {
        parent::__construct();

        $this->activePlugins = array(
            'photo' => OW::getPluginManager()->isPluginActive('photo'),
            'video' => OW::getPluginManager()->isPluginActive('video'),
            'links' => OW::getPluginManager()->isPluginActive('links')
        );
    }

    public function index()
    {
        $form = new ATTACHMENTS_SettingForm($this->activePlugins);
        $this->addForm($form);

        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            $form->process($_POST, $this->activePlugins);
            OW::getFeedback()->info(OW::getLanguage()->text('attachments', 'settings_saved_message'));
            $this->redirect();
        }

        OW::getDocument()->setHeading(OW::getLanguage()->text('attachments', 'heading_configuration'));
        OW::getDocument()->setHeadingIconClass('ow_ic_gear_wheel');

        $this->assign('photoShare', OW::getConfig()->getValue('attachments', 'photo_share'));

        $this->assign('plugins', $this->activePlugins);
    }
}

class ATTACHMENTS_SettingForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct($plugins)
    {
        parent::__construct('configForm');

        $language = OW::getLanguage();

        $values = OW::getConfig()->getValues('attachments');

        if ( $plugins['video'] )
        {
            $field = new CheckboxField('video_share');
            $field->setValue($values['video_share']);
            $this->addElement($field);
        }

        if ( $plugins['links'] )
        {
            $field = new CheckboxField('link_share');
            $field->setValue($values['link_share']);
            $this->addElement($field);
        }

        if ( $plugins['photo'] )
        {
            $field = new CheckboxField('photo_share');
            $field->setId('photo_share_check');
            $field->setValue($values['photo_share']);
            $this->addElement($field);

            $field = new TextField('photo_album_name');
            $field->setValue(OW::getLanguage()->text('attachments', 'default_photo_album_name'));
            $field->setRequired();

            $this->addElement($field);
        }

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('attachments', 'config_save_label'));
        $this->addElement($submit);
    }

    /**
     * Updates user settings configuration
     *
     * @return boolean
     */
    public function process($post, $plugins)
    {
        $values = $this->getValues();
        $config = OW::getConfig();

        if ( $plugins['photo'] )
        {
            $config->saveConfig('attachments', 'photo_share', empty($values['photo_share']) ? 0 : 1);

            if ( !empty($values['photo_share']) )
            {
                $languageService = BOL_LanguageService::getInstance();
                $langKey = $languageService->findKey('attachments', 'default_photo_album_name');
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
        }

        if ( $plugins['video'] )
        {
            $config->saveConfig('attachments', 'video_share', empty($values['video_share']) ? 0 : 1);
        }

        if ( $plugins['links'] )
        {
            $config->saveConfig('attachments', 'link_share', empty($values['link_share']) ? 0 : 1);
        }

        return true;
    }
}
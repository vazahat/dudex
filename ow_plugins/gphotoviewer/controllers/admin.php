<?php

class GPHOTOVIEWER_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('gphotoviewer', 'admin_menu_general'));
        $item->setUrl(OW::getRouter()->urlForRoute('gphotoviewer.admin_config'));
        $item->setKey('general');
        $item->setIconClass('ow_ic_gear_wheel');
        $item->setOrder(0);

        $menu = new BASE_CMP_ContentMenu(array($item));
        $this->addComponent('menu', $menu);

        $configs = OW::getConfig()->getValues('gphotoviewer');
		
        $configSaveForm = new ConfigSaveForm();

        $this->addForm($configSaveForm);

        if ( OW::getRequest()->isPost() && $configSaveForm->isValid($_POST) )
        {
            $res = $configSaveForm->process();
            OW::getFeedback()->info($language->text('gphotoviewer', 'settings_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('gphotoviewer.admin_config'));
        }

        if ( !OW::getRequest()->isAjax() )
        {
            $this->setPageHeading(OW::getLanguage()->text('gphotoviewer', 'admin_config'));
            $this->setPageHeadingIconClass('ow_ic_picture');

            $elem = $menu->getElement('general');
            if ( $elem )
            {
                $elem->setActive(true);
            }
        }

		
        $configSaveForm->getElement('enablePhotoviewer')->setValue($configs['enable_photo_viewer']);
		$configSaveForm->getElement('downloadable')->setValue($configs['can_users_to_download_photos']);
        $configSaveForm->getElement('slideshowTime')->setValue($configs['slideshow_time_per_a_photo']);
	
    }

}

/**
 * Save photo configuration form class
 */
class ConfigSaveForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('configSaveForm');

        $language = OW::getLanguage();

        
        $slideshowTime = new TextField('slideshowTime');
        $frValidator = new IntValidator();
        $frValidator->setMinValue(1);
        $slideshowTime->addValidator($frValidator);
        $slideshowTime->setLabel($language->text('gphotoviewer', 'slideshow_time_per_a_photo'));
        $this->addElement($slideshowTime);

        $enablePhotoviewer = new CheckboxField('enablePhotoviewer');
        $enablePhotoviewer->setLabel($language->text('gphotoviewer', 'enable_photo_viewer'));
        $this->addElement($enablePhotoviewer);
		

		$downloadable = new CheckboxField('downloadable');
        $downloadable->setLabel($language->text('gphotoviewer', 'can_users_to_download_photos'));
        $this->addElement($downloadable);	
        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('photo', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates photo plugin configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();

        $config = OW::getConfig();

        $config->saveConfig('gphotoviewer', 'slideshow_time_per_a_photo', $values['slideshowTime']);
        $config->saveConfig('gphotoviewer', 'enable_photo_viewer', $values['enablePhotoviewer']);
        $config->saveConfig('gphotoviewer', 'can_users_to_download_photos', $values['downloadable']);
		
        return array('result' => true);
    }
}
<?php

class GVIDEOVIEWER_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    /**
     * Default action
     */
    public function index()
    {
        $language = OW::getLanguage();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('gvideoviewer', 'admin_menu_general'));
        $item->setUrl(OW::getRouter()->urlForRoute('gvideoviewer.admin_config'));
        $item->setKey('general');
        $item->setIconClass('ow_ic_gear_wheel');
        $item->setOrder(0);

        $menu = new BASE_CMP_ContentMenu(array($item));
        $this->addComponent('menu', $menu);

        $configs = OW::getConfig()->getValues('gvideoviewer');
		
        $configSaveForm = new ConfigSaveForm();

        $this->addForm($configSaveForm);

        if ( OW::getRequest()->isPost() && $configSaveForm->isValid($_POST) )
        {
            $res = $configSaveForm->process();
            OW::getFeedback()->info($language->text('gvideoviewer', 'settings_updated'));
            $this->redirect(OW::getRouter()->urlForRoute('gvideoviewer.admin_config'));
        }

        if ( !OW::getRequest()->isAjax() )
        {
            $this->setPageHeading(OW::getLanguage()->text('gvideoviewer', 'admin_config'));
            $this->setPageHeadingIconClass('ow_ic_picture');

            $elem = $menu->getElement('general');
            if ( $elem )
            {
                $elem->setActive(true);
            }
        }

		
        $configSaveForm->getElement('enableVideoviewer')->setValue($configs['enable_video_viewer']);
		//$configSaveForm->getElement('downloadable')->setValue($configs['can_users_to_download_videos']);
        //$configSaveForm->getElement('slideshowTime')->setValue($configs['slideshow_time_per_a_video']);
	
    }

}

/**
 * Save video configuration form class
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
        $slideshowTime->setLabel($language->text('gvideoviewer', 'slideshow_time_per_a_video'));
        $this->addElement($slideshowTime);

        $enableVideoviewer = new CheckboxField('enableVideoviewer');
        $enableVideoviewer->setLabel($language->text('gvideoviewer', 'enable_video_viewer'));
        $this->addElement($enableVideoviewer);
		
		
		$downloadable = new CheckboxField('downloadable');
        $downloadable->setLabel($language->text('gvideoviewer', 'can_users_to_download_videos'));
        $this->addElement($downloadable);	
        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('video', 'btn_edit'));
        $this->addElement($submit);
    }

    /**
     * Updates video plugin configuration
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();

        $config = OW::getConfig();

        //$config->saveConfig('gvideoviewer', 'slideshow_time_per_a_video', $values['slideshowTime']);
        $config->saveConfig('gvideoviewer', 'enable_video_viewer', $values['enableVideoviewer']);
        //$config->saveConfig('gvideoviewer', 'can_users_to_download_videos', $values['downloadable']);
		
        return array('result' => true);
    }
}
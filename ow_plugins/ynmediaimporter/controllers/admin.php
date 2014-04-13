<?php
class YNMEDIAIMPORTER_CTRL_Admin extends ADMIN_CTRL_Abstract
{
	private function getMenu()
    {
        $menuItems = array();
		
        $item = new BASE_MenuItem();
        $item->setLabel( OW::getLanguage()->text('ynmediaimporter', 'general_settings') );
        $item->setUrl( OW::getRouter()->urlForRoute('ynmediaimporter.admin_general') );
        $item->setKey( 'general' );
        $item->setIconClass( 'ow_ic_gear_wheel' );
        $item->setOrder( 0 );
        array_push( $menuItems, $item );

        $item = new BASE_MenuItem();
        $item->setLabel( OW::getLanguage()->text('ynmediaimporter', 'provider_settings') );
        $item->setUrl( OW::getRouter()->urlForRoute('ynmediaimporter.admin_providers') );
        $item->setKey( 'providers' );
        $item->setIconClass( 'ow_ic_gear_wheel' );
        $item->setOrder( 1 );
        array_push( $menuItems, $item );

        return new BASE_CMP_ContentMenu( $menuItems );
    }
	
	
	public function general()
	{
		$this->addComponent( 'menu', $this->getMenu() );
        $this->getComponent( 'menu' )->getElement( 'general' )->setActive( true );
        
		$language = OW::getLanguage();
        $configs =  OW::getConfig()->getValues('ynmediaimporter');
        
        $configSaveGeneralForm = new ConfigSaveGeneralForm();
        $this->addForm($configSaveGeneralForm);
            
        if ( OW::getRequest()->isPost() && $configSaveGeneralForm->isValid($_POST) )
        {
            $res = $configSaveGeneralForm->process();
            OW::getFeedback()->info($language->text('ynmediaimporter', 'settings_updated'));
            $this->redirect();
        }
        
	    if ( !OW::getRequest()->isAjax() )
        {
            $this->setPageHeading(OW::getLanguage()->text('ynmediaimporter', 'admin_config'));
            $this->setPageHeadingIconClass('ow_ic_gear_wheel');
        }
        
        $configSaveGeneralForm->getElement('page')->setValue($configs['page']);
        
        $configSaveGeneralForm->getElement('albumThumbWidth')->setValue($configs['album_thumb_width']);
        $configSaveGeneralForm->getElement('albumThumbHeight')->setValue($configs['album_thumb_height']);
        $configSaveGeneralForm->getElement('albumWrapHeight')->setValue($configs['album_wrap_height']);
        $configSaveGeneralForm->getElement('albumWrapMargin')->setValue($configs['album_wrap_margin']);
        
        $configSaveGeneralForm->getElement('photoThumbWidth')->setValue($configs['photo_thumb_width']);
        $configSaveGeneralForm->getElement('photoThumbHeight')->setValue($configs['photo_thumb_height']);
        $configSaveGeneralForm->getElement('photoWrapHeight')->setValue($configs['photo_wrap_height']);
        $configSaveGeneralForm->getElement('photoWrapMargin')->setValue($configs['photo_wrap_margin']);
        
        $configSaveGeneralForm->getElement('numberPhoto')->setValue($configs['number_photo']);
        $configSaveGeneralForm->getElement('numberQueue')->setValue($configs['number_queue']);
	}
	
	public function providers()
	{
		$this->addComponent( 'menu', $this->getMenu() );
        $this->getComponent( 'menu' )->getElement( 'providers' )->setActive( true );
        
		$language = OW::getLanguage();
        $configs =  OW::getConfig()->getValues('ynmediaimporter');
        
        $configSaveProviderForm = new ConfigSaveProviderForm();
        $this->addForm($configSaveProviderForm);
            
        if ( OW::getRequest()->isPost() && $configSaveProviderForm->isValid($_POST) )
        {
            $res = $configSaveProviderForm->process();
            OW::getFeedback()->info($language->text('ynmediaimporter', 'settings_updated'));
            $this->redirect();
        }
        
	    if ( !OW::getRequest()->isAjax() )
        {
            $this->setPageHeading(OW::getLanguage()->text('ynmediaimporter', 'admin_config'));
            $this->setPageHeadingIconClass('ow_ic_gear_wheel');
        }
        
        $configSaveProviderForm->getElement('enableFacebook')->setValue($configs['enable_facebook']);
        $configSaveProviderForm->getElement('enablePicasa')->setValue($configs['enable_picasa']);
        $configSaveProviderForm->getElement('enableFlickr')->setValue($configs['enable_flickr']);
        $configSaveProviderForm->getElement('enableInstagram')->setValue($configs['enable_instagram']);
        //$configSaveProviderForm->getElement('enableYfrog')->setValue($configs['enable_yfrog']);
	}
	
}


class ConfigSaveProviderForm extends Form
{
    public function __construct()
    {
        parent::__construct('configSaveProviderForm');

        $language = OW::getLanguage();
        
        $enableFacebookField = new CheckboxField('enableFacebook');
        $this->addElement($enableFacebookField);
        
        $enablePicasaField = new CheckboxField('enablePicasa');
        $this->addElement($enablePicasaField);
        
        $enableFlickrField = new CheckboxField('enableFlickr');
        $this->addElement($enableFlickrField);
        
        $enableInstagramField = new CheckboxField('enableInstagram');
        $this->addElement($enableInstagramField);
        
        /*
        $enableYfrogField = new CheckboxField('enableYfrog');
        $this->addElement($enableYfrogField);
        */
        
        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('base', 'edit_button'));
        $this->addElement($submit);
    }
    
    /**
     * Updates Media Importer plugin configuration
     *
     * @return boolean
     */
    public function process( )
    {
        $values = $this->getValues();

        $config = OW::getConfig();

        $config->saveConfig('ynmediaimporter', 'enable_facebook', $values['enableFacebook']);
		$config->saveConfig('ynmediaimporter', 'enable_picasa', $values['enablePicasa']);
		$config->saveConfig('ynmediaimporter', 'enable_flickr', $values['enableFlickr']);
		$config->saveConfig('ynmediaimporter', 'enable_instagram', $values['enableInstagram']);
		//$config->saveConfig('ynmediaimporter', 'enable_yfrog', $values['enableYfrog']);
		        
        return array('result' => true);
    }
}

class ConfigSaveGeneralForm extends Form
{
    public function __construct()
    {
        parent::__construct('configSaveGeneralForm');

        $language = OW::getLanguage();
        
	//1. Number Photos/Albums Per Page - How many photos/albums will be shown per page? (Enter a number between 10 and 40). Default 20
        $itemOnPageField = new TextField('page');
        $itemOnPageField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(10);
        $sValidator->setMaxValue(40);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'page_error'));
        $itemOnPageField->addValidator($sValidator);
        $itemOnPageField->setLabel($language->text('ynmediaimporter', 'page'));
        $this->addElement($itemOnPageField);
        
   	//2. Album Max Thumbnail Width - Enter a number between 100 and 200. Default: 165
		$albumThumbWidthField = new TextField('albumThumbWidth');
        $albumThumbWidthField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(100);
        $sValidator->setMaxValue(200);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'album_thumb_width_error'));
        $albumThumbWidthField->addValidator($sValidator);
        $albumThumbWidthField->setLabel($language->text('ynmediaimporter', 'album_thumb_width'));
        $this->addElement($albumThumbWidthField);
        
    //3. Album Max Thumbnail Height - Enter a number between 100 and 200. Default: 116
        $albumThumbHeightField = new TextField('albumThumbHeight');
        $albumThumbHeightField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(100);
        $sValidator->setMaxValue(200);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'album_thumb_height_error'));
        $albumThumbHeightField->addValidator($sValidator);
        $albumThumbHeightField->setLabel($language->text('ynmediaimporter', 'album_thumb_height'));
        $this->addElement($albumThumbHeightField);
        
    //4. Album Thumbnail Wrapper Height - Enter a number between 150 and 300. Default: 200
		$albumWrapHeightField = new TextField('albumWrapHeight');
        $albumWrapHeightField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(150);
        $sValidator->setMaxValue(300);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'album_wrap_height_error'));
        $albumWrapHeightField->addValidator($sValidator);
        $albumWrapHeightField->setLabel($language->text('ynmediaimporter', 'album_wrap_height'));
        $this->addElement($albumWrapHeightField);
        
    //5. Album Thumbnail Wrapper Margin - Enter a number between 5 and 20. Default: 10
		$albumWrapMarginField = new TextField('albumWrapMargin');
        $albumWrapMarginField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(5);
        $sValidator->setMaxValue(20);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'album_wrap_margin_error'));
        $albumWrapMarginField->addValidator($sValidator);
        $albumWrapMarginField->setLabel($language->text('ynmediaimporter', 'album_wrap_margin'));
        $this->addElement($albumWrapMarginField);
        
    //6. Photo Max Thumbnail Width - Enter a number between 100 and 200. Default: 165
		$photoThumbWidthField = new TextField('photoThumbWidth');
        $photoThumbWidthField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(100);
        $sValidator->setMaxValue(200);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'photo_thumb_width_error'));
        $photoThumbWidthField->addValidator($sValidator);
        $photoThumbWidthField->setLabel($language->text('ynmediaimporter', 'photo_thumb_width'));
        $this->addElement($photoThumbWidthField);

    //7. Photo Max Thumbnail Height - Enter a number between 100 and 200. Default: 116
		$photoThumbHeightField = new TextField('photoThumbHeight');
        $photoThumbHeightField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(100);
        $sValidator->setMaxValue(200);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'photo_thumb_height_error'));
        $photoThumbHeightField->addValidator($sValidator);
        $photoThumbHeightField->setLabel($language->text('ynmediaimporter', 'photo_thumb_height'));
        $this->addElement($photoThumbHeightField);

    //8. Photo Thumbnail Wrapper Height - Enter a number between 150 and 300. Default: 160
        $photoWrapHeightField = new TextField('photoWrapHeight');
        $photoWrapHeightField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(150);
        $sValidator->setMaxValue(300);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'photo_wrap_height_error'));
        $photoWrapHeightField->addValidator($sValidator);
        $photoWrapHeightField->setLabel($language->text('ynmediaimporter', 'photo_wrap_height'));
        $this->addElement($photoWrapHeightField);
        
    //9. Photo Thumbnail Wrapper Margin - Enter a number between 5 and 20. Default: 10
		$photoWrapMarginField = new TextField('photoWrapMargin');
        $photoWrapMarginField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(5);
        $sValidator->setMaxValue(20);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'photo_wrap_margin_error'));
        $photoWrapMarginField->addValidator($sValidator);
        $photoWrapMarginField->setLabel($language->text('ynmediaimporter', 'photo_wrap_margin'));
        $this->addElement($photoWrapMarginField);
        
    //10. Number Photos Per Queue - How many photos will be imported per each queue? (Enter a number between 10 and 100), suggest 20
		$numberPhotoField = new TextField('numberPhoto');
        $numberPhotoField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(10);
        $sValidator->setMaxValue(100);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'number_photo_error'));
        $numberPhotoField->addValidator($sValidator);
        $numberPhotoField->setLabel($language->text('ynmediaimporter', 'number_photo'));
        $this->addElement($numberPhotoField);		
	
    //11. Number Queue Per Cron - How many queue will be process per cron? (Enter a number between 10 and 200), suggest 20
		$numberQueueField = new TextField('numberQueue');
        $numberQueueField->setRequired(true);
        $sValidator = new IntValidator();
        $sValidator->setMinValue(10);
        $sValidator->setMaxValue(200);
        $sValidator->setErrorMessage($language->text('ynmediaimporter', 'number_queue_error'));
        $numberQueueField->addValidator($sValidator);
        $numberQueueField->setLabel($language->text('ynmediaimporter', 'number_queue'));
        $this->addElement($numberQueueField);		

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('base', 'edit_button'));
        $this->addElement($submit);
    }
    
    
    /**
     * Updates Media Importer plugin configuration
     *
     * @return boolean
     */
    public function process( )
    {
        $values = $this->getValues();

        $config = OW::getConfig();

        $config->saveConfig('ynmediaimporter', 'page', $values['page']);
        
		$config->saveConfig('ynmediaimporter', 'album_thumb_width', $values['albumThumbWidth']);
		$config->saveConfig('ynmediaimporter', 'album_thumb_height', $values['albumThumbHeight']);
		$config->saveConfig('ynmediaimporter', 'album_wrap_height', $values['albumWrapHeight']);
		$config->saveConfig('ynmediaimporter', 'album_wrap_margin', $values['albumWrapMargin']);
        
        $config->saveConfig('ynmediaimporter', 'photo_thumb_width', $values['photoThumbWidth']);
        $config->saveConfig('ynmediaimporter', 'photo_thumb_height', $values['photoThumbHeight']);
        $config->saveConfig('ynmediaimporter', 'photo_wrap_height', $values['photoWrapHeight']);
        $config->saveConfig('ynmediaimporter', 'photo_wrap_margin', $values['photoWrapMargin']);
        
        $config->saveConfig('ynmediaimporter', 'number_photo', $values['numberPhoto']);
        $config->saveConfig('ynmediaimporter', 'number_queue', $values['numberQueue']);
		        
        return array('result' => true);
    }
}

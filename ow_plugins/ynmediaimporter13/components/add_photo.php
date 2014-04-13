<?php

class YNMEDIAIMPORTER_CMP_AddPhoto extends OW_Component
{
	public function __construct(array $params )
	{
		parent::__construct();

		$addPhotoForm = new AddPhotoForm();
		if ( isset($params['json_data']) && $params['json_data'] != '' ){
			$params['json_data'] = str_replace("\"", "'", $params['json_data']);
			$addPhotoForm->getElement('json_data')->setValue($params['json_data']);
		}
			
		$this->addForm($addPhotoForm);
	}

	public static function getAccess() // If you redefine this method, you'll be able to manage the widget visibility
	{
		return self::ACCESS_ALL;
	}

	public function onBeforeRender() // The standard method of the component that is called before rendering
	{

	}
}


class AddPhotoForm extends Form
{
    public function __construct()
    {
        parent::__construct('addPhotoForm');

        $language = OW::getLanguage();

        // album suggest Field
        $albumField = new SuggestField('album');
        $albumField->setRequired(true);
        $albumField->setMinChars(1);

        $userId = OW::getUser()->getId();
        $responderUrl = OW::getRouter()->urlFor('PHOTO_CTRL_Upload', 'suggestAlbum', array('userId' => $userId));
        $albumField->setResponderUrl($responderUrl);
        $albumField->setLabel($language->text('photo', 'album'));
        $albumField->setId('ynmediaimporter_album_suggest');
        $this->addElement($albumField);
        
        // json hidden field
        $dataField = new HiddenField('json_data');
        $dataField->setId("ynmediaimporter_json_data");
        $this->addElement($dataField);
        
        $submit = new Submit('submit');
        $submit->setValue("Continue");
        
        $this->addElement($submit);
        $this->setAction(OW::getRouter()->urlForRoute("ynmediaimporter.addphoto"));
    }
}
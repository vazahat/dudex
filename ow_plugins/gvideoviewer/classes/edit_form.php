<?php

class GVIDEOVIEWER_CLASS_EditForm extends Form
{
	/**
     * Class constructor
     */
    public function __construct( $clipId )
    {
        parent::__construct('videoEditForm');
        
        $this->setAjax(true);
        
        $this->setAction(OW::getRouter()->urlFor('GVIDEOVIEWER_CTRL_Index', 'ajaxUpdateVideo'));
        
        $language = OW::getLanguage();

        // clip id field
        $clipIdField = new HiddenField('id');
        $clipIdField->setRequired(true);
        $this->addElement($clipIdField);

        // title Field
        $titleField = new TextField('title');
        $titleField->addValidator(new StringValidator(1, 128));
        $titleField->setRequired(true);
        $this->addElement($titleField->setLabel($language->text('video', 'title')));

        // description Field
        $descField = new WysiwygTextarea('description');
        $descField->setId("video-desc-area");
        $this->addElement($descField->setLabel($language->text('video', 'description')));

        $code = new Textarea('code');
        $code->setRequired(true);
        $this->addElement($code->setLabel($language->text('video', 'code')));

        $entityTags = BOL_TagService::getInstance()->findEntityTags($clipId, 'video');

        if ( $entityTags )
        {
            $tags = array();
            foreach ( $entityTags as $entityTag )
            {
                $tags[] = $entityTag->label;
            }

            $tagsField = new TagsInputField('tags');
            $tagsField->setValue($tags);
        }
        else
        {
            $tagsField = new TagsInputField('tags');
        }

        $this->addElement($tagsField->setLabel($language->text('video', 'tags')));

        $submit = new Submit('edit');
        $submit->setValue($language->text('video', 'btn_edit'));
        $this->addElement($submit);
    }
}
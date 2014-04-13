<?php
class GVIDEOVIEWER_CMP_EditVideo extends OW_Component
{
    public function __construct( $clipId )
    {	
        parent::__construct();
        
        $videoEditForm = new GVIDEOVIEWER_CLASS_EditForm($clipId);
        $this->addForm($videoEditForm);
		
		$clip = VIDEO_BOL_ClipService::getInstance()->findClipById($clipId);
        $videoEditForm->getElement('id')->setValue($clip->id);
        $videoEditForm->getElement('title')->setValue($clip->title);
        $videoEditForm->getElement('description')->setValue($clip->description);
        $videoEditForm->getElement('code')->setValue($clip->code);
    }
}
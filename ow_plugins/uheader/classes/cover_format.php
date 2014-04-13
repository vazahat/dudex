<?php

class UHEADER_CLASS_CoverFormat extends NEWSFEED_CLASS_Format
{
    public function onBeforeRender() 
    {
        parent::onBeforeRender();
        
        //$coverId = $this->vars["coverId"];
        $userId = $this->vars["userId"];
        
        $cmp = new UHEADER_CMP_CoverItem($userId);
        $this->addComponent("cover", $cmp);
    }
}
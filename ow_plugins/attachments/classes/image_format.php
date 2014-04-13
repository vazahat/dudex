<?php

class ATTACHMENTS_CLASS_ImageFormat extends NEWSFEED_FORMAT_Video
{
    public function __construct($vars, $formatName = null) 
    {
        parent::__construct($vars, $formatName);
        
        $this->assign("uniqId", uniqid("attp-p-"));
        
        ATTACHMENTS_Plugin::getInstance()->addStatic();
    }
}
<?php

class ATTACHMENTS_CLASS_VideoFormat extends NEWSFEED_FORMAT_Video
{
    public function __construct($vars, $formatName = null) 
    {
        parent::__construct($vars, $formatName);
        
        $this->assign("uniqId", uniqid("attp-v-"));
        
        ATTACHMENTS_Plugin::getInstance()->addStatic();
    }
}
<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package attachments.components
 */
class ATTACHMENTS_CMP_AttVideoPreview extends OW_Component
{
    public function __construct( $oembed )
    {
        parent::__construct();

        $oembed["html"] = ATTACHMENTS_CLASS_VideoTools::addCodeParam($oembed["html"]);
        
        if ( !empty($oembed["thumbnail_url"]) )
        {
            $oembed["html"] = ATTACHMENTS_CLASS_VideoTools::addAutoPlay($oembed["html"]);
        }
        
        $this->assign('oembed', $oembed);
    }
}

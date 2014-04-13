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
class ATTACHMENTS_CMP_AttLinkPreview extends OW_Component
{
    protected $oembed = array(), $uniqId;

    public function __construct( $oembed )
    {
        parent::__construct();

        $this->uniqId = uniqid('attachments');
        $this->assign('uniqId', $this->uniqId);

        $this->oembed = $oembed;
    }

    public function initJs( $delegate )
    {
        $js = UTIL_JsGenerator::newInstance();
        $js->newObject(array('ATTP.CORE.ObjectRegistry', $this->uniqId), 'ATTP.Attachment', array($this->uniqId, $delegate));

        ATTACHMENTS_Plugin::getInstance()->addJs($js);
        
        return $this->uniqId;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $this->assign('data', $this->oembed);
    }
}
